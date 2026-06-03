<?php

namespace App\Actions;

use App\Models\SocialIdentity;
use App\Models\User;

class ResolveSocialUser
{
    public function handle(string $provider, string $providerUserId, string $email, string $name): User
    {
        $identity = SocialIdentity::firstOrNew([
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
        ]);

        if (! $identity->exists) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => null],
            );

            $identity->user_id = $user->id;
            $identity->save();
        } else {
            $user = $identity->user;
        }

        if (is_null($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return $user;
    }
}
