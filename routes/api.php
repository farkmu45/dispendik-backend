<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\RecapController;
use Illuminate\Support\Facades\Route;


Route::get('institutions', [InstitutionController::class, 'index']);


Route::get('recap', [RecapController::class, 'index'])
    ->middleware('auth:sanctum')->name('recap.get');


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('activities', ActivityController::class);
    Route::apiResource('institutions', InstitutionController::class)->except(['show',  'index']);
    Route::get('profile', [AuthController::class, 'getProfile'])->name('get.profile');
    Route::put('profile', [AuthController::class, 'updateProfile'])->name('update.profile');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
});
