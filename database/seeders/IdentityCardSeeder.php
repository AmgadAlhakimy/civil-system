<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Citizen;
use App\Models\IdentityCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class IdentityCardSeeder extends Seeder
{
    public function run(): void
    {
        $citizens = Citizen::query()
            ->limit(30)
            ->get();

        $user = User::first();

        $branch = Branch::query()->first();

        if ($citizens->isEmpty() || ! $user || ! $branch) {
            return;
        }

        foreach ($citizens as $citizen) {
            $issueDate = now()->subYear();

            do {
                $nationalId = '';

                for ($i = 0; $i < 11; $i++) {
                    $nationalId .= (string) random_int(0, 9);
                }
            } while (
                IdentityCard::withTrashed()
                    ->where('national_id', $nationalId)
                    ->exists()
            );

            IdentityCard::create([
                'citizen_id' => $citizen->id,
                'branch_id' => $branch->id,
                'national_id' => $nationalId,
                'issue_date' => $issueDate,
                'expiry_date' => $issueDate->copy()->addYears(10),
                'status' => fake()->randomElement([
                    'active',
                    'pending',
                    'rejected',
                    'expired',
                    'cancelled',
                    'lost',
                    'damaged',
                ]),
                'notes' => null,
                'rejection_reason' => null,
                'print_count' => 1,
                'issued_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => $issueDate,
            ]);
        }
    }
}
