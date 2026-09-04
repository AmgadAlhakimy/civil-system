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
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        if (! $user || $familyCards->count() < 5) {
            return;
        }

        $citizens = Citizen::query()
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        if ($citizens->count() < 20) {
            return;
        }

        foreach ($familyCards as $cardIndex => $familyCard) {
            $startIndex = ($cardIndex * 4) + 1;

            for ($memberIndex = 0; $memberIndex < 3; $memberIndex++) {
                $citizen = $citizens[$startIndex + $memberIndex];

                FamilyMember::create([
                    'family_card_id' => $familyCard->id,
                    'citizen_id' => $citizen->id,
                    'relationship' => match ($memberIndex) {
                        0 => 'spouse',
                        1 => 'child',
                        default => 'child',
                    },
                    'is_active' => true,
                    'notes' => null,
                    'added_by' => $user->id,
                ]);
            }
        }
    }
}
