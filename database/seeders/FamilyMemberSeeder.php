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
        $familyCard = FamilyCard::query()->first();
        $user = User::query()->first();

        if (! $familyCard || ! $user) {
            return;
        }

        $citizens = Citizen::query()
            ->where('id', '!=', $familyCard->head_id)
            ->limit(3)
            ->get();

        foreach ($citizens as $index => $citizen) {
            FamilyMember::create([
                'family_card_id' => $familyCard->id,
                'citizen_id' => $citizen->id,
                'relationship' => match ($index) {
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
