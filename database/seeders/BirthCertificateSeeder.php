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
        $user = User::query()->first();

        if (! $user) {
            return;
        }

        $children = Citizen::query()
            ->whereNotNull('birth_date')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        $fathers = Citizen::query()
            ->where('gender', 'male')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        $mothers = Citizen::query()
            ->where('gender', 'female')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        if (
            $children->count() < 5 ||
            $fathers->count() < 5 ||
            $mothers->count() < 5
        ) {
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            $child = $children[$i];
            $father = $fathers->firstWhere('id', '!=', $child->id);
            $mother = $mothers->firstWhere('id', '!=', $child->id);

            if (! $father || ! $mother) {
                continue;
            }

            BirthCertificate::create([
                'child_id' => $child->id,
                'father_id' => $father->id,
                'mother_id' => $mother->id,
                'certificate_number' => '1000000000' . ($i + 1),
                'issue_date' => null,
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
