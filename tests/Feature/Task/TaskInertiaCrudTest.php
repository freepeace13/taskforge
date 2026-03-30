<?php

namespace Tests\Feature\Task;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
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
            'project' => $project->slug,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Index', false)
                ->has('tasks.data', 1)
                ->where('tasks.data.0.title', 'Listed task')
                ->has('organizationMembers', 1)
        );

        $boardResponse = $this->actingAs($user)->get(route('projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'view' => 'board',
        ]));

        $boardResponse->assertOk();
        $boardResponse->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Index', false)
                ->has('tasks.data', 1)
        );
    }

    public function test_tasks_index_includes_task_preview_when_task_query_matches(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Preview me']);

        $response = $this->actingAs($user)->get(route('projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Index', false)
                ->where('taskPreview.title', 'Preview me')
                ->where('taskPreview.key', $task->key)
        );
    }

    public function test_tasks_index_omits_task_preview_when_task_query_unknown(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();

        $response = $this->actingAs($user)->get(route('projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => 'NOPE-99',
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Index', false)
                ->where('taskPreview', null)
        );
    }

    public function test_task_create_form_renders(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();

        $response = $this->actingAs($user)->get(route('projects.tasks.create', [
            'org' => $organization->slug,
            'project' => $project->slug,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page->component('Tasks/Create', false)
        );
    }

    public function test_task_can_be_stored_and_redirects_to_show(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create(['name' => 'Campaign Builder']);

        $response = $this->actingAs($user)->post(route('projects.tasks.store', [
            'org' => $organization->slug,
            'project' => $project->slug,
        ]), [
            'title' => 'New inertia task',
            'description' => 'Body',
            'priority' => 'high',
        ]);

        $task = Task::query()->where('title', 'New inertia task')->first();
        $this->assertNotNull($task);
        $this->assertSame('CB-01', $task->key);

        $response->assertRedirect(route('projects.tasks.show', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]));
    }

    public function test_task_keys_auto_increment_per_project(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create(['name' => 'Campaign Builder']);

        $this->actingAs($user)->post(route('projects.tasks.store', [
            'org' => $organization->slug,
            'project' => $project->slug,
        ]), [
            'title' => 'First',
            'priority' => 'high',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('projects.tasks.store', [
            'org' => $organization->slug,
            'project' => $project->slug,
        ]), [
            'title' => 'Second',
            'priority' => 'high',
        ])->assertRedirect();

        $keys = Task::query()
            ->where('project_id', $project->id)
            ->orderBy('id')
            ->pluck('key')
            ->all();

        $this->assertSame(['CB-01', 'CB-02'], $keys);
    }

    public function test_task_show_renders(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Show me']);

        $response = $this->actingAs($user)->get(route('projects.tasks.show', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Show', false)
                ->where('task.title', 'Show me')
                ->has('organizationMembers', 1)
        );
    }

    public function test_task_show_includes_task_members(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create(['title' => 'With members']);
        $task->members()->attach($user->id);

        $response = $this->actingAs($user)->get(route('projects.tasks.show', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Show', false)
                ->has('task.members', 1)
                ->where('task.members.0.id', $user->id)
        );
    }

    public function test_task_can_be_updated(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Old']);

        $response = $this->actingAs($user)->patch(route('projects.tasks.update', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]), [
            'title' => 'Updated title',
            'description' => null,
            'priority' => 'low',
            'due_date' => null,
        ]);

        $response->assertRedirect(route('projects.tasks.show', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]));

        $this->assertSame('Updated title', $task->fresh()->title);
    }

    public function test_task_status_can_be_updated_from_board_without_sending_other_fields(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create([
            'title' => 'Board task',
            'status' => 'todo',
        ]);

        $response = $this->actingAs($user)->patch(route('projects.tasks.update', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]), [
            'status' => 'done',
            'redirect_to_board' => true,
        ]);

        $response->assertRedirect(route('projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'view' => 'board',
        ]));

        $this->assertSame('done', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_task_member_ids_can_be_synced_with_redirect_back(): void
    {
        [$organization, $owner] = $this->createOrganizationWithMember(Role::Owner);
        $member = User::factory()->create();
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();

        $returnUrl = route('projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]);

        $response = $this->actingAs($owner)->from($returnUrl)->patch(route('projects.tasks.update', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]), [
            'member_ids' => [$member->id],
            'redirect_back' => true,
        ]);

        $response->assertRedirect($returnUrl);
        $this->assertTrue($task->fresh()->members()->whereKey($member->id)->exists());
    }

    public function test_task_can_be_deleted(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this->actingAs($user)->delete(route('projects.tasks.destroy', [
            'org' => $organization->slug,
            'project' => $project->slug,
            'task' => $task->key,
        ]));

        $response->assertRedirect(route('projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->slug,
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
            'project' => $projectB->slug,
        ]));

        $response->assertNotFound();
    }
}
