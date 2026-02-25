<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Actions\Organization\InviteUserAction;
use App\Actions\Organization\RevokeInvitationAction;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrganizationInvitationRequest;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Queries\Organization\ListOrganizationInvitationsQuery;
use App\Queries\Organization\ListOrganizationInvitationsQueryHandler;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    public function index(ListOrganizationInvitationsQueryHandler $handler)
    {
        $invites = $handler->handle(new ListOrganizationInvitationsQuery(
            organizationId: tenant()->organizationId,
            status: request()->filled('status') ? request()->string('status')->toString() : null,
            search: request()->filled('q') ? request()->string('q')->toString() : null,
            perPage: request()->integer('per_page', 10),
        ));

        return response()->json($invites);
    }

    public function store(StoreOrganizationInvitationRequest $request, InviteUserAction $inviteUserAction)
    {
        $organization = Organization::query()->findOrFail(tenant()->organizationId);

        $invitation = $inviteUserAction->invite(
            organization: $organization,
            inviter: $request->user(),
            actorRole: tenant()->role,
            email: $request->string('email')->toString(),
            role: Role::from($request->string('role')->toString()),
        );

        return response()->json([
            ...$invitation['invite']->toArray(),
            'accept_url' => $invitation['accept_url'],
        ], Response::HTTP_CREATED);
    }

    public function destroy(OrganizationInvite $invite, RevokeInvitationAction $action)
    {
        $action->revoke(
            organizationId: tenant()->organizationId,
            actorRole: tenant()->role,
            invite: $invite,
        );

        return response()->noContent();
    }
}
