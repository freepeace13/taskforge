<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Contracts\Actions\Task\AssignsTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;

class AssigneeController extends Controller
{
    public function assign(
        Organization $org,
        Project $project,
        Task $task,
        AssignTaskRequest $request,
        AssignsTaskAction $action
    ) {
        $updated = $action->assign(
            actor: $request->user(),
            task: $task,
            userId: $request->integer('user_id'),
        );

        return new TaskResource($updated);
    }

    public function unassign(
        Organization $org,
        Project $project,
        Task $task,
        AssignsTaskAction $action
    ) {
        $updated = $action->assign(
            actor: request()->user(),
            task: $task,
            userId: null
        );

        return new TaskResource($updated);
    }
}
