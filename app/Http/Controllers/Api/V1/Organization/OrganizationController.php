<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Http\Controllers\Controller;
use App\Actions\Organization\CreateOrganizationAction;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Queries\Organization\ListOrganizationsQuery;
use App\Queries\Organization\ListOrganizationsQueryHandler;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $handler = app(ListOrganizationsQueryHandler::class);

        $cursorPaginator = $handler->handle(new ListOrganizationsQuery(
            userId: $request->user()->id,
            search: $request->string('q', ''),
        ));

        return OrganizationResource::collection($cursorPaginator);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $creator = app(CreateOrganizationAction::class);

        $organization = $creator->create(
            ownerId: $request->user()->id,
            name: $validated['name']
        );

        return new OrganizationResource($organization);
    }

    public function show(Request $request, Organization $organization)
    {
        $user = $request->user();

        // Quick membership check (we’ll replace with Policy soon)
        $isMember = $organization->members()
            ->where('users.id', $user->id)
            ->exists();

        abort_unless($isMember, 403);

        return response()->json($organization->load(['owner']));
    }

    public function update(Request $request)
    {
        //
    }

    public function destroy(Organization $org)
    {
        //
    }
}
