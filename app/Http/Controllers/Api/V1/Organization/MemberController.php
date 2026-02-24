<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Queries\Organization\ListMembersQuery;
use App\Queries\Organization\ListMembersQueryHandler;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request, Organization $org)
    {
        $handler = app(ListMembersQueryHandler::class);

        $cursorPaginator = $handler->handle(new ListMembersQuery(
            organizationId: $org->id,
            search: $request->q ?? '',
            roles: explode(',', $request->role ?? ''),
            perPage: 10
        ));

        return UserResource::collection($cursorPaginator);
    }

    public function store(Request $request, Organization $org)
    {
        //
    }

    public function update(Request $request, Organization $org)
    {
        //
    }

    public function destroy(Organization $org)
    {
        //
    }
}
