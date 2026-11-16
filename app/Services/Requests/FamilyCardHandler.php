<?php

namespace App\Services\Requests;

use App\Models\Citizen;
use App\Models\FamilyCard;
use App\Models\FamilyMember;
use App\Models\ServiceRequest;
use App\Models\User;

class FamilyCardHandler extends BaseServiceHandler
{
    public function getServiceTable(): string
    {
        return 'family_cards';
    }

    public function approve(ServiceRequest $request, User $reviewer): array
    {
        \DB::beginTransaction();

        try {
            $data = $request->data ?? [];

            // 1. رب الأسرة
            $head = null;

            if (!empty($data['fatherNationalId'])) {
                $head = Citizen::whereHas('identityCards', function ($q) use ($data) {
                    $q->where('national_id', $data['fatherNationalId']);
                })->first();
            }

            if (!$head) {
                $head = $this->createCitizen([
                    'fatherFullName' => $data['fatherFullName'] ?? null,
                    'phone'          => $data['fatherPhone']    ?? null,
                    'area'           => $data['area']           ?? null,
                    'village'        => $data['village']        ?? null,
                ]);

                if (!$head) {
                    \DB::rollBack();
                    return ['success' => false, 'error' => 'رب الأسرة غير موجود ولا يمكن إنشاؤه'];
                }
            }

            // 2. البطاقة العائلية
            $card = FamilyCard::create([
                'head_id'     => $head->id,
                'branch_id'   => $request->branch_id,
                'issue_date'  => now(),
                'expiry_date' => now()->addYears(10),
                'status'      => 'active',
                'issued_by'   => $reviewer->id,
                'approved_by' => $reviewer->id,
                'approved_at' => now(),
            ]);

            // 3. الأعضاء
            $members = $data['familyMembers'] ?? [];
            $membersCount = 0;

            foreach ($members as $memberData) {
                if (empty($memberData['fullName'])) continue;

                $memberCitizen = Citizen::where('first_name', $memberData['fullName'])
                    ->where('birth_date', $memberData['birthDate'] ?? null)
                    ->first();

                if (!$memberCitizen) {
                    $memberCitizen = $this->createCitizen([
                        'firstName' => $memberData['fullName'],
                        'birthDate' => $memberData['birthDate'] ?? null,
                        'gender'    => $memberData['gender']    ?? 'male',
                    ]);

                    if (!$memberCitizen) {
                        \Log::warning('FamilyCard: failed to create member, skipping', [
                            'member' => $memberData,
                        ]);
                        continue;
                    }
                }

                FamilyMember::create([
                    'family_card_id' => $card->id,
                    'citizen_id'     => $memberCitizen->id,
                    'relationship'   => $this->parseRelationship($memberData['relation'] ?? null),
                    'is_active'      => true,
                    'added_by'       => $reviewer->id,
                ]);

                $membersCount++;
            }

            $request->update(['citizen_id' => $head->id]);

            \DB::commit();

            return [
                'success'           => true,
                'citizen_id'        => $head->id,
                'service_record_id' => $card->id,
                'members_added'     => $membersCount,
            ];
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('FamilyCardHandler failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * تحويل العلاقة العربية/الإنجليزية إلى قيم ENUM:
     * spouse | child | father | mother | brother | sister | other
     */
    private function parseRelationship(?string $relation): string
    {
        $normalized = trim((string) $relation);

        return match ($normalized) {
            'ابن', 'ابنة', 'بنت', 'son', 'daughter', 'child' => 'child',
            'زوج', 'زوجة', 'spouse', 'husband', 'wife'       => 'spouse',
            'أب', 'father'                                    => 'father',
            'أم', 'mother'                                    => 'mother',
            'أخ', 'brother'                                   => 'brother',
            'أخت', 'sister'                                   => 'sister',
            default                                            => 'other',
        };
    }
}