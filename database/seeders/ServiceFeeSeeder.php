<?php

namespace Database\Seeders;

use App\Models\ServiceFee;
use Illuminate\Database\Seeder;

class ServiceFeeSeeder extends Seeder
{
    public function run(): void
    {
        $fees = [
            [
                'service_name' => 'إصدار بطاقة شخصية جديدة',
                'category' => 'identity_card',
                'amount' => 5000.00,
                'is_active' => true,
                'notes' => 'الرسوم الأساسية لإصدار بطاقة لأول مرة',
            ],
            [
                'service_name' => 'تجديد بطاقة شخصية',
                'category' => 'identity_card',
                'amount' => 3000.00,
                'is_active' => true,
                'notes' => 'رسوم تجديد بطاقة منتهية',
            ],
            [
                'service_name' => 'إصدار جواز سفر جديد',
                'category' => 'passport',
                'amount' => 15000.00,
                'is_active' => true,
                'notes' => 'رسوم إصدار جواز سفر لأول مرة',
            ],
            [
                'service_name' => 'تجديد جواز سفر',
                'category' => 'passport',
                'amount' => 10000.00,
                'is_active' => true,
                'notes' => 'رسوم تجديد جواز سفر منتهي',
            ],
            [
                'service_name' => 'إصدار بطاقة عائلية جديدة',
                'category' => 'family_card',
                'amount' => 4000.00,
                'is_active' => true,
                'notes' => 'رسوم إصدار بطاقة عائلية',
            ],
            [
                'service_name' => 'تجديد بطاقة عائلية',
                'category' => 'family_card',
                'amount' => 2500.00,
                'is_active' => true,
                'notes' => 'رسوم تجديد بطاقة عائلية',
            ],
            [
                'service_name' => 'إصدار شهادة ميلاد',
                'category' => 'birth_certificate',
                'amount' => 2000.00,
                'is_active' => true,
                'notes' => 'رسوم إصدار شهادة ميلاد',
            ],
            [
                'service_name' => 'إصدار شهادة وفاة',
                'category' => 'death_certificate',
                'amount' => 2000.00,
                'is_active' => true,
                'notes' => 'رسوم إصدار شهادة وفاة',
            ],
        ];

        foreach ($fees as $fee) {
            ServiceFee::updateOrCreate(
                [
                    'service_name' => $fee['service_name'],
                    'category' => $fee['category'],
                ],
                $fee
            );
        }
    }
}
