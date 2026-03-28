<?php

namespace Tests\Unit\Queries\Task;

use App\Enums\Role;
use App\Models\Project;
use App\Queries\Task\ListTaskHubOrganizationsQuery;
use App\Queries\Task\ListTaskHubOrganizationsQueryHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\Concerns\InteractsWithTenant;
use Tests\TestCase;

class ListTaskHubOrganizationsQueryHandlerTest extends TestCase
{
    use InteractsWithTenant;
    use RefreshDatabase;

    public function test_returns_member_organizations_with_active_projects(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        Project::factory()->for($organization)->create(['name' => 'Alpha', 'archived_at' => null]);
        Project::factory()->for($organization)->create(['name' => 'Beta', 'archived_at' => now()]);

        $handler = new ListTaskHubOrganizationsQueryHandler;
        $result = $handler->handle(new ListTaskHubOrganizationsQuery(userId: $user->id));

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame($organization->id, $result->first()->id);
        $this->assertCount(1, $result->first()->projects);
        $this->assertSame('Alpha', $result->first()->projects->first()->name);
    }
}
