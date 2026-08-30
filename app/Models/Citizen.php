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

    use HasFactory, SoftDeletes, HasUuids, LogsActivity;

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
