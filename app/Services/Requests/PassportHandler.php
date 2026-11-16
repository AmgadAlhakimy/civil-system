<?php

namespace App\Services\Requests;

use App\Models\Passport;
use App\Models\ServiceRequest;
use App\Models\User;

class PassportHandler extends BaseServiceHandler
{
    public function getServiceTable(): string
    {
        return 'passports';
    }

    public function approve(ServiceRequest $request, User $reviewer): array
    {
        \DB::beginTransaction();

        try {
            $data = $request->data ?? [];

            $citizen = $this->findExistingCitizen($request);

            if (!$citizen) {
                $citizen = $this->createCitizen($data);

                if (!$citizen) {
                    \DB::rollBack();
                    return ['success' => false, 'error' => 'المواطن غير موجود ولا يمكن إنشاؤه'];
                }
            }

            $passport = Passport::create([
                'citizen_id'           => $citizen->id,
                'passport_number'      => $this->generatePassportNumber(),
                'issuing_authority_ar' => 'مصلحة الأحوال المدنية',
                'issuing_authority_en' => 'Civil Registry Authority',
                'issue_date'           => now(),
                'expiry_date'          => now()->addYears(6),
                'type'                 => 'ordinary',   // ✅ ENUM: ordinary|diplomatic|official
                'status'               => 'active',
                'issued_by'            => $reviewer->id,
                'approved_by'          => $reviewer->id,
                'approved_at'          => now(),
            ]);

            $request->update(['citizen_id' => $citizen->id]);

            \DB::commit();

            return [
                'success'           => true,
                'citizen_id'        => $citizen->id,
                'service_record_id' => $passport->id,
            ];
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('PassportHandler failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function generatePassportNumber(): string
    {
        do {
            $number = 'P' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
        } while (Passport::where('passport_number', $number)->exists());

        return $number;
    }
}