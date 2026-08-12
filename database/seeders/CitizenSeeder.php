<?php

namespace Database\Seeders;

use App\Models\Citizen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitizenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Citizen::factory(50)->create();
    }
}
