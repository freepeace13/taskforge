<?php

namespace App\Http\Controllers\User;

use App\Actions\Organization\AcceptInvitationAction;
use App\Actions\Organization\AuthenticateInvitedUserAction;
use App\Actions\Organization\ResolveInvitableUserAction;
use App\Http\Controllers\Controller;
use App\Models\OrganizationInvite;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    public function accept(
        Request $request,
        string $token,
        ResolveInvitableUserAction $resolveInvitableUserAction,
        AuthenticateInvitedUserAction $authenticateInvitedUserAction,
        AcceptInvitationAction $acceptInvitationAction
    ) {
        abort_unless($request->hasValidSignature(), Response::HTTP_FORBIDDEN);

        $invite = OrganizationInvite::query()
            ->where('token', $token)
            ->firstOrFail();

        $signedEmail = $request->string('email')->toString();

        abort_if($signedEmail === '', Response::HTTP_FORBIDDEN, 'Invitation signature is invalid.');
        abort_if(strtolower($signedEmail) !== strtolower($invite->email), Response::HTTP_UNPROCESSABLE_ENTITY, 'Invitation email mismatch.');

        $user = $resolveInvitableUserAction->resolve($invite->email);

        $authenticateInvitedUserAction->authenticate($request, $user);
        $acceptInvitationAction->accept($user, $invite);

        return response()->json([
            'message' => 'Invitation accepted.',
        ]);
    }
}
