<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Contracts\Actions\Organization\CancelsInvitationAction;
use App\Contracts\Actions\Organization\InvitesUserAction;
use App\Data\InviteUserData;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreInvitationRequest;
use App\Http\Resources\InvitationResource;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Queries\Organization\ListOrganizationInvitationsQuery;
use App\Queries\Organization\ListOrganizationInvitationsQueryHandler;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvitationController extends Controller
{
    use AuthorizesRequests;

    public function index(ListOrganizationInvitationsQueryHandler $handler)
    {
        $organization = tenant()->organization;
        $this->authorize('viewAny', [OrganizationInvite::class, $organization]);

        $invites = $handler->handle(new ListOrganizationInvitationsQuery(
            organizationId: $organization->id,
            status: request()->string('status', null),
            search: request()->string('q', null),
            perPage: request()->integer('per_page', 10),
        ));

        return InvitationResource::collection($invites);
    }

    public function store(StoreInvitationRequest $request, InvitesUserAction $action)
    {
        $user = $request->user();
        $organization = tenant()->organization;

        $invitation = $action->invite(
            actor: $user,
            organization: $organization,
            data: new InviteUserData(
                email: $request->string('email'),
                role: Role::from($request->string('role'))
            )
        );

        return new InvitationResource($invitation);
    }

    public function destroy(OrganizationInvite $invite, CancelsInvitationAction $action)
    {
        $user = request()->user();

        $action->cancel(actor: $user, invitation: $invite);

        return response()->noContent();
    }
}
