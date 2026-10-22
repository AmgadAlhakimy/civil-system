<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Governorate;

class GovernorateApiController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // جلب جميع المحافظات
    // ═══════════════════════════════════════════════════════════
    public function index()
    {
        $governorates = Governorate::active()
            ->ordered()
            ->get()
            ->map(fn ($gov) => [
                'id' => $gov->id,
                'code' => $gov->code,
                'name_ar' => $gov->name_ar,
                'name_en' => $gov->name_en,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $governorates,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // جلب مديريات محافظة محددة
    // ═══════════════════════════════════════════════════════════
    public function districts(string $governorateId)
    {
        $governorate = Governorate::find($governorateId);

        if (!$governorate) {
            return response()->json([
                'status' => 'error',
                'message' => 'المحافظة غير موجودة',
            ], 404);
        }

        $districts = $governorate->districts()
            ->active()
            ->ordered()
            ->get()
            ->map(fn ($dist) => [
                'id' => $dist->id,
                'name_ar' => $dist->name_ar,
                'name_en' => $dist->name_en,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $districts,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // جلب فروع محافظة محددة
    // ═══════════════════════════════════════════════════════════
    public function branches(string $governorateId)
    {
        $branches = Branch::where('governorate_id', $governorateId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($branch) => [
                'id' => $branch->id,
                'code' => $branch->code,
                'name' => $branch->name,
                'address' => $branch->address,
                'phone' => $branch->phone,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $branches,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // جلب كل شيء (للتخزين المؤقت)
    // ═══════════════════════════════════════════════════════════
    public function allWithDistricts()
    {
        $governorates = Governorate::active()
            ->ordered()
            ->with(['districts' => fn ($q) => $q->active()->ordered()])
            ->get()
            ->map(fn ($gov) => [
                'id' => $gov->id,
                'code' => $gov->code,
                'name_ar' => $gov->name_ar,
                'name_en' => $gov->name_en,
                'districts' => $gov->districts->map(fn ($dist) => [
                    'id' => $dist->id,
                    'name_ar' => $dist->name_ar,
                    'name_en' => $dist->name_en,
                ]),
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $governorates,
        ]);
    }
}