<?php

namespace App\Services\Requests;

use App\Models\Passport;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PassportHandler extends BaseServiceHandler
{
    public function getServiceTable(): string
    {
        return 'passports';
    }

    /**
     * اعتماد طلب إصدار أو تجديد جواز السفر.
     */
    public function approve(ServiceRequest $request, User $reviewer): array
    {
        try {
            return DB::transaction(function () use ($request, $reviewer) {

                $data = $request->data ?? [];

                if (!is_array($data)) {
                    $data = (array) $data;
                }

                // تحديد نوع الخدمة
                $isRenewal = in_array(
                    strtolower(trim((string) $request->service_key)),
                    [
                        'renew_passport',
                        'renewal_passport',
                    ],
                    true
                );

                // البحث عن المواطن
                $citizen = $this->findExistingCitizen($request);

                // في الإصدار الجديد يمكن إنشاء المواطن عند عدم وجوده
                if (!$citizen && !$isRenewal) {
                    $citizen = $this->createCitizen($data);
                }

                if (!$citizen) {
                    return [
                        'success' => false,
                        'error' => $isRenewal
                            ? 'تعذر العثور على المواطن المرتبط بطلب التجديد'
                            : 'المواطن غير موجود ولا يمكن إنشاؤه',
                    ];
                }

                // ==========================================
                // تجديد جواز السفر
                // ==========================================

                if ($isRenewal) {

                    $oldPassportNumber = trim(
                        (string) ($data['oldPassportNumber'] ?? '')
                    );

                    if ($oldPassportNumber === '') {
                        return [
                            'success' => false,
                            'error' => 'رقم الجواز القديم مفقود',
                        ];
                    }

                    // البحث عن الجواز القديم
                    $passport = Passport::query()
                        ->where('passport_number', $oldPassportNumber)
                        ->lockForUpdate()
                        ->first();

                    if (!$passport) {
                        return [
                            'success' => false,
                            'error' => 'الجواز القديم غير موجود، تحقق من رقم الجواز',
                        ];
                    }

                    // التأكد أن الجواز يخص المواطن صاحب الطلب
                    if ((string) $passport->citizen_id !== (string) $citizen->id) {
                        return [
                            'success' => false,
                            'error' => 'الجواز القديم لا يخص المواطن صاحب الطلب',
                        ];
                    }

                    // منع تجديد الجوازات الملغاة أو المفقودة أو التالفة
                    if (in_array(
                        $passport->status,
                        ['cancelled', 'lost', 'damaged'],
                        true
                    )) {
                        return [
                            'success' => false,
                            'error' => 'لا يمكن تجديد جواز ملغي أو مفقود أو تالف',
                        ];
                    }

                    // تحديث الجواز نفسه مع الحفاظ على رقمه
                    $passport->update([
                        'passport_number' => $oldPassportNumber,
                        'issuing_authority_ar' => 'مصلحة الأحوال المدنية',
                        'issuing_authority_en' => 'Civil Registry Authority',
                        'issue_date' => now()->toDateString(),
                        'expiry_date' => now()->addYears(6)->toDateString(),
                        'type' => 'ordinary',
                        'status' => 'active',
                        'issued_by' => $reviewer->id,
                        'approved_by' => $reviewer->id,
                        'approved_at' => now(),
                    ]);
                }

                // ==========================================
                // إصدار جواز سفر جديد
                // ==========================================

                else {

                    $passport = Passport::create([
                        'citizen_id' => $citizen->id,
                        'passport_number' => $this->generatePassportNumber(),
                        'issuing_authority_ar' => 'مصلحة الأحوال المدنية',
                        'issuing_authority_en' => 'Civil Registry Authority',
                        'issue_date' => now()->toDateString(),
                        'expiry_date' => now()->addYears(6)->toDateString(),
                        'type' => 'ordinary',
                        'status' => 'active',
                        'issued_by' => $reviewer->id,
                        'approved_by' => $reviewer->id,
                        'approved_at' => now(),
                    ]);
                }

                // ربط الطلب بالمواطن
                $request->update([
                    'citizen_id' => $citizen->id,
                ]);

                return [
                    'success' => true,
                    'citizen_id' => $citizen->id,
                    'service_record_id' => $passport->id,
                ];
            });

        } catch (\Throwable $e) {

            Log::error('PassportHandler failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_id' => $request->id,
                'service_key' => $request->service_key,
            ]);

            return [
                'success' => false,
                'error' => 'حدث خطأ أثناء تنفيذ معاملة جواز السفر: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * توليد رقم جواز جديد عند الإصدار لأول مرة فقط.
     */
    private function generatePassportNumber(): string
    {
        do {
            $number = 'P' . strtoupper(
                substr(md5(uniqid('', true)), 0, 8)
            );

        } while (
            Passport::where('passport_number', $number)->exists()
        );

        return $number;
    }
}
