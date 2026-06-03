<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MobileTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (is_null(Auth::user()->email_verified_at)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Your email address is not verified.'],
            ]);
        }

        $token = Auth::user()->createToken('mobile')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
