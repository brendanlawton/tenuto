<?php

use App\Http\Controllers\MobileRegistrationController;
use App\Http\Controllers\MobileSocialTokenController;
use App\Http\Controllers\MobileTokenController;
use App\Http\Controllers\PasswordEnrollmentController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [MobileRegistrationController::class, 'store']);
    Route::post('/auth/token', [MobileTokenController::class, 'store']);
    Route::post('/auth/{provider}/token', [MobileSocialTokenController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn (Request $request) => new UserResource($request->user()));
        Route::post('/user/password', [PasswordEnrollmentController::class, 'store']);
    });
});
