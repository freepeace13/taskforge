<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Contracts\Actions\Task\CreatesTaskAction;
use App\Contracts\Actions\Task\DeletesTaskAction;
use App\Contracts\Actions\Task\UpdatesTaskAction;
use App\Contracts\Queries\Task\ListsTasks;
use App\Data\TaskData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Queries\Task\ListTasksQuery;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Project $project, ListsTasks $listsTasks)
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $perPage = $request->filled('per_page')
            ? $request->integer('per_page')
            : null;

        $tasks = $listsTasks->handle(new ListTasksQuery(
            projectId: $project->id,
            perPage: $perPage,
        ));

        return TaskResource::collection($tasks);
    }

    public function store(Project $project, StoreTaskRequest $request, CreatesTaskAction $action)
    {
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

    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(
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
