<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVerification extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'service_request_id',
        'document_reference',
        'document_type',
        'sha256',
        'mime_type',
        'file_size',
        'image_width',
        'image_height',
        'status',
        'checks',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'image_width' => 'integer',
            'image_height' => 'integer',
            'checks' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }
}
