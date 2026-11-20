<?php

namespace App\Services;

use App\Models\DocumentVerification;
use App\Models\User;
use App\Models\ServiceRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DocumentVerificationService
{
    public function verify(
        User $user,
        UploadedFile $file,
        string $documentType,
        ServiceRequest $serviceRequest,
        ?string $documentReference = null,
        ?User $performedBy = null
    ): array {
        if ((string) $serviceRequest->user_id !== (string) $user->id) {
            throw new RuntimeException('الطلب لا يتبع المستخدم الحالي.');
        }

        $realPath = $file->getRealPath();

        if (
            ! $realPath
            || ! is_file($realPath)
            || ! is_readable($realPath)
        ) {
            throw new RuntimeException('تعذر الوصول إلى ملف المستند.');
        }

        $sha256 = hash_file('sha256', $realPath);

        if ($sha256 === false) {
            throw new RuntimeException('تعذر حساب البصمة الرقمية للمستند.');
        }

        $imageInfo = @getimagesize($realPath);

        $width = $imageInfo !== false
            ? (int) $imageInfo[0]
            : null;

        $height = $imageInfo !== false
            ? (int) $imageInfo[1]
            : null;

        $isReadable = $imageInfo !== false;

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($realPath) ?: 'application/octet-stream';

        $supportedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        $isSupportedType = in_array($mimeType, $supportedMimeTypes, true);

        /*
         * لا تعتبر إعادة فحص المرفق نفسه نسخة مكررة.
         * يستمر كشف استخدام الصورة نفسها في مرفق آخر أو طلب آخر للمستخدم.
         */
        $duplicateQuery = DocumentVerification::query()
            ->where('user_id', $user->id)
            ->where('sha256', $sha256);

        if ($documentReference !== null) {
            $duplicateQuery->where(function ($query) use ($serviceRequest, $documentReference) {
                $query->whereNull('service_request_id')
                    ->orWhere('service_request_id', '!=', $serviceRequest->id)
                    ->orWhere('document_reference', '!=', $documentReference);
            });
        }

        $isDuplicate = $duplicateQuery->exists();

        $hasAcceptableDimensions = $isReadable
            && $width >= 300
            && $height >= 300;

        // تقييم أولي لجودة الصورة.
        $quality = $this->assessImageQuality(
            $realPath,
            $width,
            $height,
            $isReadable
        );

        // استخراج النصوص والحقول المحتملة.
        $ocrResult = app(DocumentOcrService::class)->extract($realPath);
        $ocrText = $ocrResult['text'] ?? '';

        // مطابقة الوجه مع سجل المواطن المحلي عند استخراج رقم الهوية.
        // هذه مطابقة أولية ولا تمثل تحققًا رسميًا من الهوية.
        $nationalId = data_get($ocrResult, 'extracted_fields.national_id');

        $faceMatchResult = app(DocumentFaceMatchService::class)
            ->verify($realPath, $nationalId);

        // مقارنة الحقول المستخرجة مع المستندات السابقة للمستخدم نفسه.
        $crossMatchResult = app(DocumentCrossMatchService::class)
            ->compare($user, $documentType, $ocrResult);

        // قراءة محتوى QR إن كان قارئ ZBar مثبتًا.
        $qrResult = app(DocumentQrService::class)->verify($realPath);

        // فحص MRZ مخصص لجوازات السفر.
        $isPassport = str_contains(strtolower($documentType), 'passport');

        if ($isPassport && $ocrText !== '') {
            $mrzResult = app(DocumentMrzService::class)->verify($ocrText);
        } elseif ($isPassport) {
            $mrzResult = [
                'status' => 'warning',
                'message' => 'تعذر استخراج نص MRZ من صورة الجواز.',
                'structure_valid' => false,
                'official_verification' => false,
            ];
        } else {
            $mrzResult = [
                'status' => 'not_applicable',
                'message' => 'فحص MRZ مخصص لجوازات السفر.',
                'structure_valid' => false,
                'official_verification' => false,
            ];
        }

        // تحليل مؤشرات تعديل الصورة، وليس إثبات التزوير.
        $forgeryResult = app(DocumentForgeryService::class)->analyze($realPath);

        $fileIntegrityPassed = $isReadable && $isSupportedType;

        $checks = [
            'image_readability' => [
                'status' => $isReadable ? 'passed' : 'failed',
                'message' => $isReadable
                    ? 'تمت قراءة الصورة بنجاح.'
                    : 'تعذر قراءة بيانات الصورة.',
            ],

            'file_integrity' => [
                'status' => $fileIntegrityPassed ? 'passed' : 'warning',
                'message' => $fileIntegrityPassed
                    ? 'نوع الملف مدعوم ويمكن قراءته.'
                    : 'نوع الملف غير مدعوم أو تعذرت قراءة الصورة.',
                'mime_type' => $mimeType,
            ],

            'image_dimensions' => [
                'status' => $hasAcceptableDimensions ? 'passed' : 'warning',
                'message' => $hasAcceptableDimensions
                    ? 'أبعاد الصورة مناسبة للفحص الأولي.'
                    : 'أبعاد الصورة منخفضة أو غير معروفة.',
                'width' => $width,
                'height' => $height,
            ],

            'image_quality' => $quality,

            'duplicate_detection' => [
                'status' => $isDuplicate ? 'warning' : 'passed',
                'message' => $isDuplicate
                    ? 'تم العثور على ملف مطابق سبق إرساله من حسابك.'
                    : 'لم يتم العثور على نسخة مطابقة في سجلات حسابك.',
            ],

            'ocr' => $ocrResult,
            'qr_verification' => $qrResult,
            'mrz_verification' => $mrzResult,
            'forgery_detection' => $forgeryResult,
            'face_matching' => $faceMatchResult,
            'cross_document_matching' => $crossMatchResult,

            'security_features' => [
                'status' => 'unavailable',
                'message' => 'فحص العلامات الأمنية غير متاح بعد.',
            ],

            'official_database' => [
                'status' => 'unavailable',
                'message' => 'لا توجد قاعدة بيانات رسمية مربوطة.',
            ],

            'ai_risk_assessment' => [
                'status' => 'unavailable',
                'message' => 'سيتم احتساب تقييم المخاطر بعد اكتمال نتائج الفحوص.',
            ],
        ];

        // هذا تقييم قائم على قواعد وليس نموذج ذكاء اصطناعي مدربًا.
        $riskAssessment = app(DocumentRiskAssessmentService::class)
            ->assess($checks);

        $checks['ai_risk_assessment'] = $riskAssessment;

        $requiresReview = (
            ! $isReadable
            || ! $fileIntegrityPassed
            || $isDuplicate
            || ! $hasAcceptableDimensions
            || $quality['status'] !== 'passed'
            || in_array(
                $riskAssessment['risk_level'] ?? 'undetermined',
                ['medium', 'high'],
                true
            )
        );

        $status = $requiresReview
            ? 'review_required'
            : 'preliminary_checks_passed';

        $verificationData = [
            'user_id' => $user->id,
            'service_request_id' => $serviceRequest->id,
            'document_reference' => $documentReference,
            'document_type' => $documentType,
            'sha256' => $sha256,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'image_width' => $width,
            'image_height' => $height,
            'status' => $status,
            'checks' => $checks,
        ];

        /*
         * للمرفقات المرتبطة بطلب: حدّث أحدث سجل لنفس المرفق بدل إنشاء
         * سجل جديد عند كل إعادة فحص.
         *
         * لا نحذف السجلات التاريخية القديمة التي أنشأتها الفحوص السابقة.
         */
        if ($documentReference !== null) {
            $verification = DocumentVerification::query()
                ->where('user_id', $user->id)
                ->where('service_request_id', $serviceRequest->id)
                ->where('document_reference', $documentReference)
                ->orderByDesc('created_at')
                ->first();

            if (! $verification) {
                $verification = new DocumentVerification();
            }

            $verification->fill($verificationData);
            $verification->save();
        } else {
            // حافظ على سلوك الفحوص التي لا تملك document_reference.
            $verification = DocumentVerification::create($verificationData);
        }

        // تسجيل عملية الفحص في سجل التدقيق.
        // لا نسجل نص OCR أو بيانات الهوية المستخرجة حفاظًا على الخصوصية.
        activity('document_verifications')
            ->causedBy($performedBy ?? $user)
            ->performedOn($verification)
            ->event('document_verification')
            ->withProperties([
                'verification_id' => $verification->id,
                'service_request_id' => $serviceRequest->id,
                'request_number' => $serviceRequest->request_number,
                'document_reference' => $documentReference,
                'document_type' => $documentType,
                'verification_status' => $status,
                'duplicate_detected' => $isDuplicate,
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
                'image_width' => $width,
                'image_height' => $height,
                'risk_assessment' => [
                    'method' => $riskAssessment['method'] ?? 'rule_based',
                    'score' => $riskAssessment['risk_score'] ?? null,
                    'level' => $riskAssessment['risk_level'] ?? 'undetermined',
                    'evaluated_checks' => $riskAssessment['evaluated_checks'] ?? [],
                ],
                'official_verification' => false,
                'forgery_confirmed' => false,
            ])
            ->log('تم تنفيذ الفحص الأولي للمستند.');

        return [
            'verification_id' => $verification->id,
            'service_request_id' => $serviceRequest->id,
            'document_reference' => $documentReference,
            'document_type' => $documentType,
            'status' => $status,
            'sha256' => $sha256,
            'file_size' => $file->getSize(),
            'mime_type' => $mimeType,

            'image_dimensions' => [
                'width' => $width,
                'height' => $height,
            ],

            'checks' => $checks,
            'risk_assessment' => $riskAssessment,
            'official_verification' => false,
            'message' => 'نتيجة الفحص أولية ولا تثبت أصالة المستند.',
        ];
    }

    /**
     * فحص مرفق محفوظ مسبقًا ضمن طلب الخدمة.
     * يستخدمه الموظف من لوحة Filament حتى لا يحتاج المواطن إلى إعادة رفع الصورة.
     */
    public function verifyStoredDocument(
        ServiceRequest $serviceRequest,
        string $documentReference,
        ?User $performedBy = null
    ): array {
        $documents = $serviceRequest->documents;

        if (! is_array($documents) || ! array_key_exists($documentReference, $documents)) {
            throw new RuntimeException('المرفق المحدد غير موجود ضمن مستندات الطلب.');
        }

        $attachment = $documents[$documentReference];

        if (! is_array($attachment)) {
            throw new RuntimeException('بيانات المرفق غير صالحة.');
        }

        $relativePath = $attachment['path'] ?? null;

        if (! is_string($relativePath) || trim($relativePath) === '') {
            throw new RuntimeException('مسار الملف غير موجود في بيانات المرفق.');
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($relativePath)) {
            throw new RuntimeException('ملف المرفق غير موجود في مساحة التخزين العامة.');
        }

        $absolutePath = $disk->path($relativePath);

        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            throw new RuntimeException('تعذر قراءة ملف المرفق من مساحة التخزين.');
        }

        $mimeType = $disk->mimeType($relativePath) ?: 'application/octet-stream';
        $documentType = (string) ($attachment['document_type'] ?? $documentReference);

        $uploadedFile = new UploadedFile(
            $absolutePath,
            basename($relativePath),
            $mimeType,
            null,
            true
        );

        $owner = $serviceRequest->user;

        if (! $owner) {
            throw new RuntimeException('تعذر تحديد صاحب الطلب لإسناد نتيجة الفحص.');
        }

        return $this->verify(
            $owner,
            $uploadedFile,
            $documentType,
            $serviceRequest,
            $documentReference,
            $performedBy
        );
    }

    private function assessImageQuality(
        string $path,
        ?int $width,
        ?int $height,
        bool $isReadable
    ): array {
        if (! $isReadable || ! $width || ! $height) {
            return [
                'status' => 'warning',
                'message' => 'تعذر تقييم جودة الصورة.',
                'brightness' => null,
                'contrast' => null,
            ];
        }

        if (($width * $height) > 40000000) {
            return [
                'status' => 'warning',
                'message' => 'أبعاد الصورة كبيرة جدًا للتقييم.',
                'brightness' => null,
                'contrast' => null,
            ];
        }

        if (! function_exists('imagecreatefromstring')) {
            return [
                'status' => 'unavailable',
                'message' => 'امتداد GD غير متوفر.',
                'brightness' => null,
                'contrast' => null,
            ];
        }

        $contents = @file_get_contents($path);

        if ($contents === false) {
            return [
                'status' => 'warning',
                'message' => 'تعذرت قراءة الصورة.',
                'brightness' => null,
                'contrast' => null,
            ];
        }

        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            return [
                'status' => 'warning',
                'message' => 'تعذر فك ترميز الصورة.',
                'brightness' => null,
                'contrast' => null,
            ];
        }

        $sample = imagecreatetruecolor(32, 32);

        if ($sample === false) {
            imagedestroy($source);

            return [
                'status' => 'warning',
                'message' => 'تعذر تجهيز الصورة.',
                'brightness' => null,
                'contrast' => null,
            ];
        }

        imagecopyresampled(
            $sample,
            $source,
            0,
            0,
            0,
            0,
            32,
            32,
            $width,
            $height
        );

        $values = [];

        for ($y = 0; $y < 32; $y++) {
            for ($x = 0; $x < 32; $x++) {
                $rgb = imagecolorat($sample, $x, $y);

                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;

                $values[] = (
                    0.299 * $red
                    + 0.587 * $green
                    + 0.114 * $blue
                );
            }
        }

        imagedestroy($source);
        imagedestroy($sample);

        $brightness = array_sum($values) / count($values);
        $variance = 0;

        foreach ($values as $value) {
            $variance += ($value - $brightness) ** 2;
        }

        $contrast = sqrt($variance / count($values));

        $passed = (
            $brightness >= 35
            && $brightness <= 225
            && $contrast >= 15
        );

        return [
            'status' => $passed ? 'passed' : 'warning',
            'message' => $passed
                ? 'الإضاءة والتباين مناسبان للفحص الأولي.'
                : 'قد تعاني الصورة من ضعف الإضاءة أو التباين.',
            'brightness' => round($brightness, 2),
            'contrast' => round($contrast, 2),
        ];
    }
}
