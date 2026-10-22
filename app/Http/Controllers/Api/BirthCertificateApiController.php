<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BirthCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BirthCertificateApiController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // جلب شهادات ميلاد المستخدم
    // ═══════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $certificates = BirthCertificate::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $certificates,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // إنشاء طلب شهادة ميلاد جديد
    // ═══════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_type' => 'required|string',
            'title' => 'required|string',
            'amount' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'data' => 'nullable|array',
            'documents' => 'nullable|array',
        ], [
            'request_type.required' => 'نوع الطلب مطلوب',
            'title.required' => 'عنوان الطلب مطلوب',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $requestNumber = 'BC-' . now()->format('Ymd') . '-' . rand(1000, 9999);

        try {
            $certificate = BirthCertificate::create([
                'user_id' => $request->user()->id,
                'request_number' => $requestNumber,
                'request_type' => $request->request_type ?? 'issue_birth_certificate',
                'title' => $request->title,
                'status' => 'pending',
                'amount' => $request->amount ?? 0,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status ?? 'pending',
                'data' => json_encode($request->data ?? []),
                'documents' => json_encode($request->documents ?? []),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء الطلب بنجاح',
                'data' => $certificate,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'تعذر إنشاء الطلب: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // جلب شهادة ميلاد واحدة
    // ═══════════════════════════════════════════════════════════
    public function show(Request $request, $id)
    {
        $certificate = BirthCertificate::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$certificate) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطلب غير موجود',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $certificate,
        ]);
    }
}