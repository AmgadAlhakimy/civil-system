<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\IdentityCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class IdentityCardSeeder extends Seeder
{
    public function run(): void
    {
        $citizens = Citizen::query()
            ->limit(10)
            ->get();

        $user = User::first();

        if ($citizens->isEmpty() || ! $user) {
            return;
        }

        foreach ($citizens as $index => $citizen) {
            $issueDate = now()->subYear();

            IdentityCard::create([
                'citizen_id' => $citizen->id,
                'id_number' => str_pad(
                    (string) ($index + 1),
                    11,
                    '0',
                    STR_PAD_LEFT
                ),
                'issue_date' => $issueDate,
                'expiry_date' => $issueDate->copy()->addYears(5),
                'status' => 'active',
                'notes' => null,
                'qr_code' => null,
                'print_count' => 1,
                'issued_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => $issueDate,
            ]);
        }
    }
}
