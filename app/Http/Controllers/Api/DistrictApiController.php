<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;

class DistrictApiController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // جلب جميع المديريات
    // ═══════════════════════════════════════════════════════════
    public function index()
    {
        $districts = District::active()
            ->ordered()
            ->with('governorate:id,name_ar')
            ->get()
            ->map(fn ($dist) => [
                'id' => $dist->id,
                'governorate_id' => $dist->governorate_id,
                'governorate_name' => $dist->governorate?->name_ar,
                'name_ar' => $dist->name_ar,
                'name_en' => $dist->name_en,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $districts,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // جلب مديرية واحدة
    // ═══════════════════════════════════════════════════════════
    public function show(string $id)
    {
        $district = District::with('governorate')->find($id);

        if (!$district) {
            return response()->json([
                'status' => 'error',
                'message' => 'المديرية غير موجودة',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $district->id,
                'governorate_id' => $district->governorate_id,
                'governorate' => [
                    'id' => $district->governorate?->id,
                    'name_ar' => $district->governorate?->name_ar,
                ],
                'name_ar' => $district->name_ar,
                'name_en' => $district->name_en,
            ],
        ]);
    }
}