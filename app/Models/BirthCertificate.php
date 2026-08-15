<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthCertificate extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'child_id',
        'father_id',
        'mother_id',
        'certificate_number',
        'issue_date',
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
        'approved_at' => 'datetime',
    ];

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

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
