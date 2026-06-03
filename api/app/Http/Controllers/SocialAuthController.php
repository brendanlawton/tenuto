<?php

namespace App\Http\Controllers;

use App\Actions\ResolveSocialUser;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, ResolveSocialUser $resolveSocialUser): RedirectResponse
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect(config('app.frontend_url').'/login?error=social_auth_failed');
        }

        $user = $resolveSocialUser->handle(
            $provider,
            $socialiteUser->getId(),
            $socialiteUser->getEmail(),
            $socialiteUser->getName(),
        );

        Auth::login($user);

        return redirect(config('app.frontend_url'));
    }
}
