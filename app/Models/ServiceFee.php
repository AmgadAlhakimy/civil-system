<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceFee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'service_fees';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'service_key',   // ✅ أُضيف
        'service_name',
        'category',
        'amount',
        'currency',      // ✅ أُضيف (لأنه NOT NULL مع default YER — لكن يُفضّل تمريره)
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

    // ═══════════════════════════════════════════════════════════
    // ✅ توليد service_key تلقائياً إذا لم يُمرَّر
    // ═══════════════════════════════════════════════════════════
    protected static function booted(): void
    {
        static::creating(function (self $fee): void {
            if (empty($fee->service_key)) {
                $fee->service_key = static::generateUniqueKey(
                    $fee->service_name ?? 'service'
                );
            }
        });

        static::updating(function (self $fee): void {
            // إذا غيّر المستخدم الاسم ولم يكن هناك service_key صريح، لا نغيّره
            // (نحافظ على الاستقرار عبر التحديثات)
        });
    }

    // ═══════════════════════════════════════════════════════════
    // ✅ توليد مفتاح فريد من اسم الخدمة
    // ═══════════════════════════════════════════════════════════
    public static function generateUniqueKey(string $serviceName): string
    {
        // slug من الاسم؛ إن كان عربياً بالكامل → fallback
        $base = Str::slug($serviceName);
        if (empty($base)) {
            $base = 'service';
        }

        // اقتطاع + ضمان الطول
        $base = Str::limit($base, 200, '');

        $key = $base;
        $counter = 1;

        // ضمان الفرادة (UNIQUE)
        while (static::withTrashed()->where('service_key', $key)->exists()) {
            $key = $base . '_' . $counter;
            $counter++;

            // حماية من الحلقة اللانهائية
            if ($counter > 1000) {
                $key = $base . '_' . Str::random(6);
                break;
            }
        }

        return $key;
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