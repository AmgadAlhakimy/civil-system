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
        $aden = Branch::where('code', 'ADN-01')->first();
        $taiz = Branch::where('code', 'TIZ-01')->first();

        User::updateOrCreate(
            ['email' => 'employee01@civil.gov'],
            [
                'name' => 'employee01',
                'full_name' => 'موظف فرع عدن',
                'phone' => '777100002',
                'password' => Hash::make('Pa$$w0rd'),
                'branch_id' => $aden?->id,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'employee02@civil.gov'],
            [
                'name' => 'employee02',
                'full_name' => 'موظف فرع تعز',
                'phone' => '777100003',
                'password' => Hash::make('Pa$$w0rd'),
                'branch_id' => $taiz?->id,
                'status' => 'active',
            ]
        );
    }
}
