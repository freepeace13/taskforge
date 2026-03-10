<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * Redirect the user to the auth server for authentication.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('techysavvy')->stateless()->redirect();
    }

    /**
     * Handle the callback from the auth server.
     */
    public function callback(Request $request): RedirectResponse
    {
        $oauthUser = Socialite::driver('techysavvy')->stateless()->user();

        $authId = (string) $oauthUser->getId();
        $user = User::query()->where('auth_id', $authId)->first();

        if (! $user) {
            $user = User::query()
                ->where('email', $oauthUser->getEmail())
                ->first();

            if ($user) {
                $user->update(['auth_id' => $authId]);
            } else {
                $user = User::query()->create([
                    'auth_id' => $authId,
                    'name' => $oauthUser->getName(),
                    'email' => $oauthUser->getEmail(),
                    'password' => null,
                ]);
            }
        } else {
            $user->update([
                'name' => $oauthUser->getName(),
                'email' => $oauthUser->getEmail(),
            ]);
        }

        Auth::guard('web')->login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
