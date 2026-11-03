<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'service_fees';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'service_name',
        'category',
        'amount',
        'is_active',
        'notes',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2) . ' ريال يمني';
    }

    public static function getById(string $id): ?self
    {
        return static::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->first();
    }

    public static function allAsKeyValue(): array
    {
        return static::active()
            ->pluck('amount', 'id')
            ->toArray();
    }

    public static function allActive(): array
    {
        return static::active()
            ->get()
            ->map(fn (self $fee): array => [
                'id' => $fee->id,
                'service_name' => $fee->service_name,
                'category' => $fee->category,
                'amount' => (float) $fee->amount,
            ])
            ->toArray();
    }
}
