<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\ArchiveProjectAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveProjectActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_archives_project_for_admin(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $admin->id,
            'role' => Role::Admin->value,
        ]);

        $project = Project::factory()->for($organization)->create([
            'archived_at' => null,
        ]);

        $action = app(ArchiveProjectAction::class);

        $archived = $action->archive(
            actor: $admin,
            project: $project,
        );

        $this->assertNotNull($archived->archived_at);
    }

    public function test_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $nonMember = User::factory()->create();
        $project = Project::factory()->for($organization)->create([
            'archived_at' => null,
        ]);

        $action = app(ArchiveProjectAction::class);

        $this->expectException(AuthorizationException::class);

        $action->archive(
            actor: $nonMember,
            project: $project,
        );
    }
}
