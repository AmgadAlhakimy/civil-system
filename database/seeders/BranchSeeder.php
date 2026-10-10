<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::create([
            'code' => 'SNA-01',
            'name' => 'فرع صنعاء',
            'address' => 'صنعاء',
            'phone' => '777000001',
            'email' => 'sanaa@civil.gov',
            'is_active' => true,
        ]);

        Branch::create([
            'code' => 'ADN-01',
            'name' => 'فرع عدن',
            'address' => 'عدن',
            'phone' => '777000002',
            'email' => 'aden@civil.gov',
            'is_active' => true,
        ]);

        Branch::create([
            'code' => 'TIZ-01',
            'name' => 'فرع تعز',
            'address' => 'تعز',
            'phone' => '777000003',
            'email' => 'taiz@civil.gov',
            'is_active' => true,
        ]);
    }
}
