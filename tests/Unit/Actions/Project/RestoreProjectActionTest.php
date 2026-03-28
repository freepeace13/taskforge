<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\RestoreProjectAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestoreProjectActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_restores_archived_project_for_admin(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $admin->id,
            'role' => Role::Admin->value,
        ]);

        $project = Project::factory()->for($organization)->create([
            'archived_at' => now(),
        ]);

        $action = app(RestoreProjectAction::class);

        $restored = $action->restore(
            actor: $admin,
            project: $project,
        );

        $this->assertNull($restored->archived_at);
    }

    public function test_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $nonMember = User::factory()->create();
        $project = Project::factory()->for($organization)->create([
            'archived_at' => now(),
        ]);

        $action = app(RestoreProjectAction::class);

        $this->expectException(AuthorizationException::class);

        $action->restore(
            actor: $nonMember,
            project: $project,
        );
    }
}
