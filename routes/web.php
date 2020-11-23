<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AssignRootController;
use App\Http\Controllers\DevelopersController;
use App\Http\Controllers\UsersController;

// Pages
Route::get('/', function () {
    return Inertia::render('Welcome', []);
});

// Register users
Route::middleware('guest')->get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::middleware('guest')->post('register', [RegisterController::class, 'register']);

// Authenticate Users
Route::middleware('guest')->get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::middleware('guest')->post('login', [LoginController::class, 'login']);
Route::middleware('guest')->get('auth/{provider}', [LoginController::class, 'redirectToProvider']);
Route::middleware('guest')->get('auth/{provider}/callback', [LoginController::class, 'handleProviderCallback']);
Route::middleware('auth')->post('logout', [LoginController::class, 'logout']);

// User features
Route::middleware('auth')->get('profile', [UsersController::class, 'profile'])->name('profile');
Route::middleware('guest', 'signed')->get('link-social', [UsersController::class, 'linkSocial'])->name('linkSocial');
// Route::middleware('auth')->get('dashboard', [UsersController::class, 'dashboard'])->name('dashboard');

// Developer features
Route::middleware('auth')->get('developer', [DevelopersController::class, 'developer'])->name('developer');
Route::middleware('auth')->post('developer', [DevelopersController::class, 'apply']);
Route::middleware('auth', 'can:get_user')->get('dashboard', [DevelopersController::class, 'dashboard'])->name('dashboard');

// Assign root
Route::middleware('auth')->get('sudo/{passcode}', AssignRootController::class);
