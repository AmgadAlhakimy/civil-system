<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    public $guarded = [];

    protected $casts = [
        'properties' => 'collection',
        'batch_uuid' => 'string',
    ];

    protected static function booted(): void
    {
        static::creating(function (Activity $activity) {
            $user = Auth::user();

            $activity->ip_address = Request::ip();

            $activity->user_agent = Request::userAgent();

            $activity->device_type = self::detectDevice(
                Request::userAgent()
            );

            $activity->browser = self::detectBrowser(
                Request::userAgent()
            );

            $activity->user_role = $user?->roles?->first()?->name;
        });
    }

    protected static function detectDevice(?string $userAgent): string
    {
        $userAgent = strtolower($userAgent ?? '');

        if (str_contains($userAgent, 'tablet')) {
            return 'tablet';
        }

        if (
            str_contains($userAgent, 'mobile') ||
            str_contains($userAgent, 'android') ||
            str_contains($userAgent, 'iphone')
        ) {
            return 'mobile';
        }

        return 'desktop';
    }

    protected static function detectBrowser(?string $userAgent): string
    {
        $userAgent = $userAgent ?? '';

        return match (true) {
            str_contains($userAgent, 'Edg') => 'Edge',
            str_contains($userAgent, 'OPR') => 'Opera',
            str_contains($userAgent, 'Chrome') => 'Chrome',
            str_contains($userAgent, 'Firefox') => 'Firefox',
            str_contains($userAgent, 'Safari') => 'Safari',
            default => 'غير معروف',
        };
    }
}
