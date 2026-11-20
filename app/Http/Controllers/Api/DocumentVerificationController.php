<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\DocumentVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentVerificationController extends Controller
{
    public function __construct(
        private readonly DocumentVerificationService $verificationService
    ) {}

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
            'document_type' => [
                'required',
                'string',
                'max:100',
            ],
            'service_request_id' => [
                'required',
                'uuid',
                Rule::exists('service_requests', 'id')->where(
                    fn ($query) => $query->where('user_id', $request->user()->id)
                ),
            ],
            // قيمة تساعد على تحديد المرفق داخل documents JSON (مثل document key أو storage path).
            'document_reference' => ['nullable', 'string', 'max:500'],
        ], [
            'document.required' => 'يرجى إرفاق المستند.',
            'document.image' => 'الملف المرفق يجب أن يكون صورة.',
            'document.mimes' => 'نوع الصورة غير مدعوم.',
            'document.max' => 'حجم الصورة يجب ألا يتجاوز 10 ميجابايت.',
            'document_type.required' => 'نوع المستند مطلوب.',
            'service_request_id.required' => 'رقم الطلب مطلوب لربط نتيجة الفحص به.',
            'service_request_id.exists' => 'الطلب غير موجود أو لا يتبع حسابك.',
        ]);

        $serviceRequest = ServiceRequest::query()
            ->whereKey($validated['service_request_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $result = $this->verificationService->verify(
            $request->user(),
            $request->file('document'),
            $validated['document_type'],
            $serviceRequest,
            $validated['document_reference'] ?? null,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'اكتمل الفحص الأولي للمستند وربط نتيجته بالطلب.',
            'official_verification' => false,
            'data' => $result,
        ]);
    }
}
