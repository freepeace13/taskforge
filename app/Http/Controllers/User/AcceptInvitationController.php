<?php

namespace App\Http\Controllers\User;

use App\Contracts\Actions\Organization\AcceptsInvitationAction;
use App\Http\Controllers\Controller;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AcceptInvitationController extends Controller
{
    public function __construct(
        protected readonly AcceptsInvitationAction $action
    ) {}

    public function __invoke(Request $request)
    {
        abort_unless($request->hasValidSignature(), Response::HTTP_FORBIDDEN);

        $token = $request->route('token');

        $invite = OrganizationInvite::query()
            ->where('token', $token)
            ->firstOrFail();

        $this->ensureInvitationIsValid($request, $invite);

        $this->authenticate(
            $user = $this->resolveInvitableUser($invite), $request
        );

        $this->action->accept($user, $invite);

        return response()->noContent();
    }

    private function authenticate($user, $request)
    {
        Auth::guard('web')->login($user);

        $request->session()->regenerate();
    }

    private function ensureInvitationIsValid($request, $invite)
    {
        $signedEmail = $request->string('email');

        abort_if(blank($signedEmail), Response::HTTP_FORBIDDEN, 'Invitation signature is invalid.');

        $emailMismatched = strtolower($signedEmail) !== strtolower($invite->email);

        abort_if($emailMismatched, Response::HTTP_UNPROCESSABLE_ENTITY, 'Invitation email mismatch.');
    }

    private function resolveInvitableUser($invite)
    {
        $email = $invite->email;

        $user = User::firstWhere('email', $email);

        if (! $user) {
            $localPart = Str::before($email, '@');
            $normalizedName = Str::of($localPart)
                ->replace(['.', '_', '-'], ' ')
                ->title()
                ->trim()
                ->value();

            $name = $normalizedName !== '' ? $normalizedName : 'Invited User';

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => null,
            ]);
        }

        return $user;
    }
}
