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
        $citizen = Citizen::query()->first();
        $user = User::query()->first();

        if (! $citizen || ! $user) {
            return;
        }

        FamilyCard::create([
            'head_id' => $citizen->id,
            'card_number' => '10000000001',
            'issue_date' => now()->toDateString(),
            'expiry_date' => now()->addYears(5)->toDateString(),
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
