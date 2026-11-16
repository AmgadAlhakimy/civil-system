<?php

namespace App\Services\Requests;

use App\Models\ServiceRequest;
use App\Models\User;

interface ServiceRequestHandler
{
    /**
     * ينفذ الخدمة عند الاعتماد
     *
     * @return array{
     *     success: bool,
     *     citizen_id?: string,
     *     service_record_id?: string,
     *     error?: string
     * }
     */
    public function approve(ServiceRequest $request, User $reviewer): array;

    /**
     * اسم الجدول الرئيسي للخدمة
     */
    public function getServiceTable(): string;
}