<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * الجدول المرتبط
     */
    protected $table = 'service_fees';

    /**
     * المفتاح الأساسي
     */
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * الحقول القابلة للتعيين الجماعي
     */
    protected $fillable = [
        'service_key',
        'service_name',
        'category',
        'amount',
        'currency',
        'is_active',
        'notes',
        'updated_by',
    ];

    /**
     * التحويلات
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ═══════════════════════════════════════════════════════════
    // العلاقات
    // ═══════════════════════════════════════════════════════════

    /**
     * من آخر من عدّل السعر
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ═══════════════════════════════════════════════════════════
    // Scopes (استعلامات جاهزة)
    // ═══════════════════════════════════════════════════════════

    /**
     * فقط الخدمات المفعّلة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * حسب الفئة
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ═══════════════════════════════════════════════════════════
    // Accessors & Mutators
    // ═══════════════════════════════════════════════════════════

    /**
     * السعر منسّق مع العملة
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 0) . ' ' . $this->currency;
    }

    // ═══════════════════════════════════════════════════════════
    // Static Helpers
    // ═══════════════════════════════════════════════════════════

    /**
     * جلب رسوم خدمة بمفتاحها
     */
    public static function getByKey(string $serviceKey): ?self
    {
        return static::where('service_key', $serviceKey)
            ->where('is_active', true)
            ->first();
    }

    /**
     * جلب كل الرسوم المفعّلة كمصفوفة key => amount
     */
    public static function allAsKeyValue(): array
    {
        return static::active()
            ->pluck('amount', 'service_key')
            ->toArray();
    }

    /**
     * جلب كل الرسوم المفعّلة كمصفوفة كاملة
     */
    public static function allActive(): array
    {
        return static::active()
            ->get()
            ->map(fn ($fee) => [
                'service_key' => $fee->service_key,
                'service_name' => $fee->service_name,
                'category' => $fee->category,
                'amount' => (float) $fee->amount,
                'currency' => $fee->currency,
            ])
            ->toArray();
    }
}