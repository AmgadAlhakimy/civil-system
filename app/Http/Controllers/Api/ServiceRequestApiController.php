<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ServiceRequestApiController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // جلب طلبات المستخدم
    // ═══════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $query = ServiceRequest::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc');

        // فلترة اختيارية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة اختيارية حسب الفئة
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $requests = $query->get()->map(fn ($req) => $this->formatRequest($req));

        return response()->json([
            'status' => 'success',
            'data' => $requests,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // إنشاء طلب جديد
    // ═══════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_key' => 'required|string',
            'service_name' => 'required|string',
            'category' => 'required|string',
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'branch_id' => 'nullable|uuid|exists:branches,id',
            'data' => 'nullable|array',
            'documents' => 'nullable|array',
        ], [
            'service_key.required' => 'نوع الخدمة مطلوب',
            'service_name.required' => 'اسم الخدمة مطلوب',
            'category.required' => 'الفئة مطلوبة',
            'title.required' => 'عنوان الطلب مطلوب',
            'amount.required' => 'المبلغ مطلوب',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // ✅ 1. توليد رقم طلب فريد
            $requestNumber = ServiceRequest::generateRequestNumber('REQ');

            // ✅ 2. إنشاء الطلب
            $serviceRequest = ServiceRequest::create([
                'user_id' => $request->user()->id,
                'branch_id' => $request->branch_id,
                'request_number' => $requestNumber,
                'service_key' => $request->service_key,
                'service_name' => $request->service_name,
                'category' => $request->category,
                'title' => $request->title,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'YER',
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status ?? 'pending',
                'status' => 'pending',
                'data' => $request->data ?? [],
                'documents' => $request->documents ?? [],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء الطلب بنجاح',
                'data' => $this->formatRequest($serviceRequest),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'تعذر إنشاء الطلب: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // جلب طلب واحد
    // ═══════════════════════════════════════════════════════════
    public function show(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$serviceRequest) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطلب غير موجود',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->formatRequest($serviceRequest),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // إلغاء طلب (من قبل المستخدم)
    // ═══════════════════════════════════════════════════════════
    public function cancel(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$serviceRequest) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطلب غير موجود',
            ], 404);
        }

        if (!in_array($serviceRequest->status, ['pending', 'processing'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن إلغاء الطلب في حالته الحالية',
            ], 400);
        }

        $serviceRequest->update([
            'status' => 'rejected',
            'rejection_reason' => 'أُلغي من قبل المستخدم',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إلغاء الطلب',
            'data' => $this->formatRequest($serviceRequest->fresh()),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // تنسيق الطلب للإرجاع
    // ═══════════════════════════════════════════════════════════
    private function formatRequest(ServiceRequest $req): array
    {
        return [
            'id' => $req->id,
            'request_number' => $req->request_number,
            'service_key' => $req->service_key,
            'service_name' => $req->service_name,
            'category' => $req->category,
            'category_label' => $req->category_label,
            'title' => $req->title,
            'amount' => (float) $req->amount,
            'currency' => $req->currency,
            'formatted_amount' => $req->formatted_amount,
            'payment_method' => $req->payment_method,
            'payment_status' => $req->payment_status,
            'payment_status_label' => $req->payment_status_label,
            'status' => $req->status,
            'status_label' => $req->status_label,
            'rejection_reason' => $req->rejection_reason,
            'branch_id' => $req->branch_id,
            'branch_name' => $req->branch?->name,
            'data' => $req->data,
            'documents' => $req->documents,
            'reviewed_at' => $req->reviewed_at?->toIso8601String(),
            'created_at' => $req->created_at?->toIso8601String(),
            'updated_at' => $req->updated_at?->toIso8601String(),
        ];
    }


    public function byCategory(Request $request, string $category)
{
    $allowed = ['id_card', 'certificate', 'passport', 'family_card', 'appointment'];

    if (!in_array($category, $allowed)) {
        return response()->json(['message' => 'فئة غير معروفة'], 404);
    }

    $requests = \App\Models\ServiceRequest::where('user_id', $request->user()->id)
        ->where('category', $category)
        ->latest()
        ->get();

    return response()->json([
        'data' => $requests,
    ]);
}
}