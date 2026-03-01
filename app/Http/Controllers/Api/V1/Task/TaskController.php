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
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project)
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $tasks = $project->tasks()
            ->latest('id')
            ->paginate();

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request, Project $project, CreatesTaskAction $action)
    {
        $task = $action->create(
            actor: $request->user(),
            project: $project,
            data: new TaskData(
                title: $request->string('title'),
                description: $request->string('description'),
                priority: $request->string('priority'),
                dueDate: $request->string('due_date')
            )
        );

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task, UpdatesTaskAction $action)
    {
        $updated = $action->update(
            actor: $request->user(),
            task: $task,
            data: new TaskData(
                title: $request->string('title'),
                description: $request->string('description'),
                priority: $request->string('priority'),
                dueDate: $request->string('due_date')
            ),
        );

        return new TaskResource($updated);
    }

    public function destroy(Task $task, DeletesTaskAction $action)
    {
        $action->delete(
            actor: request()->user(),
            task: $task
        );

        return response()->noContent();
    }
}
