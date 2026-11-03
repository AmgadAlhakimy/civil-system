<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $aden = Branch::where('code', 'ADN-01')->first();
        $taiz = Branch::where('code', 'TIZ-01')->first();

        $employee01 = User::updateOrCreate(
            ['email' => 'employee01@civil.gov'],
            [
                'name' => 'employee01',
                'full_name' => 'موظف فرع عدن',
                'phone' => '777100002',
                'password' => 'password',
                'must_change_password' => true,
                'account_type' => 'employee',
                'branch_id' => $aden?->id,
                'status' => 'active',
            ]
        );

        $employee01->syncRoles([
            'موظف التسجيل',
            'المعتمد',
        ]);

        $employee02 = User::updateOrCreate(
            ['email' => 'employee02@civil.gov'],
            [
                'name' => 'employee02',
                'full_name' => 'موظف فرع تعز',
                'phone' => '777100003',
                'password' => 'password',
                'must_change_password' => true,
                'account_type' => 'employee',
                'branch_id' => $taiz?->id,
                'status' => 'active',
            ]
        );

        $employee02->syncRoles([
            'موظف الجوازات',
            'المعتمد',
        ]);
    }
}
