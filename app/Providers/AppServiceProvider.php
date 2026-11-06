<?php

namespace App\Providers;

use App\Listeners\UpdateLastLogin;
use App\Services\FaceEmbeddingService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
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

        Event::listen(Login::class, function (Login $event): void {
            activity()
                ->causedBy($event->user)
                ->event('login')
                ->log('تم تسجيل الدخول إلى النظام.');
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if (! $event->user) {
                return;
            }

            activity()
                ->causedBy($event->user)
                ->event('logout')
                ->log('تم تسجيل الخروج من النظام.');
        });

        Event::listen(Failed::class, function (Failed $event): void {
            $email = $event->credentials['email']
                ?? $event->credentials['username']
                ?? null;

            activity()
                ->event('failed_login')
                ->withProperties([
                    'email' => $email,
                ])
                ->log(
                    filled($email)
                        ? "فشلت محاولة تسجيل الدخول باستخدام البريد الإلكتروني: {$email}."
                        : 'فشلت محاولة تسجيل الدخول إلى النظام.'
                );
        });

        Gate::define('use-face-recognition', function ($user) {
            return $user->hasRole('query_officer') && $user->status === 'active';
        });
    }
}
