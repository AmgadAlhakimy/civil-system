<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FamilyCard extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'head_id',
        'branch_id',
        'card_number',
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

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
        'print_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (FamilyCard $card): void {
            if (filled($card->card_number)) {
                return;
            }

            do {
                $cardNumber = '';

                for ($i = 0; $i < 11; $i++) {
                    $cardNumber .= (string) random_int(0, 9);
                }
            } while (
                static::withTrashed()
                    ->where('card_number', $cardNumber)
                    ->exists()
            );

            $card->card_number = $cardNumber;
        });
    }
    public function validityYears(): int
    {
        return 10;
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'head_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'family_card_id');
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
