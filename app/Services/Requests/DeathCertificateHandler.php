<?php

namespace App\Services\Requests;

use App\Models\Citizen;
use App\Models\DeathCertificate;
use App\Models\ServiceRequest;
use App\Models\User;

class DeathCertificateHandler extends BaseServiceHandler
{
    public function getServiceTable(): string
    {
        return 'death_certificates';
    }








public function approve(ServiceRequest $request, User $reviewer): array
{
    \DB::beginTransaction();

    try {
        $data = $request->data ?? [];

        // 1. ابحث عن المتوفى أو أنشئه
        $deceased = $this->findExistingCitizen($request);

        if (!$deceased) {
            $deceased = Citizen::create([
                'first_name' => $this->truncate($data['deceasedFirstName'] ?? 'غير محدد', 50),
                'father_name' => $this->truncate($data['deceasedFatherName'] ?? 'غير محدد', 50),
                'middle_name' => $this->truncate($data['deceasedGrandfatherName'] ?? 'غير محدد', 50),
                'last_name' => $this->truncate($data['deceasedFamilyName'] ?? 'غير محدد', 50),
                'mother_name' => $this->truncate($data['motherFullName'] ?? 'غير محدد', 50),
                'birth_date' => $this->parseDate($data['birthDate'] ?? null),
                'birth_place' => $this->truncate($data['birthArea'] ?? 'غير محدد', 50),
                'gender' => $this->parseGender($data['gender'] ?? null),
                'marital_status' => 'single',
                'address' => $this->buildAddress($data) ?? 'غير محدد',
                'phone' => $this->truncate($data['phone'] ?? '000000000', 20),
                'is_active' => false,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'verified_by' => $reviewer->id,
            ]);
        }

        // 2. توليد رقم الشهادة
        $certificateNumber = $this->generateCertificateNumber();

        // 3. ✅ place_of_death — قيمة افتراضية مضمونة
        $placeOfDeath = $data['deathArea']
            ?? $data['deathPlace']
            ?? $data['deathVillage']
            ?? null;

        if (empty($placeOfDeath)) {
            $placeOfDeath = 'غير محدد';
        } else {
            $placeOfDeath = mb_substr(trim((string) $placeOfDeath), 0, 50);
        }

        // 4. إنشاء شهادة الوفاة
        $certificate = DeathCertificate::create([
            'deceased_id' => $deceased->id,
            'death_date' => $this->parseDate($data['deathDate'] ?? null),
            'cause_of_death' => $data['deathCase'] ?? 'غير محدد',
            'place_of_death' => $placeOfDeath,  // ← مضمونة
            'certificate_number' => $certificateNumber,
            'issue_date' => now(),
            'status' => 'active',
            'issued_by' => $reviewer->id,
            'approved_by' => $reviewer->id,
            'approved_at' => now(),
        ]);

        // 5. تعطيل المواطن
        $deceased->update(['is_active' => false]);

        $request->update(['citizen_id' => $deceased->id]);

        \DB::commit();

        return [
            'success' => true,
            'citizen_id' => $deceased->id,
            'service_record_id' => $certificate->id,
        ];
    } catch (\Throwable $e) {
        \DB::rollBack();
        \Log::error('DeathCertificateHandler failed', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return ['success' => false, 'error' => $e->getMessage()];
    }
}


    /**
     * توليد رقم شهادة وفاة فريد
     */
    private function generateCertificateNumber(): string
    {
        do {
            $number = 'DC' . date('Ymd') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (DeathCertificate::where('certificate_number', $number)->exists());

        return $number;
    }
}