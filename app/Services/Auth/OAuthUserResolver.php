<?php

namespace App\Services\Auth;

use App\Models\User;
use Laravel\Socialite\Two\User as OAuthUser;

class OAuthUserResolver
{
    public function resolve(OAuthUser $oauthUser): User
    {
        $authId = (string) $oauthUser->getId();
        $authName = (string) $oauthUser->getName();
        $authEmail = (string) $oauthUser->getEmail();

        $user = User::where('auth_id', $authId)
            ->orWhere('email', $authEmail)
            ->first();

        if (! $user) {
            return User::create([
                'auth_id' => $authId,
                'name' => $authName,
                'email' => $authEmail,
                'password' => null,
            ]);
        }

        $user->update([
            'name' => $authName,
            'email' => $authEmail,
        ]);

        return $user;
    }
}
