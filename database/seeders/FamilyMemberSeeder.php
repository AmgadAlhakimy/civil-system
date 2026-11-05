<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\FamilyCard;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class FamilyMemberSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        $familyCards = FamilyCard::query()
            ->whereHas('head', fn ($query) => $query
                ->where('gender', 'male')
                ->where('verification_status', 'approved'))
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        $spouses = Citizen::query()
            ->where('gender', 'female')
            ->where('verification_status', 'approved')
            ->orderBy('created_at')
            ->get();

        $children = Citizen::query()
            ->where('verification_status', 'approved')
            ->whereNotNull('birth_date')
            ->orderBy('created_at')
            ->get();

        if (
            ! $user ||
            $familyCards->count() < 5 ||
            $spouses->count() < 5 ||
            $children->count() < 10
        ) {
            return;
        }

        $usedCitizenIds = $familyCards->pluck('head_id')->all();

        foreach ($familyCards as $cardIndex => $familyCard) {
            $spouse = $spouses->first(
                fn ($citizen) => ! in_array($citizen->id, $usedCitizenIds, true)
            );

            if (! $spouse) {
                continue;
            }

            $usedCitizenIds[] = $spouse->id;

            FamilyMember::create([
                'family_card_id' => $familyCard->id,
                'citizen_id' => $spouse->id,
                'relationship' => 'spouse',
                'is_active' => true,
                'notes' => null,
                'added_by' => $user->id,
            ]);

            $addedChildren = 0;

            foreach ($children as $child) {
                if (
                    in_array($child->id, $usedCitizenIds, true) ||
                    $child->id === $familyCard->head_id
                ) {
                    continue;
                }

                FamilyMember::create([
                    'family_card_id' => $familyCard->id,
                    'citizen_id' => $child->id,
                    'relationship' => 'child',
                    'is_active' => true,
                    'notes' => null,
                    'added_by' => $user->id,
                ]);

                $usedCitizenIds[] = $child->id;
                $addedChildren++;

                if ($addedChildren === 2) {
                    break;
                }
            }

            if ($addedChildren < 2) {
                return;
            }
        }
    }
}
