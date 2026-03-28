<?php

namespace Tests\Unit\Queries\Project;

use App\Models\Organization;
use App\Models\Project;
use App\Queries\Project\ListProjectsQuery;
use App\Queries\Project\ListProjectsQueryHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ListProjectsQueryHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_only_non_archived_projects_when_filter_is_active(): void
    {
        $organization = Organization::factory()->create();
        $active = Project::factory()->for($organization)->create(['archived_at' => null]);
        Project::factory()->for($organization)->create([
            'archived_at' => now(),
        ]);

        $handler = new ListProjectsQueryHandler;
        $paginator = $handler->handle(new ListProjectsQuery(
            organizationId: $organization->id,
            archivedFilter: 'active',
            perPage: 15,
        ));

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertSame(1, $paginator->total());
        $this->assertSame($active->id, $paginator->items()[0]->id);
    }

    public function test_lists_only_archived_projects_when_filter_is_archived(): void
    {
        $organization = Organization::factory()->create();
        Project::factory()->for($organization)->create(['archived_at' => null]);
        $archived = Project::factory()->for($organization)->create([
            'archived_at' => now(),
        ]);

        $handler = new ListProjectsQueryHandler;
        $paginator = $handler->handle(new ListProjectsQuery(
            organizationId: $organization->id,
            archivedFilter: 'archived',
            perPage: 15,
        ));

        $this->assertSame(1, $paginator->total());
        $this->assertSame($archived->id, $paginator->items()[0]->id);
    }

    public function test_scopes_to_organization(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        $projectA = Project::factory()->for($orgA)->create(['archived_at' => null]);
        Project::factory()->for($orgB)->create(['archived_at' => null]);

        $handler = new ListProjectsQueryHandler;
        $paginator = $handler->handle(new ListProjectsQuery(
            organizationId: $orgA->id,
            archivedFilter: 'active',
            perPage: 15,
        ));

        $this->assertSame(1, $paginator->total());
        $this->assertSame($projectA->id, $paginator->items()[0]->id);
    }

    public function test_orders_by_id_descending(): void
    {
        $organization = Organization::factory()->create();
        $older = Project::factory()->for($organization)->create(['archived_at' => null]);
        $newer = Project::factory()->for($organization)->create(['archived_at' => null]);

        $handler = new ListProjectsQueryHandler;
        $paginator = $handler->handle(new ListProjectsQuery(
            organizationId: $organization->id,
            archivedFilter: 'active',
            perPage: 15,
        ));

        $this->assertSame($newer->id, $paginator->items()[0]->id);
        $this->assertSame($older->id, $paginator->items()[1]->id);
    }

    public function test_respects_per_page(): void
    {
        $organization = Organization::factory()->create();
        Project::factory()->for($organization)->count(3)->create(['archived_at' => null]);

        $handler = new ListProjectsQueryHandler;
        $paginator = $handler->handle(new ListProjectsQuery(
            organizationId: $organization->id,
            archivedFilter: 'active',
            perPage: 2,
        ));

        $this->assertCount(2, $paginator->items());
        $this->assertSame(3, $paginator->total());
    }
}
