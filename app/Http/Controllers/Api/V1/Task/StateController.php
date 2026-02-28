<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Actions\Task\CompleteTaskAction;
use App\Actions\Task\ReopenTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Symfony\Component\HttpFoundation\Response;

class StateController extends Controller
{
    public function complete(Project $project, Task $task, CompleteTaskAction $action)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        $completed = $action->complete($task);

        return new TaskResource($completed);
    }

    public function reopen(Project $project, Task $task, ReopenTaskAction $action)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        $reopened = $action->reopen($task);

        return new TaskResource($reopened);
    }

    protected function ensureTaskBelongsToProjectAndTenant(Project $project, Task $task): void
    {
        abort_if($project->organization_id !== tenant()->organizationId, Response::HTTP_NOT_FOUND);
        abort_if($task->project_id !== $project->id, Response::HTTP_NOT_FOUND);
    }
}
