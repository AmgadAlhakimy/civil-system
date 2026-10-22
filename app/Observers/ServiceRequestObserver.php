<?php

namespace App\Observers;

use App\Models\ServiceRequest;
use App\Notifications\PaymentStatusChanged;
use App\Notifications\ServiceRequestCreated;
use App\Notifications\ServiceRequestStatusChanged;

class ServiceRequestObserver
{
    // ═══════════════════════════════════════════════════════════
    // عند إنشاء طلب جديد → إشعار "تم إدخال البيانات"
    // ═══════════════════════════════════════════════════════════
    public function created(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->user?->notify(
            new ServiceRequestCreated($serviceRequest)
        );
    }

    // ═══════════════════════════════════════════════════════════
    // عند التحديث → إشعارات حسب التغيير
    // ═══════════════════════════════════════════════════════════
    public function updated(ServiceRequest $serviceRequest): void
    {
        // ✅ 1. تغيير الحالة
        if ($serviceRequest->isDirty('status')) {
            $oldStatus = $serviceRequest->getOriginal('status');

            // لا نُرسل إشعاراً إذا كان تغييراً أولياً (pending → pending)
            if ($oldStatus !== null) {
                $serviceRequest->user?->notify(
                    new ServiceRequestStatusChanged($serviceRequest, $oldStatus)
                );
            }
        }

        // ✅ 2. تغيير حالة الدفع
        if ($serviceRequest->isDirty('payment_status')) {
            $oldPayment = $serviceRequest->getOriginal('payment_status');

            if ($oldPayment !== null) {
                $serviceRequest->user?->notify(
                    new PaymentStatusChanged($serviceRequest, $oldPayment)
                );
            }
        }
    }
}