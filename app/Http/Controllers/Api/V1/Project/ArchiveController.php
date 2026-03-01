<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Contracts\Actions\Project\ArchivesProjectAction;
use App\Contracts\Actions\Project\RestoresProjectAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ArchiveController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $organization = tenant()->organization;

        $this->authorize('viewAny', [Project::class, $organization]);

        $projects = $organization->projects()
            ->whereNotNull('archived_at')
            ->latest('id')
            ->paginate();

        return ProjectResource::collection($projects);
    }

    public function archive(Project $project, ArchivesProjectAction $action)
    {
        $user = request()->user();

        $archived = $action->archive(actor: $user, project: $project);

        return new ProjectResource($archived);
    }

    public function restore(Project $project, RestoresProjectAction $action)
    {
        $user = request()->user();

        $restored = $action->restore(actor: $user, project: $project);

        return new ProjectResource($restored);
    }
}
