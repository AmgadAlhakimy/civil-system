<?php

namespace App\Observers;

use App\Models\ServiceRequest;
use App\Notifications\PaymentStatusChanged;
use App\Notifications\ServiceRequestCreated;
use App\Notifications\ServiceRequestStatusChanged;

class ServiceRequestObserver
{
    /**
     * Send a notification when a new service request is created.
     */
    public function created(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->user?->notify(
            new ServiceRequestCreated($serviceRequest)
        );
    }

    /**
     * Send notifications when request or payment status changes.
     */
    public function updated(ServiceRequest $serviceRequest): void
    {
        if ($serviceRequest->wasChanged('status')) {
            $oldStatus = $serviceRequest->getOriginal('status');

            if ($oldStatus !== null) {
                $serviceRequest->user?->notify(
                    new ServiceRequestStatusChanged($serviceRequest, $oldStatus)
                );
            }
        }

        if ($serviceRequest->wasChanged('payment_status')) {
            $oldPaymentStatus = $serviceRequest->getOriginal('payment_status');

            if ($oldPaymentStatus !== null) {
                $serviceRequest->user?->notify(
                    new PaymentStatusChanged($serviceRequest, $oldPaymentStatus)
                );
            }
        }
    }
}
