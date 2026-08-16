<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Citizen;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $citizens = Citizen::query()->get();
        $branches = Branch::query()->get();
        $users = User::query()->get();

        if ($citizens->isEmpty() || $branches->isEmpty() || $users->isEmpty()) {
            return;
        }

        $services = [
            'passport_new',
            'passport_renew',
            'passport_lost',
            'passport_damaged',
            'national_id_new',
            'national_id_renew',
            'national_id_lost',
            'national_id_damaged',
            'family_card_new',
            'family_card_renew',
            'birth_certificate',
            'death_certificate',
        ];

        $statuses = [
            'pending',
            'confirmed',
            'attended',
            'cancelled',
            'no_show',
        ];

        for ($i = 0; $i < 10; $i++) {
            $date = now()->addDays($i + 1)->toDateString();
            $time = sprintf('%02d:00:00', 9 + ($i % 6));

            $appointment = Appointment::create([
                'citizen_id' => $citizens->random()->id,
                'branch_id' => $branches->random()->id,
                'user_id' => $users->random()->id,
                'service_type' => $services[$i % count($services)],
                'appointment_date' => $date,
                'appointment_time' => $time,
                'status' => $statuses[$i % count($statuses)],
                'notes' => null,
                'qr_code' => null,
                'confirmed_by' => null,
                'confirmed_at' => null,
            ]);

            if ($appointment->status === 'confirmed') {
                $appointment->update([
                    'confirmed_by' => $users->random()->id,
                    'confirmed_at' => now(),
                ]);
            }
        }
    }
}
