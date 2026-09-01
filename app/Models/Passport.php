<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Passport extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'citizen_id',
        'passport_number',
        'issue_date',
        'expiry_date',
        'type',
        'status',
        'notes',
        'qr_code',
        'print_count',
        'issued_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
        'print_count' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | مدة صلاحية الجواز
    |--------------------------------------------------------------------------
    */

    public function validityYears(): int
    {
        return 5;
    }

    /*
    |--------------------------------------------------------------------------
    | التحقق من انتهاء الجواز
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expiry_date !== null
            && $this->expiry_date->isPast();
    }

    /*
    |--------------------------------------------------------------------------
    | التحقق من أن الجواز فعال
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active'
            && ! $this->isExpired();
    }

    /*
    |--------------------------------------------------------------------------
    | تحديث الحالة حسب تاريخ الانتهاء
    |--------------------------------------------------------------------------
    */

    public function updateExpirationStatus(): void
    {
        if (
            $this->expiry_date !== null
            && $this->expiry_date->isPast()
            && in_array($this->status, ['approved', 'active'], true)
        ) {
            $this->update([
                'status' => 'expired',
            ]);
        }
    }
}
