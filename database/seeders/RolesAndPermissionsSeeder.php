<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // مسح Cache الخاص بالصلاحيات
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Citizens
            'citizens.view',
            'citizens.create',
            'citizens.edit',
            'citizens.delete',

            // Passports
            'passports.view',
            'passports.create',
            'passports.edit',
            'passports.delete',
            'passports.approve',
            'passports.reject',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Roles & Permissions
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'مدير النظام',
            'guard_name' => 'web',
        ]);

        $registrationOfficer = Role::firstOrCreate([
            'name' => 'موظف تسجيل',
            'guard_name' => 'web',
        ]);

        $passportOfficer = Role::firstOrCreate([
            'name' => 'موظف جوازات',
            'guard_name' => 'web',
        ]);

        $approver = Role::firstOrCreate([
            'name' => 'موظف اعتماد',
            'guard_name' => 'web',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Registration Officer
        |--------------------------------------------------------------------------
        */

        $registrationOfficer->syncPermissions([
            'citizens.view',
            'citizens.create',
            'citizens.edit',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Passport Officer
        |--------------------------------------------------------------------------
        */

        $passportOfficer->syncPermissions([
            'citizens.view',

            'passports.view',
            'passports.create',
            'passports.edit',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Approver
        |--------------------------------------------------------------------------
        */

        $approver->syncPermissions([
            'citizens.view',

            'passports.view',
            'passports.approve',
            'passports.reject',
        ]);

        // مسح الكاش مرة أخرى بعد إنشاء الصلاحيات والأدوار
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
