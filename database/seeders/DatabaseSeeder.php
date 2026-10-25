<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@civil.gov'],
            [
                'name' => 'Amjad',
                'full_name' => 'مدير النظام',
                'phone' => '777100001',
                'password' => 'Pa$$w0rd',
                'must_change_password' => true,
                'account_type' => 'employee',
                'branch_id' => Branch::where('code', 'SNA-01')->value('id'),
                'status' => 'active',
            ]
        );

        $admin->assignRole('admin');

        $this->call([
            UserSeeder::class,
            CitizenSeeder::class,
            PassportSeeder::class,
            IdentityCardSeeder::class,
            FamilyMemberSeeder::class,
            FamilyCardSeeder::class,
            BirthCertificateSeeder::class,
            DeathCertificateSeeder::class,
            AppointmentSeeder::class,
            SettingSeeder::class,
            GovernorateDistrictSeeder::class,
            ServiceFeeSeeder::class,
        ]);
    }
}
