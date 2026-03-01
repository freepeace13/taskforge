<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Contracts\Actions\Task\CompletesTaskAction;
use App\Contracts\Actions\Task\ReopensTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class StateController extends Controller
{
    public function complete(Task $task, CompletesTaskAction $action)
    {
        $completed = $action->complete(
            actor: request()->user(),
            task: $task
        );

        return new TaskResource($completed);
    }

    public function reopen(Task $task, ReopensTaskAction $action)
    {
        $reopened = $action->reopen(
            actor: request()->user(),
            task: $task
        );

        return new TaskResource($reopened);
    }
}
