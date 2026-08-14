<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\IdentityCard;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IdentityCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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

            IdentityCard::create([
                'id' => (string) Str::uuid(),

                'citizen_id' => $citizen->id,

                'id_number' => 'ID' . str_pad(
                        $index + 1,
                        8,
                        '0',
                        STR_PAD_LEFT
                    ),

                'issue_date' => now()->subYear(),

                'expiry_date' => now()->addYears(4),

                'status' => 'active',

                'notes' => null,

                'qr_code' => null,

                'print_count' => 1,

                'issued_by' => $user->id,

                'approved_by' => $user->id,

                'approved_at' => now(),
            ]);
        }
    }
}
