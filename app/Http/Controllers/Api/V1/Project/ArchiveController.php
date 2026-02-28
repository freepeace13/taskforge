<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Actions\Project\ArchiveProjectAction;
use App\Actions\Project\RestoreProjectAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->where('organization_id', tenant()->organizationId)
            ->whereNotNull('archived_at')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return ProjectResource::collection($projects);
    }

    public function archive(Project $project, ArchiveProjectAction $action)
    {
        $this->ensureBelongsToTenant($project);

        $archived = $action->archive($project);

        return new ProjectResource($archived);
    }

    public function restore(Project $project, RestoreProjectAction $action)
    {
        $this->ensureBelongsToTenant($project);

        $restored = $action->restore($project);

        return new ProjectResource($restored);
    }

    protected function ensureBelongsToTenant(Project $project): void
    {
        abort_if($project->organization_id !== tenant()->organizationId, Response::HTTP_NOT_FOUND);
    }
}
