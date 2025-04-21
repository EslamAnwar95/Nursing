<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Grant\PasswordGrant;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use DateInterval;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Enable password grant type manually (for Laravel Passport 12+)
        $passwordGrant = new PasswordGrant(
            app(UserRepository::class),
            app(RefreshTokenRepository::class)
        );

        $passwordGrant->setRefreshTokenTTL(new DateInterval('P30D'));

        

        Passport::enablePasswordGrant($passwordGrant);


        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
