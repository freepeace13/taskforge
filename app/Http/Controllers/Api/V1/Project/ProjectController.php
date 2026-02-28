<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Actions\Project\CreateProjectAction;
use App\Actions\Project\DeleteProjectAction;
use App\Actions\Project\UpdateProjectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->where('organization_id', tenant()->organizationId)
            ->whereNull('archived_at')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request, CreateProjectAction $action)
    {
        $project = $action->create(
            organizationId: tenant()->organizationId,
            name: $request->string('name')->toString(),
            description: $request->string('description')->toNullableString(),
        );

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        $this->ensureBelongsToTenant($project);

        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project, UpdateProjectAction $action)
    {
        $this->ensureBelongsToTenant($project);

        $updated = $action->update(
            project: $project,
            attributes: $request->validated(),
        );

        return new ProjectResource($updated);
    }

    public function destroy(Project $project, DeleteProjectAction $action)
    {
        $this->ensureBelongsToTenant($project);

        $action->delete($project);

        return response()->noContent();
    }

    protected function ensureBelongsToTenant(Project $project): void
    {
        abort_if($project->organization_id !== tenant()->organizationId, Response::HTTP_NOT_FOUND);
    }
}
