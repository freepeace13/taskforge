<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $this->ensureProjectBelongsToTenant($project);

        $tasks = Task::query()
            ->where('project_id', $project->id)
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request, Project $project, CreateTaskAction $action)
    {
        $this->ensureProjectBelongsToTenant($project);

        $task = $action->create(
            project: $project,
            title: $request->string('title')->toString(),
            description: $request->string('description')->toNullableString(),
            priority: $request->string('priority')->toNullableString(),
            dueDate: $request->string('due_date')->toNullableString(),
            assignedToUserId: $request->integer('assigned_to_user_id'),
        );

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Project $project, Task $task)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task, UpdateTaskAction $action)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        $updated = $action->update(
            task: $task,
            attributes: $request->validated(),
        );

        return new TaskResource($updated);
    }

    public function destroy(Project $project, Task $task, DeleteTaskAction $action)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        $action->delete($task);

        return response()->noContent();
    }

    protected function ensureProjectBelongsToTenant(Project $project): void
    {
        abort_if($project->organization_id !== tenant()->organizationId, Response::HTTP_NOT_FOUND);
    }

    protected function ensureTaskBelongsToProjectAndTenant(Project $project, Task $task): void
    {
        $this->ensureProjectBelongsToTenant($project);

        abort_if($task->project_id !== $project->id, Response::HTTP_NOT_FOUND);
    }
}
