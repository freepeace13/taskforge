<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Actions\Auth\SyncsAuthTenantsForUser;
use App\Http\Controllers\Controller;
use App\Services\Auth\OAuthUserResolver;
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
    public function callback(
        OAuthUserResolver $oAuthUserResolver,
        SyncsAuthTenantsForUser $syncAuthTenantsForUser,
    ): RedirectResponse {
        $oauthUser = Socialite::driver('techysavvy')->user();

        $syncAuthTenantsForUser->sync(
            $user = $oAuthUserResolver->resolve($oauthUser),
            $oauthUser->tenants
        );

        Auth::guard(self::GUARD)->login($user, remember: true);

        return redirect()->intended(route('workspaces'));
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
