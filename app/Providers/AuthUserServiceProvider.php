<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Contracts\AuthUserAPI', config('app.AUTH_USER_PROVIDER'));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
