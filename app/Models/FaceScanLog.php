<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceScanLog extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'matched_citizen_id', 'confidence',
        'matched', 'status', 'ip_address', 'user_agent', 'meta',
    ];

    protected $casts = [
        'confidence' => 'float',
        'matched'    => 'boolean',
        'meta'       => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'matched_citizen_id');
    }
}