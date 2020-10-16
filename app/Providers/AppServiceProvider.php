<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Inertia::version(function () {
            return md5_file(public_path('mix-manifest.json'));
        });

        Inertia::share([
            'app' => [
                'baseUrl' => url(''),
                'session_lifetime' => ((int) Config::get('session.lifetime') * 60 * 60), // in seconds
                // 'csrf_token' => function () { return csrf_token(); },
                // 'client_on_desktop' => function () {
                //     return (new Agent())->isDesktop();
                // }
            ],
            // 'auth' => function () {
            //     return Auth::user() ? [
            //             'user' => [
            //                 'id' => Auth::user()->id,
            //                 'name' => Auth::user()->name,
            //                 'roles' => Auth::user()->rolesName(),
            //                 'abilities' => Auth::user()->abilities(),
            //             ]
            //         ]:null;
            // },
            // 'flash' => function () {
            //     return [
            //         'success' => Session::get('success'),
            //         'error' => Session::get('error'),
            //         'data' => Session::get('data'),
            //     ];
            // },
            // 'errors' => function () {
            //     return Session::get('errors') ?
            //             Session::get('errors')->getBag('default')->getMessages() :
            //             (object) [];
            // },
        ]);
    }
}
