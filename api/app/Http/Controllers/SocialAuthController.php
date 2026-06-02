<?php

namespace App\Http\Controllers;

use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect(config('app.frontend_url').'/login?error=social_auth_failed');
        }

        $identity = SocialIdentity::firstOrNew([
            'provider' => $provider,
            'provider_user_id' => $socialiteUser->getId(),
        ]);

        if (! $identity->exists) {
            $user = User::firstOrCreate(
                ['email' => $socialiteUser->getEmail()],
                [
                    'name' => $socialiteUser->getName(),
                    'password' => null,
                ],
            );

            $identity->user_id = $user->id;
            $identity->save();
        } else {
            $user = $identity->user;
        }

        if (is_null($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);

        return redirect(config('app.frontend_url'));
    }
}
