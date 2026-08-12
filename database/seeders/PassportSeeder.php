<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\Passport;
use Illuminate\Database\Seeder;

class PassportSeeder extends Seeder
{
    public function run(): void
    {
        Citizen::query()->each(function (Citizen $citizen) {
            Passport::factory()->create([
                'citizen_id' => $citizen->id,
            ]);
        });
    }
}
