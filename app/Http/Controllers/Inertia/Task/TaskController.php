<?php

namespace App\Http\Controllers\Inertia\Task;

use App\Contracts\Actions\Task\CreatesTaskAction;
use App\Contracts\Actions\Task\DeletesTaskAction;
use App\Contracts\Actions\Task\UpdatesTaskAction;
use App\Contracts\Queries\Task\ListsTaskHubOrganizations;
use App\Contracts\Queries\Task\ListsTasks;
use App\Data\TaskData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Queries\Task\ListTaskHubOrganizationsQuery;
use App\Queries\Task\ListTasksQuery;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function hub(Request $request, ListsTaskHubOrganizations $listsTaskHubOrganizations): InertiaResponse
    {
        $organizations = $listsTaskHubOrganizations->handle(
            new ListTaskHubOrganizationsQuery(userId: $request->user()->id)
        );

        return Inertia::render('Tasks/Hub', [
            'organizations' => $organizations->map(fn (Organization $org) => [
                'slug' => $org->slug,
                'name' => $org->name,
                'projects' => $org->projects->map(fn (Project $project) => [
                    'id' => $project->id,
                    'name' => $project->name,
                ]),
            ]),
        ]);
    }

    public function index(Request $request, Project $project, ListsTasks $listsTasks): InertiaResponse
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $organization = $project->organization;

        $tasks = $listsTasks->handle(new ListTasksQuery(
            projectId: $project->id,
            perPage: 15,
        ));

        $tasks->setCollection(
            $tasks->getCollection()->map(fn (Task $task) => (new TaskResource($task))->resolve())
        );

        return Inertia::render('Tasks/Index', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'tasks' => $tasks,
        ]);
    }

    public function create(Project $project): InertiaResponse
    {
        $this->authorize('create', [Task::class, $project]);

        $organization = $project->organization;

        return Inertia::render('Tasks/Create', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
        ]);
    }

    public function store(StoreTaskRequest $request, Project $project, CreatesTaskAction $action): RedirectResponse
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

        return redirect()->route('projects.tasks.show', [
            'project' => $project,
            'task' => $task,
        ])->with('success', __('Task created.'));
    }

    public function show(Project $project, Task $task): InertiaResponse
    {
        $this->authorize('view', $task);

        $organization = $project->organization;

        return Inertia::render('Tasks/Show', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'task' => (new TaskResource($task))->resolve(),
        ]);
    }

    public function edit(Project $project, Task $task): InertiaResponse
    {
        $this->authorize('update', $task);

        $organization = $project->organization;

        return Inertia::render('Tasks/Edit', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'task' => (new TaskResource($task))->resolve(),
        ]);
    }

    public function update(
        UpdateTaskRequest $request,
        Project $project,
        Task $task,
        UpdatesTaskAction $action
    ): RedirectResponse {
        $action->update(
            actor: $request->user(),
            task: $task,
            data: new TaskData(
                title: $request->input('title'),
                description: $request->input('description'),
                priority: $request->input('priority'),
                dueDate: $request->input('due_date')
            ),
        );

        return redirect()->route('projects.tasks.show', [
            'project' => $project,
            'task' => $task->fresh(),
        ])->with('success', __('Task updated.'));
    }

    public function destroy(
        Request $request,
        Project $project,
        Task $task,
        DeletesTaskAction $action
    ): RedirectResponse {
        $action->delete(
            actor: $request->user(),
            task: $task
        );

        return redirect()->route('projects.tasks.index', [
            'project' => $project,
        ])->with('success', __('Task deleted.'));
    }
}
