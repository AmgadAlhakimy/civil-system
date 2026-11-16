<?php

namespace App\Services\Requests;

use App\Models\Citizen;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Log;

abstract class BaseServiceHandler implements ServiceRequestHandler
{
    protected function findOrCreateCitizen(ServiceRequest $request): ?Citizen
    {
        $data = $request->data ?? [];

        if (!empty($data['national_id'])) {
            $citizen = Citizen::whereHas('identityCards', function ($q) use ($data) {
                $q->where('national_id', $data['national_id']);
            })->first();

            if ($citizen) return $citizen;
        }

        if (!empty($data['phone'])) {
            $citizen = Citizen::where('phone', $data['phone'])->first();
            if ($citizen) return $citizen;
        }

        return $this->createCitizen($data);
    }

    protected function findExistingCitizen(ServiceRequest $request): ?Citizen
    {
        $data = $request->data ?? [];

        if (!empty($data['national_id'])) {
            $citizen = Citizen::whereHas('identityCards', function ($q) use ($data) {
                $q->where('national_id', $data['national_id']);
            })->first();

            if ($citizen) return $citizen;
        }

        if (!empty($data['phone'])) {
            return Citizen::where('phone', $data['phone'])->first();
        }

        return null;
    }

    protected function createCitizen(array $data): ?Citizen
    {
        try {
            // ── جمع الأجزاء من المفاتيح المعروفة ──
            $firstName = $data['firstName']
                ?? $data['newbornFirstName']
                ?? $data['deceasedFirstName']
                ?? null;

            $fatherName = $data['fatherName']
                ?? $data['newbornFatherName']
                ?? $data['deceasedFatherName']
                ?? null;

            $middleName = $data['grandfatherName']
                ?? $data['newbornGrandfatherName']
                ?? $data['deceasedGrandfatherName']
                ?? null;

            $lastName = $data['familyName']
                ?? $data['newbornFamilyName']
                ?? $data['deceasedFamilyName']
                ?? null;

            // ── fallback: قسّم fatherFullName إذا لم يوجد firstName ──
            if (!$firstName && !empty($data['fatherFullName'])) {
                $parts = preg_split('/\s+/', trim($data['fatherFullName']));
                $parts = array_values(array_filter($parts, fn ($p) => trim($p) !== ''));

                $firstName  = $firstName  ?? ($parts[0] ?? null);
                $fatherName = $fatherName ?? ($parts[1] ?? null);
                $middleName = $middleName ?? ($parts[2] ?? null);
                $lastName   = $lastName   ?? ($parts[3] ?? (count($parts) > 1 ? end($parts) : null));
            }

            // ── fallback: fatherFullName موجود لكن firstName موجود (نادراً) ──
            if (!$firstName && !empty($data['fatherFullName'])) {
                $firstName = $data['fatherFullName'];
            }

            if (!$firstName) {
                Log::warning('No first name found in data', $data);
                return null;
            }

            return Citizen::create([
                'first_name'  => $this->truncate($firstName, 50),
                'father_name' => $this->truncate($fatherName, 50),
                'middle_name' => $this->truncate($middleName, 50),
                'last_name'   => $this->truncate($lastName, 50),
                'mother_name' => $this->truncate(
                    $data['motherFullName']
                        ?? $data['motherName']
                        ?? $data['newbornMotherName']
                        ?? null,
                    50
                ),
                'birth_date'  => $this->parseDate($data['birthDate'] ?? null),
                'birth_place' => $this->truncate(
                    $data['birthPlace']
                        ?? $data['birthArea']
                        ?? $data['birthGovernorateName']
                        ?? null,
                    50
                ),
                'gender'         => $this->parseGender($data['gender'] ?? null),
                'marital_status' => $this->parseMaritalStatus($data['maritalStatus'] ?? null),
                'address'        => $this->buildAddress($data) ?? 'غير محدد',
                'phone'          => $this->truncate(
                    $data['phone']
                        ?? $data['fatherPhone']
                        ?? $data['applicantPhone']
                        ?? null,
                    20
                ),
                'email'      => $data['email'] ?? null,
                'occupation' => $data['job'] ?? $data['occupation'] ?? null,
                'is_active'  => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to create citizen', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'data'  => $data,
            ]);
            return null;
        }
    }

    protected function updateCitizen(Citizen $citizen, array $data): bool
    {
        $fields = [
            'first_name'  => ['firstName', 'newbornFirstName', 'deceasedFirstName'],
            'father_name' => ['fatherName', 'newbornFatherName', 'deceasedFatherName'],
            'middle_name' => ['grandfatherName', 'newbornGrandfatherName', 'deceasedGrandfatherName'],
            'last_name'   => ['familyName', 'newbornFamilyName', 'deceasedFamilyName'],
            'mother_name' => ['motherFullName', 'motherName'],
            'phone'       => ['phone', 'fatherPhone'],
        ];

        $updates = [];
        foreach ($fields as $column => $keys) {
            foreach ($keys as $key) {
                if (!empty($data[$key])) {
                    $updates[$column] = $data[$key];
                    break;
                }
            }
        }

        $address = $this->buildAddress($data);
        if ($address) {
            $updates['address'] = $address;
        }

        if (empty($updates)) return false;

        try {
            $citizen->update($updates);
            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to update citizen', [
                'error' => $e->getMessage(),
                'citizen_id' => $citizen->id,
            ]);
            return false;
        }
    }

    protected function buildAddress(array $data): ?string
    {
        $parts = array_filter([
            $data['residenceGovernorateName'] ?? $data['birthGovernorateName'] ?? null,
            $data['residenceDistrictName']    ?? $data['birthDistrictName']    ?? null,
            $data['area']                     ?? $data['residenceArea']        ?? $data['birthArea'] ?? null,
            $data['village']                  ?? $data['residenceVillage']     ?? $data['birthVillage'] ?? null,
            $data['addressDetails'] ?? null,
        ]);

        return empty($parts) ? null : implode(' - ', $parts);
    }

    // ═══════════════════════════════════════════════════════════
    // Helpers — protected (للسماح باستخدامها من Subclasses)
    // ═══════════════════════════════════════════════════════════

    protected function truncate(?string $value, int $length): string
    {
        $value = trim($value ?? '');
        if ($value === '') return 'غير محدد';
        return mb_substr($value, 0, $length);
    }

    protected function parseDate($value): string
    {
        if (empty($value)) {
            return now()->subYears(30)->toDateString();
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return $value->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return now()->subYears(30)->toDateString();
        }
    }

    protected function parseGender($value): string
    {
        $normalized = mb_strtolower(trim((string) $value));

        return match ($normalized) {
            'أنثى', 'female', 'f', 'أنثي' => 'female',
            'ذكر', 'male', 'm' => 'male',
            default => 'male',
        };
    }

    protected function parseMaritalStatus($value): string
    {
        $normalized = mb_strtolower(trim((string) $value));

        return match ($normalized) {
            'متزوج', 'married'   => 'married',
            'مطلق', 'divorced'   => 'divorced',
            'أرمل', 'widowed'    => 'widowed',
            'أعزب', 'single'     => 'single',
            default => 'single',
        };
    }
}