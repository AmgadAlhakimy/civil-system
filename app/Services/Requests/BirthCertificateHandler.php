<?php

namespace App\Services\Requests;

use App\Models\BirthCertificate;
use App\Models\Citizen;
use App\Models\ServiceRequest;
use App\Models\User;

class BirthCertificateHandler extends BaseServiceHandler
{
    public function getServiceTable(): string
    {
        return 'birth_certificates';
    }












public function approve(ServiceRequest $request, User $reviewer): array
{
    \DB::beginTransaction();

    try {
        $data = $request->data ?? [];

        // 1. إنشاء المولود
        $newborn = Citizen::create([
            'first_name' => $this->truncate($data['newbornFirstName'] ?? 'غير محدد', 50),
            'father_name' => $this->truncate($data['newbornFatherName'] ?? 'غير محدد', 50),
            'middle_name' => $this->truncate($data['newbornGrandfatherName'] ?? 'غير محدد', 50),
            'last_name' => $this->truncate($data['newbornFamilyName'] ?? 'غير محدد', 50),
            'mother_name' => $this->truncate($data['motherFullName'] ?? 'غير محدد', 50),
            'birth_date' => $this->parseDate($data['birthDate'] ?? null),
            'birth_place' => $this->truncate($data['birthArea'] ?? 'غير محدد', 50),
            'gender' => $this->parseGender($data['gender'] ?? null),
            'marital_status' => 'single',
            'address' => $this->buildAddress($data) ?? 'غير محدد',
            'phone' => $this->truncate($data['fatherPhone'] ?? '000000000', 20),
            'is_active' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $reviewer->id,
        ]);

        // 2. البحث عن الأب
        $father = null;
        if (!empty($data['fatherNationalId'])) {
            $father = Citizen::whereHas('identityCards', function ($q) use ($data) {
                $q->where('national_id', $data['fatherNationalId']);
            })->first();
        }

        // 3. البحث عن الأم
        $mother = null;
        if (!empty($data['motherNationalId'])) {
            $mother = Citizen::whereHas('identityCards', function ($q) use ($data) {
                $q->where('national_id', $data['motherNationalId']);
            })->first();
        }

        // 4. توليد رقم الشهادة
        $certificateNumber = $this->generateCertificateNumber();

        // 5. ✅ mother_id — قيمة افتراضية مضمونة
        $motherId = $mother?->id ?? $father?->id ?? $newborn->id;

        // 6. إنشاء شهادة الميلاد
        $certificate = BirthCertificate::create([
            'child_id' => $newborn->id,
            'father_id' => $father?->id,
            'mother_id' => $motherId,  // ← مضمونة
            'certificate_number' => $certificateNumber,
            'issue_date' => now(),
            'status' => 'active',
            'issued_by' => $reviewer->id,
            'approved_by' => $reviewer->id,
            'approved_at' => now(),
        ]);

        $request->update(['citizen_id' => $newborn->id]);

        \DB::commit();

        return [
            'success' => true,
            'citizen_id' => $newborn->id,
            'service_record_id' => $certificate->id,
        ];
    } catch (\Throwable $e) {
        \DB::rollBack();
        \Log::error('BirthCertificateHandler failed', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return ['success' => false, 'error' => $e->getMessage()];
    }
}

    /**
     * توليد رقم شهادة ميلاد فريد
     */
    private function generateCertificateNumber(): string
    {
        do {
            $number = 'BC' . date('Ymd') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (BirthCertificate::where('certificate_number', $number)->exists());

        return $number;
    }
}