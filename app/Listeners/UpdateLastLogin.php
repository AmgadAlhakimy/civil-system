<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class UpdateLastLogin
{
    public function handle(Login $event): void
    {
        $event->user->disableLogging();

        try {
            $event->user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
        } finally {
            $event->user->enableLogging();
        }
    }
}
