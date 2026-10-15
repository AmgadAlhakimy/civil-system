<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class IdentityCard extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'citizen_id',
        'national_id',
        'branch_id',
        'issue_date',
        'expiry_date',
        'status',
        'notes',
        'rejection_reason',
        'print_count',
        'issued_by',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'approved_at' => 'datetime',
            'print_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (IdentityCard $card): void {
            if (filled($card->national_id)) {
                return;
            }

            do {
                $nationalId = '';

                for ($i = 0; $i < 11; $i++) {
                    $nationalId .= (string) random_int(0, 9);
                }
            } while (
                static::withTrashed()
                    ->where('national_id', $nationalId)
                    ->exists()
            );

            $card->national_id = $nationalId;
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class);
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

    public function validityYears(): int
    {
        return 10;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date !== null
            && $this->expiry_date->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && ! $this->isExpired();
    }

    public function updateExpirationStatus(): void
    {
        if (
            $this->expiry_date !== null
            && $this->expiry_date->isPast()
            && $this->status === 'active'
        ) {
            $this->update([
                'status' => 'expired',
            ]);
        }
    }
}
