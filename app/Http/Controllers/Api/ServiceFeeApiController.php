<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceFee;
use Illuminate\Http\Request;

class ServiceFeeApiController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // جلب جميع الرسوم المفعّلة
    // ═══════════════════════════════════════════════════════════
    public function index()
    {
        $fees = ServiceFee::active()
            ->orderBy('category')
            ->orderBy('service_name')
            ->get()
            ->map(fn ($fee) => [
                'service_key' => $fee->service_key,
                'service_name' => $fee->service_name,
                'category' => $fee->category,
                'amount' => (float) $fee->amount,
                'currency' => $fee->currency,
                'formatted_amount' => $fee->formatted_amount,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $fees,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // جلب رسوم خدمة واحدة
    // ═══════════════════════════════════════════════════════════
    public function show(string $serviceKey)
    {
        $fee = ServiceFee::where('service_key', $serviceKey)
            ->where('is_active', true)
            ->first();

        if (!$fee) {
            return response()->json([
                'status' => 'error',
                'message' => 'الخدمة غير موجودة أو غير مفعّلة',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'service_key' => $fee->service_key,
                'service_name' => $fee->service_name,
                'category' => $fee->category,
                'amount' => (float) $fee->amount,
                'currency' => $fee->currency,
                'formatted_amount' => $fee->formatted_amount,
            ],
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // جلب الرسوم حسب الفئة
    // ═══════════════════════════════════════════════════════════
    public function byCategory(string $category)
    {
        $fees = ServiceFee::active()
            ->category($category)
            ->orderBy('service_name')
            ->get()
            ->map(fn ($fee) => [
                'service_key' => $fee->service_key,
                'service_name' => $fee->service_name,
                'category' => $fee->category,
                'amount' => (float) $fee->amount,
                'currency' => $fee->currency,
                'formatted_amount' => $fee->formatted_amount,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $fees,
        ]);
    }
}