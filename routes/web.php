<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UsersController;

// Pages
Route::get('/', function () {
    return Inertia::render('Welcome', []);
});

// Register users
Route::get('register', [RegisterController::class, 'showRegisterForm'])->middleware('guest')->name('register');
Route::post('register', [RegisterController::class, 'register'])->middleware('guest');

// Authenticate Users
Route::get('login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('guest');
Route::get('auth/{provider}', [LoginController::class, 'redirectToProvider']);
Route::get('auth/{provider}/callback', [LoginController::class, 'handleProviderCallback']);
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth');

// User features
Route::get('profile', [UsersController::class, 'profile'])->middleware('auth')->name('profile');
Route::get('dashboard', [UsersController::class, 'dashboard'])->middleware('auth')->name('dashboard');

Route::get('/test-api/user', function (App\Contracts\AuthUserAPI $api) {
    return $api->getUser(request()->input('login'));
});

Route::get('/test-api/authenticate', function (App\Contracts\AuthUserAPI $api) {
    return $api->authenticate(request()->input('login'), request()->input('password'));
});
