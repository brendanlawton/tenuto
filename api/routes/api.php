<?php

use App\Http\Controllers\PasswordEnrollmentController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/user', fn (Request $request) => new UserResource($request->user()));
    Route::post('/user/password', [PasswordEnrollmentController::class, 'store']);
});
