<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class OAuthController extends Controller
{
    const GUARD = 'web';

    /**
     * Show a short message, then send the user to the auth server for authentication.
     */
    public function redirect(): View
    {
        $authUrl = Socialite::driver('techysavvy')->redirect()->getTargetUrl();

        return view('auth.oauth-redirect', [
            'authUrl' => $authUrl,
        ]);
    }

    /**
     * Handle the callback from the auth server.
     */
    public function callback(Request $request): RedirectResponse
    {
        $oauthUser = Socialite::driver('techysavvy')->user();

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

        Auth::guard(self::GUARD)->login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): Response
    {
        Auth::guard(self::GUARD)->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->header('X-Inertia')) {
            return Inertia::location(route('site.home'));
        }

        return redirect()->route('site.home');
    }
}
