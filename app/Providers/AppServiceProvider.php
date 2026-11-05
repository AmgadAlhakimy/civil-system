<?php

namespace App\Providers;

use App\Listeners\UpdateLastLogin;
use App\Services\FaceEmbeddingService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ✅ تسجيل FaceEmbeddingService مع القيم من config
        $this->app->singleton(FaceEmbeddingService::class, function ($app) {
            return new FaceEmbeddingService(
                config('services.face_ai.url', 'http://127.0.0.1:8001'),
                config('services.face_ai.key'),
            );
        });
    }

    public function boot(): void
    {
        Event::listen(
            Login::class,
            UpdateLastLogin::class
        );

        // ✅ Gate لصلاحية التعرف على الوجه
        Gate::define('use-face-recognition', function ($user) {
            return $user->hasRole('query_officer') && $user->status === 'active';
        });
    }
}