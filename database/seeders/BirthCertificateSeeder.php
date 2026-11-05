<?php

namespace Database\Seeders;

use App\Models\BirthCertificate;
use App\Models\Branch;
use App\Models\Citizen;
use App\Models\User;
use Illuminate\Database\Seeder;

class BirthCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        $branch = Branch::query()->first();

        $children = Citizen::query()
            ->whereNotNull('birth_date')
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        $fathers = Citizen::query()
            ->where('gender', 'male')
            ->orderBy('created_at')
            ->get();

        $mothers = Citizen::query()
            ->where('gender', 'female')
            ->orderBy('created_at')
            ->get();

        if (
            ! $user ||
            ! $branch ||
            $children->count() < 20 ||
            $fathers->isEmpty() ||
            $mothers->isEmpty()
        ) {
            return;
        }

        foreach ($children as $child) {
            if (
                BirthCertificate::withTrashed()
                    ->where('child_id', $child->id)
                    ->exists()
            ) {
                continue;
            }

            $father = $fathers->first(
                fn ($citizen) => $citizen->id !== $child->id
            );

            $mother = $mothers->first(
                fn ($citizen) => $citizen->id !== $child->id
            );

            if (! $father || ! $mother) {
                continue;
            }

            BirthCertificate::create([
                'child_id' => $child->id,
                'father_id' => $father->id,
                'mother_id' => $mother->id,
                'branch_id' => $branch->id,
                'issue_date' => null,
                'status' => 'pending',
                'notes' => null,
                'rejection_reason' => null,
                'qr_code' => null,
                'print_count' => 0,
                'issued_by' => $user->id,
                'approved_by' => null,
                'approved_at' => null,
            ]);
        }
    }
}
