<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BirthCertificate extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'child_id',
        'father_id',
        'mother_id',
        'branch_id',
        'issue_date',
        'status',
        'notes',
        'rejection_reason',
        'qr_code',
        'print_count',
        'issued_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
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

    public function child(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'child_id');
    }

    public function father(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'father_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'mother_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
