<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Contracts\Actions\Organization\CreatesOrganizationAction;
use App\Contracts\Actions\Organization\DeletesOrganizationAction;
use App\Contracts\Actions\Organization\UpdatesOrganizationAction;
use App\Data\OrganizationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Queries\Organization\ListOrganizationsQuery;
use App\Queries\Organization\ListOrganizationsQueryHandler;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, ListOrganizationsQueryHandler $handler)
    {
        $user = $request->user();
        $search = $request->string('q', null);

        $cursorPaginator = $handler->handle(new ListOrganizationsQuery(
            userId: $user->id,
            search: $search,
        ));

        return OrganizationResource::collection($cursorPaginator);
    }

    public function store(StoreOrganizationRequest $request, CreatesOrganizationAction $action)
    {
        $organization = $action->create(
            actor: $request->user(),
            data: new OrganizationData(
                name: $request->name
            )
        );

        return new OrganizationResource($organization);
    }

    public function show()
    {
        $org = tenant()->organization;

        $this->authorize('view', $org);

        return new OrganizationResource($org);
    }

    public function update(UpdateOrganizationRequest $request, UpdatesOrganizationAction $action)
    {
        $logo = $request->has('logo') ? $request->file('logo') : null;

        $organization = $action->update(
            actor: $request->user(),
            organization: tenant()->organization,
            data: new OrganizationData(
                name: $request->name,
                logo: $logo
            )
        );

        return new OrganizationResource($organization);
    }

    public function destroy(DeletesOrganizationAction $action)
    {
        $org = tenant()->organization;

        $action->delete(actor: request()->user(), organization: $org);

        return response()->noContent();
    }
}
