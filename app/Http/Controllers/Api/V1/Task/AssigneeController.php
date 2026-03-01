<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Contracts\Actions\Task\AssignsTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class AssigneeController extends Controller
{
    public function assign(AssignTaskRequest $request, Task $task, AssignsTaskAction $action)
    {
        $updated = $action->assign(
            actor: $request->user(),
            task: $task,
            userId: $request->integer('user_id'),
        );

        return new TaskResource($updated);
    }

    public function unassign(Task $task, AssignsTaskAction $action)
    {
        $updated = $action->assign(
            actor: request()->user(),
            task: $task,
            userId: null
        );

        return new TaskResource($updated);
    }
}
