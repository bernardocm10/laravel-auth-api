<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->letters()->numbers()->mixedCase());

        // A URL enviada no e-mail aponta para o frontend, que por sua vez
        // chama POST /api/auth/reset-password com o token recebido.
        ResetPassword::createUrlUsing(function (object $user, string $token): string {
            $base = rtrim(config('app.frontend_url', config('app.url')), '/');

            return $base . '/reset-password?token=' . $token
                . '&email=' . urlencode($user->getEmailForPasswordReset());
        });
    }
}
