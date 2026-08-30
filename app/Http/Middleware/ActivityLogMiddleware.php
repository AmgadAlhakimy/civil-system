<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $enabled = app(SettingService::class)->get(
            'activity_log_enabled',
            true
        );

        if ($enabled) {
            activity()->enableLogging();
        } else {
            activity()->disableLogging();
        }

        return $next($request);
    }
}
