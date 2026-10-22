<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceRequestCreated extends Notification
{
    use Queueable;

    public function __construct(public ServiceRequest $serviceRequest) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $reqNum = $this->serviceRequest->request_number;
        $serviceName = $this->serviceRequest->service_name;

        return [
            'title' => '📝 تم إدخال البيانات',
            'body' => "طلبك رقم {$reqNum} ({$serviceName}) تم إنشاؤه بنجاح.",
            'stage' => 'created',
            'icon' => 'description',
            'color' => 'info',
            'request_id' => $this->serviceRequest->id,
            'request_number' => $reqNum,
            'service_name' => $serviceName,
            'new_status' => $this->serviceRequest->status,
        ];
    }
}