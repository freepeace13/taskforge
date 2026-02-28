<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Actions\Organization\RemoveMemberAction;
use App\Actions\Organization\UpdateMemberRoleAction;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\UpdateMemberRoleRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Queries\Organization\ListMembersQuery;
use App\Queries\Organization\ListMembersQueryHandler;

class MemberController extends Controller
{
    public function index(ListMembersQueryHandler $handler)
    {
        $cursorPaginator = $handler->handle(new ListMembersQuery(
            organizationId: tenant()->organizationId,
            search: request()->filled('q') ? request()->string('q')->toString() : null,
            roles: request()->filled('roles') ? request()->string('roles')->toString() : null,
        ));

        return UserResource::collection($cursorPaginator);
    }

    public function update(UpdateMemberRoleRequest $request, User $user, UpdateMemberRoleAction $action)
    {
        $action->update(
            organizationId: tenant()->organizationId,
            actorRole: tenant()->role,
            member: $user,
            role: Role::from($request->string('role')->toString()),
        );

        return response()->json([
            'message' => 'Member role updated.',
        ]);
    }

    public function destroy(User $user, RemoveMemberAction $action)
    {
        $action->remove(
            organizationId: tenant()->organizationId,
            actorRole: tenant()->role,
            member: $user,
        );

        return response()->noContent();
    }
}
