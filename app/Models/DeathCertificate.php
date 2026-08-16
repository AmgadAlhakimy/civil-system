<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeathCertificate extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'deceased_id',
        'death_date',
        'cause_of_death',
        'place_of_death',
        'certificate_number',
        'issue_date',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'qr_code',
        'print_count',
        'issued_by',
    ];

    protected $casts = [
        'death_date' => 'date',
        'issue_date' => 'date',
        'approved_at' => 'datetime',
        'print_count' => 'integer',
    ];

    public function deceased(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'deceased_id');
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
