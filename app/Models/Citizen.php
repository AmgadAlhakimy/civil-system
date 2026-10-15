<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Citizen extends Model
{
    use HasFactory, SoftDeletes, HasUuids, LogsActivity;

    protected $fillable = [
        'first_name',
        'father_name',
        'middle_name',
        'last_name',
        'mother_name',

        'first_name_en',
        'father_name_en',
        'middle_name_en',
        'last_name_en',

        'birth_date',
        'birth_place',
        'birth_place_en',

        'blood_type',
        'gender',
        'marital_status',

        'occupation',
        'occupation_en',

        'address',
        'phone',
        'email',
        'photo',
        'face_data',
        'fingerprint_data',
        'is_active',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
    ];

    protected $attributes = [
        'verification_status' => 'pending',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function deathCertificates(): HasMany
    {
        return $this->hasMany(DeathCertificate::class, 'deceased_id');
    }
}
