<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Citizen extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    // الحقول المسموح بتعبئتها (Mass Assignment)
    // لاحظ أننا لم نضف full_name لأنه يتم توليده تلقائياً في قاعدة البيانات
    protected $fillable = [
        'national_id',
        'first_name',
        'middle_name',
        'last_name',
        'father_name',
        'mother_name',
        'birth_date',
        'birth_place',
        'gender',
        'marital_status',
        'occupation',
        'address',
        'phone',
        'email',
        'photo',
        'face_data',
        'fingerprint_data',
        'is_active',
        'verified_at',
        'verified_by',
    ];

    // تحديد أنواع بعض الحقول (Casting) لتسهيل التعامل معها برمجياً
    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * علاقة الموظف/المستخدم الذي قام بالتحقق من بيانات هذا المواطن
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
