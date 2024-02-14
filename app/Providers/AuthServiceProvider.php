<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $url = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            preg_match('/\/verify\/(\d+)\//', $url, $matches);
            $id = $matches[1];

            preg_match('/\/verify\/\d+\/([^?]+)/', $url, $matches);
            $hash = $matches[1];

            parse_str(parse_url($url, PHP_URL_QUERY), $query_params);
            $expires = $query_params['expires'];
            $signature = $query_params['signature'];

            return config('app.frontend_url')."/verify-email/$id/$hash?expires=$expires&signature=$signature";
        });
    }
}
