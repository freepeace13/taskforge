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
                    'slug' => $project->slug,
                    'name' => $project->name,
                ]),
            ]),
        ]);
    }

    public function index(Request $request, Organization $org, Project $project, ListsTasks $listsTasks): InertiaResponse
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

        $taskPreview = null;
        $previewKey = $request->query('task');
        if (is_string($previewKey) && $previewKey !== '') {
            $previewTask = Task::query()
                ->where('project_id', $project->id)
                ->where(function ($query) use ($previewKey): void {
                    $query->where('key', $previewKey);
                    if (ctype_digit($previewKey)) {
                        $query->orWhere('id', (int) $previewKey);
                    }
                })
                ->with('members')
                ->first();

            if ($previewTask !== null && $request->user()->can('view', $previewTask)) {
                $taskPreview = (new TaskResource($previewTask))->resolve();
            }
        }

        $organizationMembers = $organization->members()
            ->orderBy('name')
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->values()
            ->all();

        return Inertia::render('Tasks/Index', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'slug' => $project->slug,
                'name' => $project->name,
            ],
            'tasks' => $tasks,
            'taskPreview' => $taskPreview,
            'organizationMembers' => $organizationMembers,
        ]);
    }

    public function create(Organization $org, Project $project): InertiaResponse
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
                'slug' => $project->slug,
                'name' => $project->name,
            ],
        ]);
    }

    public function store(StoreTaskRequest $request, Organization $org, Project $project, CreatesTaskAction $action): RedirectResponse
    {
        $validated = $request->validated();

        $task = $action->create(
            actor: $request->user(),
            project: $project,
            data: new TaskData(
                title: $validated['title'],
                description: $validated['description'] ?? null,
                priority: $validated['priority'] ?? null,
                dueDate: $validated['due_date'] ?? null,
                status: null,
                memberIds: $validated['member_ids'] ?? null,
            )
        );

        return redirect()->route('projects.tasks.show', [
            'org' => $org,
            'project' => $project,
            'task' => $task,
        ])->with('success', __('Task created.'));
    }

    public function show(Organization $org, Project $project, Task $task): InertiaResponse
    {
        $this->authorize('view', $task);

        $organization = $project->organization;

        $task->loadMissing('members');

        $organizationMembers = $organization->members()
            ->orderBy('name')
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->values()
            ->all();

        return Inertia::render('Tasks/Show', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'slug' => $project->slug,
                'name' => $project->name,
            ],
            'task' => (new TaskResource($task))->resolve(),
            'organizationMembers' => $organizationMembers,
        ]);
    }

    public function edit(Organization $org, Project $project, Task $task): InertiaResponse
    {
        $this->authorize('update', $task);

        $organization = $project->organization;

        $task->loadMissing('members');

        return Inertia::render('Tasks/Edit', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'slug' => $project->slug,
                'name' => $project->name,
            ],
            'task' => (new TaskResource($task))->resolve(),
        ]);
    }

    public function update(
        UpdateTaskRequest $request,
        Organization $org,
        Project $project,
        Task $task,
        UpdatesTaskAction $action
    ): RedirectResponse {
        $validated = $request->validated();
        $redirectToBoard = $validated['redirect_to_board'] ?? false;
        $redirectBack = $validated['redirect_back'] ?? false;
        unset($validated['redirect_to_board'], $validated['redirect_back']);

        $action->update(
            actor: $request->user(),
            task: $task,
            data: TaskData::mergeForUpdate($task, $validated),
        );

        $freshTask = $task->fresh();

        if ($redirectToBoard) {
            return redirect()->route('projects.tasks.index', [
                'org' => $org,
                'project' => $project,
                'view' => 'board',
            ])->with('success', __('Task updated.'));
        }

        if ($redirectBack) {
            return back()->with('success', __('Task updated.'));
        }

        return redirect()->route('projects.tasks.show', [
            'org' => $org,
            'project' => $project,
            'task' => $freshTask,
        ])->with('success', __('Task updated.'));
    }

    public function destroy(
        Request $request,
        Organization $org,
        Project $project,
        Task $task,
        DeletesTaskAction $action
    ): RedirectResponse {
        $action->delete(
            actor: $request->user(),
            task: $task
        );

        return redirect()->route('projects.tasks.index', [
            'org' => $org,
            'project' => $project,
        ])->with('success', __('Task deleted.'));
    }
}
