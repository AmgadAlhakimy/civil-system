<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceRequestStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public ServiceRequest $serviceRequest,
        public string $oldStatus,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $stage = $this->resolveStage();
        [$title, $body, $icon, $color] = $this->resolveContent($stage);

        return [
            'title' => $title,
            'body' => $body,
            'stage' => $stage,              // 🆕 للمييز في Flutter
            'icon' => $icon,
            'color' => $color,
            'request_id' => $this->serviceRequest->id,
            'request_number' => $this->serviceRequest->request_number,
            'service_name' => $this->serviceRequest->service_name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->serviceRequest->status,
            'rejection_reason' => $this->serviceRequest->rejection_reason,
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // تحديد المرحلة
    // ═══════════════════════════════════════════════════════════
    private function resolveStage(): string
    {
        return match ($this->serviceRequest->status) {
            'processing' => 'processing',
            'approved' => 'approved',
            'rejected' => 'rejected',
            default => 'status_change',
        };
    }

    // ═══════════════════════════════════════════════════════════
    // محتوى حسب المرحلة
    // ═══════════════════════════════════════════════════════════
    private function resolveContent(string $stage): array
    {
        $reqNum = $this->serviceRequest->request_number;
        $serviceName = $this->serviceRequest->service_name;

        switch ($stage) {
            case 'processing':
                return [
                    '👀 جاري مراجعة الوثائق',
                    "موظف الأحوال المدنية يراجع مستندات طلبك رقم {$reqNum}.",
                    'hourglass_empty',
                    'warning',
                ];

            case 'approved':
                return [
                    '✅ تمت الموافقة على الطلب',
                    "تمت الموافقة على طلبك رقم {$reqNum} ({$serviceName}).",
                    'check_circle',
                    'success',
                ];

            case 'rejected':
                $reason = $this->serviceRequest->rejection_reason
                    ?? 'لم يتم تسجيل سبب';
                return [
                    '❌ تم رفض الطلب',
                    "تم رفض طلبك رقم {$reqNum}.\nالسبب: {$reason}",
                    'cancel',
                    'danger',
                ];

            default:
                return [
                    '📝 تحديث حالة الطلب',
                    "الطلب {$reqNum} تغيّر من {$this->getLabel($this->oldStatus)} إلى {$this->getLabel($this->serviceRequest->status)}.",
                    'notification_important',
                    'info',
                ];
        }
    }

    private function getLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            default => $status,
        };
    }
}