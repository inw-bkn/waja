<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('guest');
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth');
Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/test-api/user', function (App\Contracts\AuthUserAPI $api) {
    return $api->getUser(request()->input('login'));
});

Route::get('/test-api/authenticate', function (App\Contracts\AuthUserAPI $api) {
    return $api->authenticate(request()->input('login'), request()->input('password'));
});
