<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Contracts\Actions\Project\ArchivesProjectAction;
use App\Contracts\Actions\Project\RestoresProjectAction;
use App\Contracts\Queries\Project\ListsProjects;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Queries\Project\ListProjectsQuery;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ArchiveController extends Controller
{
    use AuthorizesRequests;

    public function index(ListsProjects $listsProjects)
    {
        $org = tenant()->organization;

        $this->authorize('viewAny', [Project::class, $org]);

        $projects = $listsProjects->handle(new ListProjectsQuery(
            organizationId: $org->id,
            archivedFilter: 'archived',
        ));

        return ProjectResource::collection($projects);
    }

    public function archive(
        Project $project,
        ArchivesProjectAction $action
    ) {
        $user = request()->user();

        $archived = $action->archive(actor: $user, project: $project);

        return new ProjectResource($archived);
    }

    public function restore(
        Project $project,
        RestoresProjectAction $action
    ) {
        $user = request()->user();

        $restored = $action->restore(actor: $user, project: $project);

        return new ProjectResource($restored);
    }
}
