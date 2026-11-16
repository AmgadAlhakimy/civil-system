<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\RequestStatusHistory;
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
            $requestNumber = ServiceRequest::generateRequestNumber('REQ');

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
    // تاريخ حالات الطلب (Timeline)
    // ═══════════════════════════════════════════════════════════
    public function history(Request $request, $id)
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

        $history = RequestStatusHistory::where('service_request_id', $id)
            ->with('changedBy')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($h) => [
                'from_status' => $h->from_status,
                'from_label'  => $h->from_status ? $this->statusLabel($h->from_status) : null,
                'to_status'   => $h->to_status,
                'to_label'    => $this->statusLabel($h->to_status),
                'notes'       => $h->notes,
                'changed_by'  => $h->changedBy?->full_name ?? $h->changedBy?->name,
                'created_at'  => $h->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'request' => [
                    'request_number'       => $serviceRequest->request_number,
                    'service_name'         => $serviceRequest->service_name,
                    'current_status'       => $serviceRequest->status,
                    'current_status_label' => $this->statusLabel($serviceRequest->status),
                ],
                'timeline' => $history,
            ],
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // إعادة إرسال طلب مرفوض بعد التعديل
    // ═══════════════════════════════════════════════════════════
    public function resubmit(Request $request, $id)
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

        if ($serviceRequest->status !== 'rejected') {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن تعديل طلب غير مرفوض',
            ], 400);
        }

        if (!$serviceRequest->is_resubmittable) {
            return response()->json([
                'status' => 'error',
                'message' => 'هذا الرفض نهائي ولا يمكن التعديل عليه',
            ], 400);
        }

        if ($serviceRequest->resubmission_count >= 3) {
            return response()->json([
                'status' => 'error',
                'message' => 'تجاوزت الحد الأقصى لإعادة الإرسال (3 مرات)',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'data' => 'nullable|array',
            'documents' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        RequestStatusHistory::create([
            'service_request_id' => $serviceRequest->id,
            'from_status' => 'rejected',
            'to_status' => 'pending',
            'notes' => 'أُعيد الإرسال بعد تعديل البيانات/الوثائق',
            'changed_by' => $request->user()->id,
        ]);

        $serviceRequest->update([
            'status' => 'pending',
            'rejection_reason' => null,
            'is_resubmittable' => false,
            'data' => $request->data ?? $serviceRequest->data,
            'documents' => $request->documents ?? $serviceRequest->documents,
            'resubmission_count' => $serviceRequest->resubmission_count + 1,
            'last_resubmitted_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إعادة إرسال الطلب — سيراجعه الموظف قريباً',
            'data' => $this->formatRequest($serviceRequest->fresh()),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // رفض الطلب (من قبل الموظف — API)
    // ═══════════════════════════════════════════════════════════
    public function rejectByEmployee(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'is_resubmittable' => 'required|boolean',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($id);
        $oldStatus = $serviceRequest->status;

        RequestStatusHistory::create([
            'service_request_id' => $serviceRequest->id,
            'from_status' => $oldStatus,
            'to_status' => 'rejected',
            'notes' => $request->rejection_reason,
            'changed_by' => $request->user()->id,
        ]);

        $serviceRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'is_resubmittable' => $request->is_resubmittable,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم رفض الطلب',
            'data' => $this->formatRequest($serviceRequest->fresh()),
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
            'is_resubmittable' => (bool) $req->is_resubmittable,
            'resubmission_count' => (int) $req->resubmission_count,
            'last_resubmitted_at' => $req->last_resubmitted_at?->toIso8601String(),
            'can_resubmit' => $req->status === 'rejected'
                && $req->is_resubmittable
                && $req->resubmission_count < 3,
            'branch_id' => $req->branch_id,
            'branch_name' => $req->branch?->name,
            'data' => $req->data,
            'documents' => $req->documents,
            'reviewed_at' => $req->reviewed_at?->toIso8601String(),
            'created_at' => $req->created_at?->toIso8601String(),
            'updated_at' => $req->updated_at?->toIso8601String(),
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // التسمية العربية للحالة
    // ═══════════════════════════════════════════════════════════
    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending'      => 'قيد الانتظار',
            'processing'   => 'قيد المعالجة',
            'approved'     => 'موافق عليه',
            'rejected'     => 'مرفوض',
            'completed'    => 'مكتمل',
            'cancelled'    => 'ملغي',
            'on_hold'      => 'معلّق',
            'under_review' => 'قيد المراجعة',
            default        => $status ?? 'غير محدد',
        };
    }

    // ═══════════════════════════════════════════════════════════
    // جلب طلبات حسب الفئة
    // ═══════════════════════════════════════════════════════════
    public function byCategory(Request $request, string $category)
    {
        $allowed = ['id_card', 'certificate', 'passport', 'family_card', 'appointment'];

        if (!in_array($category, $allowed)) {
            return response()->json(['message' => 'فئة غير معروفة'], 404);
        }

        $requests = ServiceRequest::where('user_id', $request->user()->id)
            ->where('category', $category)
            ->latest()
            ->get();

        return response()->json([
            'data' => $requests,
        ]);
    }
}