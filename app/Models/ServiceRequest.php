<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ServiceRequest extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'citizen_id',
        'branch_id',
        'reviewed_by',
        'request_number',
        'service_key',
        'service_name',
        'category',
        'title',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'status',
        'rejection_reason',
        'reviewed_at',
        'data',
        'documents',
    ];

    protected $casts = [
        'data' => 'array',
        'documents' => 'array',
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ═══════════════════════════════════════════════════════════
    // توليد رقم طلب فريد بصيغة REQ-YYYYMMDD-XXXX
    // ═══════════════════════════════════════════════════════════
    public static function generateRequestNumber(string $prefix = 'REQ'): string
    {
        $date = now()->format('Ymd');
        $fullPrefix = "{$prefix}-{$date}-";

        $lastRequest = static::withTrashed()
            ->where('request_number', 'LIKE', $fullPrefix . '%')
            ->orderByDesc('request_number')
            ->first();

        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->request_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $fullPrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // ═══════════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════════
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'id_card'     => 'بطاقة شخصية',
            'certificate' => 'شهادة',
            'passport'    => 'جواز سفر',
            'family_card' => 'بطاقة عائلية',
            'appointment' => 'موعد',
            default       => $this->category ?? '',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'قيد الانتظار',
            'processing' => 'قيد المراجعة',
            'approved'   => 'مقبول',
            'rejected'   => 'مرفوض',
            default      => $this->status ?? '',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'غير مدفوع',
            'paid'    => 'مدفوع',
            'failed'  => 'فشل الدفع',
            default   => $this->payment_status ?? '',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 0) . ' ' . ($this->currency ?? 'YER');
    }

    // ═══════════════════════════════════════════════════════════
    // Relations
    // ═══════════════════════════════════════════════════════════
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'citizen_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}