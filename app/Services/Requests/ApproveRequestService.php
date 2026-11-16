<?php

namespace App\Services\Requests;

use App\Models\RequestStatusHistory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveRequestService
{
    /**
     * الحالات المسموح بها لكل انتقال
     */
    private const ALLOWED_TRANSITIONS = [
        'pending'    => ['processing', 'approved', 'rejected', 'cancelled'],
        'processing' => ['approved', 'rejected', 'cancelled'],
        'approved'   => ['printing', 'cancelled'],
        'printing'   => ['ready'],
        'ready'      => ['delivered'],
        'delivered'  => [],
        'rejected'   => [],
        'cancelled'  => [],
    ];

    /**
     * التسميات العربية للحالات
     */
    private const STATUS_LABELS = [
        'pending'    => 'قيد الانتظار',
        'processing' => 'قيد المراجعة',
        'approved'   => 'مقبول',
        'rejected'   => 'مرفوض',
        'printing'   => 'قيد الطباعة',
        'ready'      => 'جاهز للاستلام',
        'delivered'  => 'تم التسليم',
        'cancelled'  => 'ملغي',
    ];

    /**
     * اعتماد الطلب: تخزين البيانات + تحديث الحالة + إشعار
     */
    public function approve(ServiceRequest $request, User $reviewer, ?string $notes = null): array
    {
        // 1. تحقق من الحالة
        if (!in_array($request->status, ['pending', 'processing'])) {
            return [
                'success' => false,
                'error' => 'لا يمكن اعتماد طلب بحالة: ' . $this->label($request->status),
            ];
        }

        // 2. ابحث عن handler
        $handler = ServiceResolver::for($request->service_key);

        if (!$handler) {
            return [
                'success' => false,
                'error' => 'لا يوجد معالج لهذه الخدمة: ' . $request->service_key,
            ];
        }

        // 3. نفّذ handler
        $result = $handler->approve($request, $reviewer);

        if (!$result['success']) {
            return $result;
        }

        // 4. تحديث حالة الطلب
        DB::transaction(function () use ($request, $reviewer, $notes, $result) {
            $oldStatus = $request->status;

            $request->update([
                'status' => 'approved',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            // 5. سجل التاريخ
            RequestStatusHistory::create([
                'service_request_id' => $request->id,
                'from_status' => $oldStatus,
                'to_status' => 'approved',
                'changed_by' => $reviewer->id,
                'notes' => $notes ?? 'تم الاعتماد',
                'meta' => [
                    'citizen_id' => $result['citizen_id'] ?? null,
                    'service_record_id' => $result['service_record_id'] ?? null,
                ],
            ]);
        });

        // 6. إرسال إشعار
        $this->sendNotification($request);

        return [
            'success' => true,
            'citizen_id' => $result['citizen_id'] ?? null,
            'service_record_id' => $result['service_record_id'] ?? null,
        ];
    }

    /**
     * رفض الطلب
     */
    public function reject(ServiceRequest $request, User $reviewer, string $reason): array
    {
        if (!in_array($request->status, ['pending', 'processing'])) {
            return [
                'success' => false,
                'error' => 'لا يمكن رفض طلب بحالة: ' . $this->label($request->status),
            ];
        }

        DB::transaction(function () use ($request, $reviewer, $reason) {
            $oldStatus = $request->status;

            $request->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            RequestStatusHistory::create([
                'service_request_id' => $request->id,
                'from_status' => $oldStatus,
                'to_status' => 'rejected',
                'changed_by' => $reviewer->id,
                'notes' => $reason,
            ]);
        });

        $this->sendNotification($request);

        return ['success' => true];
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(
        ServiceRequest $request,
        User $reviewer,
        string $newStatus,
        ?string $notes = null,
    ): array {
        $current = $request->status;

        // 1. تحقق من أن الحالة الجديدة معروفة
        if (!array_key_exists($newStatus, self::STATUS_LABELS)) {
            return [
                'success' => false,
                'error' => "حالة غير معروفة: {$newStatus}",
            ];
        }

        // 2. تحقق من أن الانتقال مسموح
        $allowed = self::ALLOWED_TRANSITIONS[$current] ?? [];
        if (!in_array($newStatus, $allowed, true)) {
            return [
                'success' => false,
                'error' => sprintf(
                    'انتقال غير مسموح: %s → %s',
                    $this->label($current),
                    $this->label($newStatus),
                ),
            ];
        }

        // 3. نفّذ التحديث داخل معاملة
        try {
            DB::transaction(function () use ($request, $reviewer, $current, $newStatus, $notes) {
                $updates = ['status' => $newStatus];

                if ($newStatus === 'approved') {
                    $updates['reviewed_by'] = $reviewer->id;
                    $updates['reviewed_at'] = now();
                }

                $request->update($updates);

                RequestStatusHistory::create([
                    'service_request_id' => $request->id,
                    'from_status' => $current,
                    'to_status' => $newStatus,
                    'changed_by' => $reviewer->id,
                    'notes' => $notes,
                ]);
            });

            // 4. إرسال إشعار
            $this->sendNotification($request);

            return ['success' => true];
        } catch (\Throwable $e) {
            Log::error('updateStatus failed', [
                'error' => $e->getMessage(),
                'request_id' => $request->id,
                'from' => $current,
                'to' => $newStatus,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * إرسال إشعار للمواطن
     */
    private function sendNotification(ServiceRequest $request): void
    {
        try {
            $statusLabel = $this->notificationMessage($request->status);

            $body = "طلبك #{$request->request_number} - {$statusLabel}";

            if ($request->status === 'rejected' && $request->rejection_reason) {
                $body .= "\nالسبب: {$request->rejection_reason}";
            }

            \DB::table('notifications')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\RequestStatusChanged',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $request->user_id,
                'data' => json_encode([
                    'title' => 'تحديث حالة الطلب',
                    'body' => $body,
                    'request_id' => $request->id,
                    'request_number' => $request->request_number,
                    'status' => $request->status,
                    'status_label' => $this->label($request->status),
                ], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'request_id' => $request->id,
            ]);
        }
    }

    /**
     * التسمية العربية للحالة
     */
    private function label(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? $status;
    }

    /**
     * رسالة الإشعار حسب الحالة
     */
    private function notificationMessage(string $status): string
    {
        return match ($status) {
            'approved'   => 'تم اعتماد طلبك',
            'rejected'   => 'تم رفض طلبك',
            'processing' => 'طلبك قيد المراجعة',
            'printing'   => 'طلبك قيد الطباعة',
            'ready'      => 'طلبك جاهز للاستلام',
            'delivered'  => 'تم تسليم طلبك',
            'cancelled'  => 'تم إلغاء طلبك',
            default      => 'تم تحديث حالة طلبك',
        };
    }
}