<?php

namespace App\Models;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;  // ✅ جديد
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasRoles, LogsActivity, HasApiTokens;

    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone',
        'password',
        'otp_code',           // ✅ جديد
        'otp_expires_at',     // ✅ جديد
        'is_verified',        // ✅ جديد
        'device_token',       // ✅ جديد
        'biometric_enabled',  // ✅ جديد
        'notifications_enabled', // ✅ جديد
        'branch_id',
        'status',
        'last_login_at',
        'last_login_ip',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',           // ✅ إخفاء OTP
    ];

    protected $attributes = [
        'status' => 'active',
        'is_verified' => false,
        'biometric_enabled' => false,
        'notifications_enabled' => true,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_verified' => 'boolean',
            'biometric_enabled' => 'boolean',
            'notifications_enabled' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function managedBranches(): HasMany
    {
        return $this->hasMany(Branch::class, 'manager_id');
    }

    public function canBeDeleted(): bool
    {
        return ! $this->managedBranches()->exists();
    }


    public function initials(): string
    {
        $name = $this->full_name ?? $this->name ?? '';

        return collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->implode('');
    }
}
