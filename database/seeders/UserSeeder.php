<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sanaa = Branch::where('code', 'SNA-01')->first();

        $aden = Branch::where('code', 'ADN-01')->first();

        $taiz = Branch::where('code', 'TIZ-01')->first();

        User::create([
            'name' => 'admin',
            'full_name' => 'مدير النظام',
            'email' => 'admin@civil.gov',
            'phone' => '777100001',
            'password' => Hash::make('password'),
            'branch_id' => $sanaa?->id,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'employee01',
            'full_name' => 'موظف فرع عدن',
            'email' => 'employee01@civil.gov',
            'phone' => '777100002',
            'password' => Hash::make('password'),
            'branch_id' => $aden?->id,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'employee02',
            'full_name' => 'موظف فرع تعز',
            'email' => 'employee02@civil.gov',
            'phone' => '777100003',
            'password' => Hash::make('password'),
            'branch_id' => $taiz?->id,
            'status' => 'active',
        ]);
    }
}
