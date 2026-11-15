<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Citizen;
use App\Models\FamilyCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class FamilyCardSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        $branch = Branch::query()->first();

        $citizens = Citizen::query()
            ->where('verification_status', 'approved')
            ->orderBy('created_at')
            ->limit(17)
            ->get();

        if (! $user || ! $branch || $citizens->count() < 17) {
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            do {
                $cardNumber = '';

                for ($j = 0; $j < 11; $j++) {
                    $cardNumber .= (string) random_int(0, 9);
                }
            } while (
                FamilyCard::withTrashed()
                    ->where('card_number', $cardNumber)
                    ->exists()
            );

            FamilyCard::create([
                'head_id' => $citizens[$i * 4]->id,
                'branch_id' => $branch->id,
                'card_number' => $cardNumber,
                'witness_1_front' => 'citizens/photos/01M6FZTNPPZ4754M6GSDJHD2RB.png',
                'witness_1_back' => 'citizens/photos/01M6FZTNPPZ4754M6GSDJHD2RB.png',
                'witness_2_front' => 'citizens/photos/01M6FZTNPPZ4754M6GSDJHD2RB.png',
                'witness_2_back' => 'citizens/photos/01M6FZTNPPZ4754M6GSDJHD2RB.png',
                'issue_date' => null,
                'expiry_date' => null,
                'status' => 'pending',
                'notes' => null,
                'rejection_reason' => null,
                'print_count' => 0,
                'issued_by' => $user->id,
                'approved_by' => null,
                'approved_at' => null,
            ]);
        }
    }
}
