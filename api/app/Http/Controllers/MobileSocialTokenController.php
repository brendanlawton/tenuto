<?php

namespace App\Http\Controllers;

use App\Actions\ResolveSocialUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class MobileSocialTokenController extends Controller
{
    public function store(string $provider, Request $request, ResolveSocialUser $resolveSocialUser): JsonResponse
    {
        $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $tokenInfo = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);

        $validAudiences = [
            config('services.google.ios_client_id'),
            config('services.google.client_id'),
        ];

        if ($tokenInfo->failed() || ! in_array($tokenInfo->json('aud'), $validAudiences)) {
            throw ValidationException::withMessages([
                'id_token' => ['The provided token is invalid.'],
            ]);
        }

        $user = $resolveSocialUser->handle(
            $provider,
            $tokenInfo->json('sub'),
            $tokenInfo->json('email'),
            $tokenInfo->json('name'),
        );

        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
        ]);
    }
}
