<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache
        |--------------------------------------------------------------------------
        */

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | Branches
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'branches.view',
                'name_ar' => 'عرض الفروع',
                'module' => 'branches',
                'action' => 'view',
                'description' => 'عرض بيانات الفروع',
            ],
            [
                'name' => 'branches.create',
                'name_ar' => 'إضافة الفروع',
                'module' => 'branches',
                'action' => 'create',
                'description' => 'إضافة فرع جديد',
            ],
            [
                'name' => 'branches.edit',
                'name_ar' => 'تعديل الفروع',
                'module' => 'branches',
                'action' => 'edit',
                'description' => 'تعديل بيانات الفروع',
            ],
            [
                'name' => 'branches.delete',
                'name_ar' => 'حذف الفروع',
                'module' => 'branches',
                'action' => 'delete',
                'description' => 'حذف بيانات الفروع',
            ],
            [
                'name' => 'branches.restore',
                'name_ar' => 'استعادة الفروع',
                'module' => 'branches',
                'action' => 'restore',
                'description' => 'استعادة الفروع المحذوفة',
            ],
            [
                'name' => 'branches.force_delete',
                'name_ar' => 'الحذف النهائي للفروع',
                'module' => 'branches',
                'action' => 'force_delete',
                'description' => 'حذف الفروع نهائيًا',
            ],

            /*
            |--------------------------------------------------------------------------
            | Citizens
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'citizens.view',
                'name_ar' => 'عرض المواطنين',
                'module' => 'citizens',
                'action' => 'view',
                'description' => 'عرض بيانات المواطنين',
            ],
            [
                'name' => 'citizens.create',
                'name_ar' => 'إضافة المواطنين',
                'module' => 'citizens',
                'action' => 'create',
                'description' => 'إضافة مواطن جديد',
            ],
            [
                'name' => 'citizens.edit',
                'name_ar' => 'تعديل المواطنين',
                'module' => 'citizens',
                'action' => 'edit',
                'description' => 'تعديل بيانات المواطنين',
            ],
            [
                'name' => 'citizens.delete',
                'name_ar' => 'حذف المواطنين',
                'module' => 'citizens',
                'action' => 'delete',
                'description' => 'حذف بيانات المواطنين',
            ],

            /*
            |--------------------------------------------------------------------------
            | Passports
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'passports.view',
                'name_ar' => 'عرض الجوازات',
                'module' => 'passports',
                'action' => 'view',
                'description' => 'عرض بيانات الجوازات',
            ],
            [
                'name' => 'passports.create',
                'name_ar' => 'إضافة الجوازات',
                'module' => 'passports',
                'action' => 'create',
                'description' => 'إضافة جواز جديد',
            ],
            [
                'name' => 'passports.edit',
                'name_ar' => 'تعديل الجوازات',
                'module' => 'passports',
                'action' => 'edit',
                'description' => 'تعديل بيانات الجوازات',
            ],
            [
                'name' => 'passports.delete',
                'name_ar' => 'حذف الجوازات',
                'module' => 'passports',
                'action' => 'delete',
                'description' => 'حذف بيانات الجوازات',
            ],
            [
                'name' => 'passports.approve',
                'name_ar' => 'اعتماد الجوازات',
                'module' => 'passports',
                'action' => 'approve',
                'description' => 'اعتماد طلبات الجوازات',
            ],
            [
                'name' => 'passports.reject',
                'name_ar' => 'رفض الجوازات',
                'module' => 'passports',
                'action' => 'reject',
                'description' => 'رفض طلبات الجوازات',
            ],

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'users.view',
                'name_ar' => 'عرض المستخدمين',
                'module' => 'users',
                'action' => 'view',
                'description' => 'عرض المستخدمين',
            ],
            [
                'name' => 'users.create',
                'name_ar' => 'إضافة المستخدمين',
                'module' => 'users',
                'action' => 'create',
                'description' => 'إضافة مستخدم جديد',
            ],
            [
                'name' => 'users.edit',
                'name_ar' => 'تعديل المستخدمين',
                'module' => 'users',
                'action' => 'edit',
                'description' => 'تعديل بيانات المستخدمين',
            ],
            [
                'name' => 'users.delete',
                'name_ar' => 'حذف المستخدمين',
                'module' => 'users',
                'action' => 'delete',
                'description' => 'حذف المستخدمين',
            ],

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'roles.view',
                'name_ar' => 'عرض الأدوار',
                'module' => 'roles',
                'action' => 'view',
                'description' => 'عرض الأدوار والصلاحيات',
            ],
            [
                'name' => 'roles.create',
                'name_ar' => 'إضافة الأدوار',
                'module' => 'roles',
                'action' => 'create',
                'description' => 'إضافة دور جديد',
            ],
            [
                'name' => 'roles.edit',
                'name_ar' => 'تعديل الأدوار',
                'module' => 'roles',
                'action' => 'edit',
                'description' => 'تعديل الأدوار والصلاحيات',
            ],
            [
                'name' => 'roles.delete',
                'name_ar' => 'حذف الأدوار',
                'module' => 'roles',
                'action' => 'delete',
                'description' => 'حذف الأدوار',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permissionData) {

            Permission::firstOrCreate(
                [
                    'name' => $permissionData['name'],
                    'guard_name' => 'web',
                ],
                [
                    'id' => (string) Str::uuid(),
                    'name_ar' => $permissionData['name_ar'],
                    'module' => $permissionData['module'],
                    'action' => $permissionData['action'],
                    'description' => $permissionData['description'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = [
            [
                'name' => 'admin',
                'name_ar' => 'مدير النظام',
            ],
            [
                'name' => 'registration_officer',
                'name_ar' => 'موظف التسجيل',
            ],
            [
                'name' => 'passport_officer',
                'name_ar' => 'موظف الجوازات',
            ],
            [
                'name' => 'approver',
                'name_ar' => 'المعتمد',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Roles
        |--------------------------------------------------------------------------
        */

        foreach ($roles as $roleData) {

            Role::firstOrCreate(
                [
                    'name' => $roleData['name'],
                    'guard_name' => 'web',
                ],
                [
                    'id' => (string) Str::uuid(),
                    'name_ar' => $roleData['name_ar'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::where('name', 'admin')->first();

        $registrationOfficer = Role::where(
            'name',
            'registration_officer'
        )->first();

        $passportOfficer = Role::where(
            'name',
            'passport_officer'
        )->first();

        $approver = Role::where(
            'name',
            'approver'
        )->first();

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions
        |--------------------------------------------------------------------------
        */

        // Admin
        // يحصل على جميع الصلاحيات في النظام
        $admin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // Registration Officer
        $registrationOfficer->syncPermissions([
            'citizens.view',
            'citizens.create',
            'citizens.edit',
        ]);

        // Passport Officer
        $passportOfficer->syncPermissions([
            'citizens.view',
            'passports.view',
            'passports.create',
            'passports.edit',
        ]);

        // Approver
        $approver->syncPermissions([
            'citizens.view',
            'passports.view',
            'passports.approve',
            'passports.reject',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache
        |--------------------------------------------------------------------------
        */

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
