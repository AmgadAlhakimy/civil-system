<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // جلب المحافظات المعروفة
        $sanaa = Governorate::where('code', 'SNA')->first();
        $aden = Governorate::where('code', 'ADN')->first();
        $taiz = Governorate::where('code', 'TAIZ')->first();
        $hodeidah = Governorate::where('code', 'HODEIDAH')->first();
        $hadhramout = Governorate::where('code', 'HADH')->first();

        $branches = [
            [
                'code' => 'SNA-01',
                'name' => 'فرع صنعاء الرئيسي',
                'address' => 'أمانة العاصمة - شارع الزبيري',
                'phone' => '777000001',
                'email' => 'sanaa@civil.gov',
                'governorate_id' => $sanaa?->id,
                'is_active' => true,
            ],
            [
                'code' => 'ADN-01',
                'name' => 'فرع عدن الرئيسي',
                'address' => 'عدن - المنصورة - شارع الميناء',
                'phone' => '777000002',
                'email' => 'aden@civil.gov',
                'governorate_id' => $aden?->id,
                'is_active' => true,
            ],
            [
                'code' => 'TIZ-01',
                'name' => 'فرع تعز الرئيسي',
                'address' => 'تعز - شارع جمال',
                'phone' => '777000003',
                'email' => 'taiz@civil.gov',
                'governorate_id' => $taiz?->id,
                'is_active' => true,
            ],
            [
                'code' => 'HOD-01',
                'name' => 'فرع الحديدة الرئيسي',
                'address' => 'الحديدة - شارع صنعاء',
                'phone' => '777000004',
                'email' => 'hodeidah@civil.gov',
                'governorate_id' => $hodeidah?->id,
                'is_active' => true,
            ],
            [
                'code' => 'HDM-01',
                'name' => 'فرع المكلا الرئيسي',
                'address' => 'حضرموت - المكلا - شارع الميناء',
                'phone' => '777000005',
                'email' => 'mukalla@civil.gov',
                'governorate_id' => $hadhramout?->id,
                'is_active' => true,
            ],
        ];

        foreach ($branches as $data) {
            Branch::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
