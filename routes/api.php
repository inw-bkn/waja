<?php

use App\Http\Controllers\Auth\APIRegisterController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/users/{userId}', [UsersController::class, 'show']);
Route::middleware('auth:sanctum')->post('/register', APIRegisterController::class);
Route::middleware('auth:sanctum')->post('/request-link-social', [UsersController::class, 'requestLinkSocial']);
