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
            FamilyCard::create([
                'head_id' => $citizens[$i * 4]->id,
                'card_number' => '1000000000' . ($i + 1),
                'issue_date' => null,
                'expiry_date' => null,
                'status' => 'pending',
                'notes' => null,
                'qr_code' => null,
                'print_count' => 0,
                'issued_by' => $user->id,
                'approved_by' => null,
                'approved_at' => null,
            ]);
        }
    }
}
