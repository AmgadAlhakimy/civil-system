<?php

namespace App\Services;

use App\Models\IdentityCard;

class DocumentFaceMatchService
{
    public function __construct(
        private readonly FaceEmbeddingService $embeddingService,
        private readonly FaceMatchService $matchService,
    ) {}

    /**
     * Compare the face on an uploaded identity document with the face
     * stored for the citizen linked to the OCR-read national ID.
     * No face image or embedding is persisted by this service.
     */
    public function verify(string $imagePath, ?string $nationalId): array
    {
        $nationalId = is_string($nationalId)
            ? preg_replace('/\D+/', '', $nationalId)
            : '';

        if ($nationalId === '') {
            return $this->result(
                'unavailable',
                'لم يتم استخراج رقم هوية واضح لربط مطابقة الوجه بسجل المواطن.'
            );
        }

        $identityCard = IdentityCard::query()
            ->with('citizen')
            ->where('national_id', $nationalId)
            ->where('status', 'active')
            ->first();

        $citizen = $identityCard?->citizen;

        if (! $citizen) {
            return $this->result(
                'unavailable',
                'لم يتم العثور على سجل هوية محلي مطابق للرقم المستخرج.'
            );
        }

        if (! is_string($citizen->face_data) || trim($citizen->face_data) === '') {
            return $this->result(
                'unavailable',
                'لا توجد بصمة وجه مسجلة لهذا المواطن في قاعدة بيانات التطبيق.'
            );
        }

        $imageContent = @file_get_contents($imagePath);

        if ($imageContent === false) {
            return $this->result(
                'unavailable',
                'تعذر قراءة صورة المستند لمطابقة الوجه.'
            );
        }

        $extraction = $this->embeddingService->extract(
            $imageContent,
            basename($imagePath) ?: 'identity-document.jpg'
        );

        if (! ($extraction['success'] ?? false)
            || ! is_array($extraction['embedding'] ?? null)) {
            $error = $extraction['error'] ?? 'unknown';

            return $this->result(
                $error === 'ai_service_unavailable' ? 'unavailable' : 'warning',
                $error === 'no_face_detected'
                    ? 'لم يتم اكتشاف وجه واضح في صورة المستند.'
                    : 'تعذر استخراج بصمة الوجه من صورة المستند.'
            );
        }

        $comparison = $this->matchService->compareWithCitizen(
            $extraction['embedding'],
            $citizen
        );

        $matched = (bool) ($comparison['matched'] ?? false);
        $confidence = (float) ($comparison['confidence'] ?? 0.0);

        return [
            'status' => $matched ? 'passed' : 'warning',
            'message' => $matched
                ? 'توجد مطابقة أولية بين الوجه في المستند والسجل المحلي.'
                : 'لم تتحقق المطابقة الأولية؛ يلزم التحقق البشري.',
            'matched' => $matched,
            'confidence' => round($confidence, 4),
            'threshold' => FaceMatchService::MIN_CONFIDENCE,
            'comparison_type' => 'one_to_one',
            'official_verification' => false,
            'human_review_required' => true,
        ];
    }

    private function result(string $status, string $message): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'matched' => false,
            'confidence' => null,
            'comparison_type' => 'one_to_one',
            'official_verification' => false,
            'human_review_required' => true,
        ];
    }
}
