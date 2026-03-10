<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Contracts\Actions\Project\CreatesProjectAction;
use App\Contracts\Actions\Project\DeletesProjectAction;
use App\Contracts\Actions\Project\UpdatesProjectAction;
use App\Data\ProjectData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $org = tenant()->organization;

        $this->authorize('viewAny', [Project::class, $org]);

        $projects = $org->projects()
            ->whereNull('archived_at')
            ->latest('id')
            ->paginate();

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request, CreatesProjectAction $action)
    {
        $org = tenant()->organization;

        $project = $action->create(
            actor: $request->user(),
            organization: $org,
            data: new ProjectData(
                name: $request->name,
                description: $request->description
            )
        );

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return new ProjectResource($project);
    }

    public function update(
        Project $project,
        UpdateProjectRequest $request,
        UpdatesProjectAction $action
    ) {
        $user = $request->user();

        $updated = $action->update(
            actor: $user,
            project: $project,
            data: new ProjectData(
                name: $request->name,
                description: $request->description
            ),
        );

        return new ProjectResource($updated);
    }

    public function destroy(
        Project $project,
        DeletesProjectAction $action
    ) {
        if (! $project instanceof Project) {
            $project = Project::query()->findOrFail($project);
        }

        $user = request()->user();

        $action->delete(actor: $user, project: $project);

        return response()->noContent();
    }
}
