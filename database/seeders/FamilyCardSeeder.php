<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\FamilyCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class FamilyCardSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        $citizens = Citizen::query()
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        if (! $user || $citizens->count() < 20) {
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
                'card_number' => $cardNumber,
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
