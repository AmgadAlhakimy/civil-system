<?php

namespace Database\Seeders;

use App\Models\BirthCertificate;
use App\Models\Citizen;
use App\Models\User;
use Illuminate\Database\Seeder;

class BirthCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $child = Citizen::where('gender', 'male')
            ->whereNotNull('birth_date')
            ->first();

        $father = Citizen::where('gender', 'male')
            ->where('id', '!=', $child?->id)
            ->first();

        $mother = Citizen::where('gender', 'female')
            ->where('id', '!=', $child?->id)
            ->first();

        $user = User::first();

        if (!$child || !$father || !$mother || !$user) {
            return;
        }

        BirthCertificate::create([
            'child_id' => $child->id,
            'father_id' => $father->id,
            'mother_id' => $mother->id,
            'certificate_number' => '10000000001',
            'issue_date' => now()->toDateString(),
            'status' => 'pending',
            'print_count' => 0,
            'issued_by' => $user->id,
        ]);
    }
}
