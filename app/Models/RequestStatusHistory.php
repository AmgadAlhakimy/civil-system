<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestStatusHistory extends Model
{
    use HasUuids;

    protected $table = 'request_status_history';  // ← أضف هذا

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'service_request_id',
        'from_status',
        'to_status',
        'changed_by',
        'notes',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->to_status) {
            'pending'     => 'قيد الانتظار',
            'processing'  => 'قيد المراجعة',
            'approved'    => 'مقبول',
            'rejected'    => 'مرفوض',
            'printing'    => 'قيد الطباعة',
            'ready'       => 'جاهز للاستلام',
            'delivered'   => 'تم التسليم',
            'cancelled'   => 'ملغي',
            default       => $this->to_status,
        };
    }
}