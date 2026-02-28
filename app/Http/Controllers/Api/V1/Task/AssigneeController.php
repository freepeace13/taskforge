<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Actions\Task\AssignTaskAction;
use App\Actions\Task\UnassignTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Symfony\Component\HttpFoundation\Response;

class AssigneeController extends Controller
{
    public function assign(AssignTaskRequest $request, Project $project, Task $task, AssignTaskAction $action)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        $updated = $action->assign(
            task: $task,
            userId: $request->integer('user_id'),
        );

        return new TaskResource($updated);
    }

    public function unassign(Project $project, Task $task, UnassignTaskAction $action)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        $updated = $action->unassign($task);

        return new TaskResource($updated);
    }

    protected function ensureTaskBelongsToProjectAndTenant(Project $project, Task $task): void
    {
        abort_if($project->organization_id !== tenant()->organizationId, Response::HTTP_NOT_FOUND);
        abort_if($task->project_id !== $project->id, Response::HTTP_NOT_FOUND);
    }
}
