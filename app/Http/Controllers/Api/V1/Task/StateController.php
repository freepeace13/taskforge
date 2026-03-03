<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Contracts\Actions\Task\CompletesTaskAction;
use App\Contracts\Actions\Task\ReopensTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;

class StateController extends Controller
{
    public function complete(
        Organization $org,
        Project $project,
        Task $task,
        CompletesTaskAction $action
    ) {
        $completed = $action->complete(
            actor: request()->user(),
            task: $task
        );

        return new TaskResource($completed);
    }

    public function reopen(
        Organization $org,
        Project $project,
        Task $task,
        ReopensTaskAction $action
    ) {
        $reopened = $action->reopen(
            actor: request()->user(),
            task: $task
        );

        return new TaskResource($reopened);
    }
}
