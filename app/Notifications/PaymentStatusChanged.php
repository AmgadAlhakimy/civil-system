<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public ServiceRequest $serviceRequest,
        public string $oldPaymentStatus,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $reqNum = $this->serviceRequest->request_number;
        $amount = number_format((float) $this->serviceRequest->amount, 0)
            . ' ' . $this->serviceRequest->currency;

        [$title, $body, $icon, $color] = match ($this->serviceRequest->payment_status) {
            'paid' => [
                '💰 تم الدفع بنجاح',
                "تم دفع مبلغ {$amount} للطلب رقم {$reqNum}.",
                'check_circle',
                'success',
            ],
            'failed' => [
                '⚠️ فشل الدفع',
                "لم يتم إتمام دفع الطلب رقم {$reqNum}. يرجى المحاولة مرة أخرى.",
                'error',
                'danger',
            ],
            default => [
                '💳 تحديث حالة الدفع',
                "حالة دفع الطلب {$reqNum} تغيّرت إلى: {$this->getLabel($this->serviceRequest->payment_status)}.",
                'payment',
                'info',
            ],
        };

        return [
            'title' => $title,
            'body' => $body,
            'stage' => 'payment',
            'icon' => $icon,
            'color' => $color,
            'request_id' => $this->serviceRequest->id,
            'request_number' => $reqNum,
            'service_name' => $this->serviceRequest->service_name,
            'old_payment_status' => $this->oldPaymentStatus,
            'new_payment_status' => $this->serviceRequest->payment_status,
        ];
    }

    private function getLabel(string $status): string
    {
        return match ($status) {
            'paid' => 'مدفوع',
            'pending' => 'بانتظار الدفع',
            'failed' => 'فشل',
            default => $status,
        };
    }
}