<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'system_name',
                'value' => 'السجل المدني',
                'group' => 'general',
                'description' => 'اسم النظام',
                'is_public' => true,
            ],
            [
                'key' => 'organization_name',
                'value' => 'مصلحة الأحوال المدنية',
                'group' => 'general',
                'description' => 'اسم الجهة',
                'is_public' => true,
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Aden',
                'group' => 'general',
                'description' => 'المنطقة الزمنية للنظام',
                'is_public' => false,
            ],
            [
                'key' => 'locale',
                'value' => 'ar',
                'group' => 'general',
                'description' => 'اللغة الافتراضية للنظام',
                'is_public' => false,
            ],

            // Civil Registry
            [
                'key' => 'national_id_length',
                'value' => 11,
                'group' => 'civil_registry',
                'description' => 'عدد أرقام الرقم الوطني',
                'is_public' => false,
            ],
            [
                'key' => 'family_card_number_length',
                'value' => 11,
                'group' => 'civil_registry',
                'description' => 'عدد أرقام البطاقة العائلية',
                'is_public' => false,
            ],

            // Security
            [
                'key' => 'activity_log_enabled',
                'value' => true,
                'group' => 'security',
                'description' => 'تفعيل سجل أنشطة المستخدمين',
                'is_public' => false,
            ],

            // Backup
            [
                'key' => 'automatic_backup_enabled',
                'value' => true,
                'group' => 'backup',
                'description' => 'تفعيل النسخ الاحتياطي التلقائي',
                'is_public' => false,
            ],
            [
                'key' => 'theme_mode',
                'value' => 'system',
                'group' => 'appearance',
                'description' => 'وضع مظهر الواجهة',
                'is_public' => true,
            ],

            [
                'key' => 'primary_color',
                'value' => 'amber',
                'group' => 'appearance',
                'description' => 'اللون الرئيسي للواجهة',
                'is_public' => true,
            ],

            [
                'key' => 'sidebar_collapsed',
                'value' => false,
                'group' => 'appearance',
                'description' => 'طي القائمة الجانبية افتراضيًا',
                'is_public' => true,
            ],

            [
                'key' => 'brand_logo',
                'value' => null,
                'group' => 'appearance',
                'description' => 'شعار النظام',
                'is_public' => true,
            ],
            [
                'key' => 'theme',
                'value' => 'government',
                'group' => 'appearance',
                'description' => 'ثيم واجهة النظام',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
