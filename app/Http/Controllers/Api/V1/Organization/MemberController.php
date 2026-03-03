<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Contracts\Actions\Organization\RemovesMemberAction;
use App\Contracts\Actions\Organization\UpdatesMemberRoleAction;
use App\Data\MemberData;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\UpdateMemberRoleRequest;
use App\Http\Resources\MemberResource;
use App\Models\OrganizationMember;
use App\Models\User;
use App\Queries\Organization\ListMembersQuery;
use App\Queries\Organization\ListMembersQueryHandler;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MemberController extends Controller
{
    use AuthorizesRequests;

    public function index(ListMembersQueryHandler $handler)
    {
        $organization = tenant()->organization;
        $this->authorize('viewAny', [OrganizationMember::class, $organization]);

        $cursorPaginator = $handler->handle(new ListMembersQuery(
            organizationId: $organization->id,
            search: request()->string('q', null),
            roles: request()->string('roles', null),
        ));

        return MemberResource::collection($cursorPaginator);
    }

    public function update(UpdateMemberRoleRequest $request, User|string $user, UpdatesMemberRoleAction $action)
    {
        $user = $user instanceof User
            ? $user
            : User::query()->findOrFail($request->route('user'));

        $member = $action->update(
            actor: $request->user(),
            data: new MemberData(
                organization: tenant()->organization,
                user: $user,
                role: Role::from($request->string('role'))
            )
        );

        return new MemberResource($member);
    }

    public function destroy(User|string $user, RemovesMemberAction $action)
    {
        $user = $user instanceof User
            ? $user
            : User::query()->findOrFail(request()->route('user'));

        $action->remove(
            actor: tenant()->user,
            organization: tenant()->organization,
            userId: $user->id,
        );

        return response()->noContent();
    }
}
