<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;


Route::get('/', function () {
    return Inertia::render('Welcome', []);
});
Route::get('/about', function () {
    sleep(2);
    return Inertia::render('About', []);
});

Route::get('login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('guest');
Route::get('auth/telegram', function () { return view('telegram-auth'); });
Route::get('auth/{provider}', [LoginController::class, 'redirectToProvider']);
Route::get('auth/{provider}/callback', [LoginController::class, 'handleProviderCallback']);
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth');
Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('register', [RegisterController::class, 'showRegisterForm'])->middleware('guest')->name('register');
Route::post('register', [RegisterController::class, 'register'])->middleware('guest')->name('register.post');

Route::get('/test-api/user', function (App\Contracts\AuthUserAPI $api) {
    return $api->getUser(request()->input('login'));
});

Route::get('/test-api/authenticate', function (App\Contracts\AuthUserAPI $api) {
    return $api->authenticate(request()->input('login'), request()->input('password'));
});
