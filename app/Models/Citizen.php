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

    public function identityCards(): HasMany
    {
        return $this->hasMany(IdentityCard::class, 'citizen_id');
    }

    public function passports(): HasMany
    {
        return $this->hasMany(Passport::class, 'citizen_id');
    }

    public function familyCards(): HasMany
    {
        return $this->hasMany(FamilyCard::class, 'head_id');
    }

    public function familyMemberships(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'citizen_id');
    }

    public function birthCertificatesAsChild(): HasMany
    {
        return $this->hasMany(BirthCertificate::class, 'child_id');
    }

    public function birthCertificatesAsFather(): HasMany
    {
        return $this->hasMany(BirthCertificate::class, 'father_id');
    }

    public function birthCertificatesAsMother(): HasMany
    {
        return $this->hasMany(BirthCertificate::class, 'mother_id');
    }

    public function deathCertificates(): HasMany
    {
        return $this->hasMany(DeathCertificate::class, 'deceased_id');
    }

    public function hasRelatedDocuments(): bool
    {
        return $this->identityCards()->exists()
            || $this->passports()->exists()
            || $this->familyCards()->exists()
            || $this->familyMemberships()->exists()
            || $this->birthCertificatesAsChild()->exists()
            || $this->birthCertificatesAsFather()->exists()
            || $this->birthCertificatesAsMother()->exists()
            || $this->deathCertificates()->exists();
    }

    public function delete(): ?bool
    {
        if (! $this->exists) {
            return null;
        }

        if ($this->hasRelatedDocuments()) {
            return false;
        }

        return parent::delete();
    }
}
