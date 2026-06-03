<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileRegistrationController extends Controller
{
    public function store(Request $request, CreateNewUser $createNewUser): JsonResponse
    {
        $user = $createNewUser->create($request->all());

        event(new Registered($user));

        return response()->json(
            ['message' => 'Registration successful. Please verify your email.'],
            201,
        );
    }
}
