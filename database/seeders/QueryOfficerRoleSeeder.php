<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class QueryOfficerRoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ═══════════════════════════════════════════════════════
        // 1) إنشاء الصلاحية — إدخال مباشر في DB لتجاوز مشكلة Spatie
        // ═══════════════════════════════════════════════════════
        $permissionId = DB::table('permissions')
            ->where('name', 'use-face-recognition')
            ->where('guard_name', 'web')
            ->value('id');

        if (! $permissionId) {
            $permissionId = (string) Str::uuid();

            DB::table('permissions')->insert([
                'id'          => $permissionId,
                'name'        => 'use-face-recognition',
                'guard_name'  => 'web',
                'module'      => 'face',
                'action'      => 'identify',
                'description' => 'استخدام ميزة التعرف على المواطنين من الصورة',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $this->command->info('✅ تم إنشاء الصلاحية: use-face-recognition');
        } else {
            $this->command->info('ℹ️ الصلاحية موجودة مسبقاً: use-face-recognition');
        }

        // ═══════════════════════════════════════════════════════
        // 2) إنشاء الدور — نفس الطريقة
        // ═══════════════════════════════════════════════════════
        $roleId = DB::table('roles')
            ->where('name', 'query_officer')
            ->where('guard_name', 'web')
            ->value('id');

        if (! $roleId) {
            $roleId = (string) Str::uuid();

            DB::table('roles')->insert([
                'id'         => $roleId,
                'name'       => 'query_officer',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('✅ تم إنشاء الدور: query_officer');
        } else {
            $this->command->info('ℹ️ الدور موجود مسبقاً: query_officer');
        }

        // ═══════════════════════════════════════════════════════
        // 3) ربط الصلاحية بالدور
        // ═══════════════════════════════════════════════════════
        $exists = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->exists();

        if (! $exists) {
            DB::table('role_has_permissions')->insert([
                'role_id'       => $roleId,
                'permission_id' => $permissionId,
            ]);
            $this->command->info('✅ تم ربط الصلاحية بالدور');
        } else {
            $this->command->info('ℹ️ الصلاحية مرتبطة بالدور مسبقاً');
        }

        // ═══════════════════════════════════════════════════════
        // 4) تنظيف الكاش
        // ═══════════════════════════════════════════════════════
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ═══════════════════════════════════════════════════════
        // 5) ملخص
        // ═══════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('✅ اكتمل الإعداد');
        $this->command->info('═══════════════════════════════════════════════');

        $this->command->table(
            ['النوع', 'الاسم', 'UUID'],
            [
                ['الصلاحية', 'use-face-recognition', $permissionId],
                ['الدور', 'query_officer', $roleId],
            ],
        );
    }
}