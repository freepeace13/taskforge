<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Contracts\Actions\Task\CreatesTaskAction;
use App\Contracts\Actions\Task\DeletesTaskAction;
use App\Contracts\Actions\Task\UpdatesTaskAction;
use App\Data\TaskData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Organization $org, Project $project)
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $tasks = $project->tasks()
            ->latest('id')
            ->paginate();

        return TaskResource::collection($tasks);
    }

    public function store(Organization $org, Project $project, StoreTaskRequest $request)
    {
        $action = app(CreatesTaskAction::class);

        $task = $action->create(
            actor: $request->user(),
            project: $project,
            data: new TaskData(
                title: $request->title,
                description: $request->description,
                priority: $request->priority,
                dueDate: $request->due_date
            )
        );

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Organization $org, Project $project, Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(
        Organization $org,
        Project $project,
        Task $task,
        UpdateTaskRequest $request,
        UpdatesTaskAction $action
    ) {
        $updated = $action->update(
            actor: $request->user(),
            task: $task,
            data: new TaskData(
                title: $request->title,
                description: $request->description,
                priority: $request->priority,
                dueDate: $request->due_date
            ),
        );

        return new TaskResource($updated);
    }

    public function destroy(
        Organization $org,
        Project $project,
        Task $task,
        DeletesTaskAction $action
    ) {
        $action->delete(
            actor: request()->user(),
            task: $task
        );

        return response()->noContent();
    }
}
