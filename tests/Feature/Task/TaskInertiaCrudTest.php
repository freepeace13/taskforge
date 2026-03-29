<?php

namespace Tests\Feature\Task;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TaskInertiaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_hub_renders_for_authenticated_member(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);

        $response = $this->actingAs($user)->get(route('tasks.hub', ['org' => $organization->slug]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Hub', false)
                ->has('organizations', 1)
                ->where('organizations.0.slug', $organization->slug)
        );
    }

    public function test_tasks_index_lists_tasks_for_project(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        Task::factory()->for($project)->create(['title' => 'Listed task']);

        $response = $this->actingAs($user)->get(route('projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Index', false)
                ->has('tasks.data', 1)
                ->where('tasks.data.0.title', 'Listed task')
        );
    }

    public function test_task_create_form_renders(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();

        $response = $this->actingAs($user)->get(route('projects.tasks.create', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page->component('Tasks/Create', false)
        );
    }

    public function test_task_can_be_stored_and_redirects_to_show(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();

        $response = $this->actingAs($user)->post(route('projects.tasks.store', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]), [
            'title' => 'New inertia task',
            'description' => 'Body',
            'priority' => 'high',
        ]);

        $task = Task::query()->where('title', 'New inertia task')->first();
        $this->assertNotNull($task);

        $response->assertRedirect(route('projects.tasks.show', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
        ]));
    }

    public function test_task_show_renders(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Show me']);

        $response = $this->actingAs($user)->get(route('projects.tasks.show', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Show', false)
                ->where('task.title', 'Show me')
        );
    }

    public function test_task_can_be_updated(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Old']);

        $response = $this->actingAs($user)->patch(route('projects.tasks.update', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
        ]), [
            'title' => 'Updated title',
            'description' => null,
            'priority' => 'low',
            'due_date' => null,
        ]);

        $response->assertRedirect(route('projects.tasks.show', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
        ]));

        $this->assertSame('Updated title', $task->fresh()->title);
    }

    public function test_task_can_be_deleted(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this->actingAs($user)->delete(route('projects.tasks.destroy', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
        ]));

        $response->assertRedirect(route('projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]));

        $this->assertSoftDeleted($task);
    }

    public function test_non_member_cannot_access_foreign_project_tasks(): void
    {
        [, $member] = $this->createOrganizationWithMember(Role::Owner);
        $organizationB = Organization::factory()->create();
        $projectB = Project::factory()->for($organizationB)->create();

        $response = $this->actingAs($member)->get(route('projects.tasks.index', [
            'org' => $organizationB->slug,
            'project' => $projectB->id,
        ]));

        $response->assertNotFound();
    }
}
