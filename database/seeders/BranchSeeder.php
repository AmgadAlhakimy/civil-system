<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['code' => 'SNA-01', 'name' => 'فرع أمانة العاصمة 1', 'governorate_code' => 'SNA', 'address' => 'أمانة العاصمة - صنعاء'],
            ['code' => 'SNA-02', 'name' => 'فرع أمانة العاصمة 2', 'governorate_code' => 'SNA', 'address' => 'أمانة العاصمة - صنعاء'],
            ['code' => 'SNA-03', 'name' => 'فرع أمانة العاصمة 3', 'governorate_code' => 'SNA', 'address' => 'أمانة العاصمة - صنعاء'],
            ['code' => 'SNA-04', 'name' => 'فرع أمانة العاصمة 4', 'governorate_code' => 'SNA', 'address' => 'أمانة العاصمة - صنعاء'],
            ['code' => 'SNA-05', 'name' => 'فرع أمانة العاصمة 5', 'governorate_code' => 'SNA', 'address' => 'أمانة العاصمة - صنعاء'],

            ['code' => 'ADN-01', 'name' => 'فرع عدن 1', 'governorate_code' => 'ADN', 'address' => 'عدن'],
            ['code' => 'ADN-02', 'name' => 'فرع عدن 2', 'governorate_code' => 'ADN', 'address' => 'عدن'],
            ['code' => 'ADN-03', 'name' => 'فرع عدن 3', 'governorate_code' => 'ADN', 'address' => 'عدن'],

            ['code' => 'TAIZ-01', 'name' => 'فرع تعز 1', 'governorate_code' => 'TAIZ', 'address' => 'تعز'],
            ['code' => 'TAIZ-02', 'name' => 'فرع تعز 2', 'governorate_code' => 'TAIZ', 'address' => 'تعز'],
            ['code' => 'TAIZ-03', 'name' => 'فرع تعز 3', 'governorate_code' => 'TAIZ', 'address' => 'تعز'],

            ['code' => 'IB-01', 'name' => 'فرع إب 1', 'governorate_code' => 'IB', 'address' => 'إب'],
            ['code' => 'IB-02', 'name' => 'فرع إب 2', 'governorate_code' => 'IB', 'address' => 'إب'],

            ['code' => 'HODEIDAH-01', 'name' => 'فرع الحديدة 1', 'governorate_code' => 'HODEIDAH', 'address' => 'الحديدة'],
            ['code' => 'HODEIDAH-02', 'name' => 'فرع الحديدة 2', 'governorate_code' => 'HODEIDAH', 'address' => 'الحديدة'],

            ['code' => 'HADH-01', 'name' => 'فرع حضرموت 1', 'governorate_code' => 'HADH', 'address' => 'حضرموت'],
            ['code' => 'HADH-02', 'name' => 'فرع حضرموت 2', 'governorate_code' => 'HADH', 'address' => 'حضرموت'],

            ['code' => 'DHAMAR-01', 'name' => 'فرع ذمار', 'governorate_code' => 'DHAMAR', 'address' => 'ذمار'],
            ['code' => 'AMRAN-01', 'name' => 'فرع عمران', 'governorate_code' => 'AMRAN', 'address' => 'عمران'],
            ['code' => 'MARIB-01', 'name' => 'فرع مأرب', 'governorate_code' => 'MARIB', 'address' => 'مأرب'],
        ];

        foreach ($branches as $index => $branchData) {
            $governorate = Governorate::where(
                'code',
                $branchData['governorate_code']
            )->first();

            if (! $governorate) {
                $this->command?->warn(
                    "تم تخطي الفرع {$branchData['code']}: لم يتم العثور على المحافظة بالكود {$branchData['governorate_code']}."
                );

                continue;
            }

            Branch::updateOrCreate(
                ['code' => $branchData['code']],
                [
                    'name' => $branchData['name'],
                    'governorate_id' => $governorate->id,
                    'address' => $branchData['address'],

                    // أرقام تجريبية فقط وليست أرقام اتصال حقيقية
                    'phone' => '700' . str_pad(
                        (string) ($index + 1),
                        6,
                        '0',
                        STR_PAD_LEFT
                    ),

                    'email' => null,
                    'manager_id' => null,
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info('تمت معالجة بيانات الفروع بنجاح.');
    }
}