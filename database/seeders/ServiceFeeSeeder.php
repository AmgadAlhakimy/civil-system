<?php

namespace Database\Seeders;

use App\Models\ServiceFee;
use Illuminate\Database\Seeder;

class ServiceFeeSeeder extends Seeder
{
    /**
     * الأسعار الافتراضية للخدمات (بالريال اليمني)
     */
    public function run(): void
    {
        $fees = [
            // ═══════════════════════════════════════════════════════
            // البطاقات الشخصية
            // ═══════════════════════════════════════════════════════
            [
                'service_key' => 'new_id_card',
                'service_name' => 'إصدار بطاقة شخصية جديدة',
                'category' => 'id_card',
                'amount' => 5000.00,
                'currency' => 'YER',
                'is_active' => true,
                'notes' => 'الرسوم الأساسية لإصدار بطاقة لأول مرة',
            ],
            [
                'service_key' => 'renew_id_card',
                'service_name' => 'تجديد بطاقة شخصية',
                'category' => 'id_card',
                'amount' => 3000.00,
                'currency' => 'YER',
                'is_active' => true,
                'notes' => 'رسوم تجديد بطاقة منتهية',
            ],

            // ═══════════════════════════════════════════════════════
            // جوازات السفر
            // ═══════════════════════════════════════════════════════
            [
                'service_key' => 'new_passport',
                'service_name' => 'إصدار جواز سفر جديد',
                'category' => 'passport',
                'amount' => 15000.00,
                'currency' => 'YER',
                'is_active' => true,
                'notes' => 'رسوم إصدار جواز سفر لأول مرة',
            ],
            [
                'service_key' => 'renew_passport',
                'service_name' => 'تجديد جواز سفر',
                'category' => 'passport',
                'amount' => 10000.00,
                'currency' => 'YER',
                'is_active' => true,
                'notes' => 'رسوم تجديد جواز سفر منتهي',
            ],

            // ═══════════════════════════════════════════════════════
            // البطاقات العائلية
            // ═══════════════════════════════════════════════════════
            [
                'service_key' => 'new_family_card',
                'service_name' => 'إصدار بطاقة عائلية جديدة',
                'category' => 'family_card',
                'amount' => 4000.00,
                'currency' => 'YER',
                'is_active' => true,
                'notes' => 'رسوم إصدار بطاقة عائلية',
            ],
            [
                'service_key' => 'renew_family_card',
                'service_name' => 'تجديد بطاقة عائلية',
                'category' => 'family_card',
                'amount' => 2500.00,
                'currency' => 'YER',
                'is_active' => true,
                'notes' => 'رسوم تجديد بطاقة عائلية',
            ],

            // ═══════════════════════════════════════════════════════
            // الشهادات
            // ═══════════════════════════════════════════════════════
            [
                'service_key' => 'birth_certificate',
                'service_name' => 'إصدار شهادة ميلاد',
                'category' => 'certificate',
                'amount' => 2000.00,
                'currency' => 'YER',
                'is_active' => true,
                'notes' => 'رسوم إصدار شهادة ميلاد',
            ],
            [
                'service_key' => 'death_certificate',
                'service_name' => 'إصدار شهادة وفاة',
                'category' => 'certificate',
                'amount' => 2000.00,
                'currency' => 'YER',
                'is_active' => true,
                'notes' => 'رسوم إصدار شهادة وفاة',
            ],
        ];

        foreach ($fees as $fee) {
            // استخدام updateOrCreate لمنع التكرار
            ServiceFee::updateOrCreate(
                ['service_key' => $fee['service_key']],
                $fee
            );
        }
    }
}
