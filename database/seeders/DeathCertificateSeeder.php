<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\DeathCertificate;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeathCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $citizens = Citizen::query()
            ->where('is_active', true)
            ->take(5)
            ->get();

        $user = User::first();

        if (! $user) {
            return;
        }

        foreach ($citizens as $index => $citizen) {
            DeathCertificate::create([
                'deceased_id' => $citizen->id,

                'death_date' => now()
                    ->subYears($index + 1)
                    ->toDateString(),

                'cause_of_death' => [
                    'وفاة طبيعية',
                    'مرض',
                    'حادث',
                    'أسباب مرضية',
                    'سبب غير محدد',
                ][$index],

                'place_of_death' => 'صنعاء',

                'certificate_number' => 'DC' . str_pad(
                        $index + 1,
                        8,
                        '0',
                        STR_PAD_LEFT
                    ),

                'issue_date' => null,

                'status' => 'pending',

                'approved_by' => null,

                'approved_at' => null,

                'notes' => null,

                'qr_code' => null,

                'print_count' => 0,

                'issued_by' => $user->id,
            ]);
        }
    }
}
