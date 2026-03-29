<?php

namespace App\Http\Controllers\Inertia\Project;

use App\Contracts\Actions\Project\CreatesProjectAction;
use App\Contracts\Actions\Project\DeletesProjectAction;
use App\Contracts\Actions\Project\UpdatesProjectAction;
use App\Contracts\Queries\Project\ListsProjects;
use App\Data\ProjectData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Organization;
use App\Models\Project;
use App\Queries\Project\ListProjectsQuery;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index(Organization $org, ListsProjects $listsProjects): InertiaResponse
    {
        $this->authorize('viewAny', [Project::class, $org]);

        $projects = $listsProjects->handle(new ListProjectsQuery(
            organizationId: $org->id,
            archivedFilter: 'active',
            perPage: 15,
        ));

        $projects->setCollection(
            $projects->getCollection()->map(fn (Project $project) => (new ProjectResource($project))->resolve())
        );

        return Inertia::render('Projects/Index', [
            'organization' => [
                'slug' => $org->slug,
                'name' => $org->name,
            ],
            'projects' => $projects,
        ]);
    }

    public function create(Organization $org): InertiaResponse
    {
        $this->authorize('create', [Project::class, $org]);

        return Inertia::render('Projects/Create', [
            'organization' => [
                'slug' => $org->slug,
                'name' => $org->name,
            ],
        ]);
    }

    public function store(StoreProjectRequest $request, Organization $org, CreatesProjectAction $action): RedirectResponse
    {
        $project = $action->create(
            actor: $request->user(),
            organization: $org,
            data: new ProjectData(
                name: $request->name,
                description: $request->description
            )
        );

        return redirect()->route('projects.show', [
            'org' => $org,
            'project' => $project,
        ])->with('success', __('Project created.'));
    }

    public function show(Organization $org, Project $project): InertiaResponse
    {
        $this->authorize('view', $project);

        $organization = $project->organization;

        return Inertia::render('Projects/Show', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => (new ProjectResource($project))->resolve(),
        ]);
    }

    public function edit(Organization $org, Project $project): InertiaResponse
    {
        $this->authorize('update', $project);

        $organization = $project->organization;

        return Inertia::render('Projects/Edit', [
            'organization' => [
                'slug' => $organization->slug,
                'name' => $organization->name,
            ],
            'project' => (new ProjectResource($project))->resolve(),
        ]);
    }

    public function update(
        UpdateProjectRequest $request,
        Organization $org,
        Project $project,
        UpdatesProjectAction $action
    ): RedirectResponse {
        $action->update(
            actor: $request->user(),
            project: $project,
            data: new ProjectData(
                name: $request->input('name'),
                description: $request->input('description'),
            ),
        );

        return redirect()->route('projects.show', [
            'org' => $org,
            'project' => $project->fresh(),
        ])->with('success', __('Project updated.'));
    }

    public function destroy(Request $request, Organization $org, Project $project, DeletesProjectAction $action): RedirectResponse
    {
        $action->delete(
            actor: $request->user(),
            project: $project
        );

        return redirect()->route('projects.index', ['org' => $org])->with('success', __('Project deleted.'));
    }
}
