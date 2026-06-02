<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordEnrollmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    if (! is_null($request->user()->password)) {
                        $fail('A password is already set on this account.');
                    }
                },
            ],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return response()->json(['message' => 'Password enrolled successfully.']);
    }
}
