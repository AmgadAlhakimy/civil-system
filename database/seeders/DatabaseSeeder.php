<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء الفروع
        $this->call([
            BranchSeeder::class,
        ]);

        // إنشاء الأدوار والصلاحيات
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // إنشاء مستخدم مدير النظام
        $admin = User::factory()->create([
            'name' => 'Amjad',
            'full_name' => 'مدير النظام',
            'email' => 'admin@civil.gov',
            'password' => 'password',
            'branch_id' => Branch::where('code', 'SNA-01')->value('id'),
            'status' => 'active',
        ]);

        $admin->assignRole('admin');

        // بيانات النظام التجريبية
        $this->call([
            CitizenSeeder::class,
            PassportSeeder::class,
            IdentityCardSeeder::class,
            FamilyMemberSeeder::class,
            FamilyCardSeeder::class,
            BirthCertificateSeeder::class,

        ]);
    }
}
