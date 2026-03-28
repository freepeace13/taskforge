<?php

namespace Tests\Unit\Queries\Task;

use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Queries\Task\ListTasksQuery;
use App\Queries\Task\ListTasksQueryHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ListTasksQueryHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_tasks_for_project_newest_first(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $first = Task::factory()->for($project)->create();
        $second = Task::factory()->for($project)->create();

        $handler = new ListTasksQueryHandler;
        $paginator = $handler->handle(new ListTasksQuery(projectId: $project->id, perPage: 15));

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertSame(2, $paginator->total());
        $this->assertSame($second->id, $paginator->items()[0]->id);
        $this->assertSame($first->id, $paginator->items()[1]->id);
    }

    public function test_respects_per_page(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        Task::factory()->for($project)->count(3)->create();

        $handler = new ListTasksQueryHandler;
        $paginator = $handler->handle(new ListTasksQuery(projectId: $project->id, perPage: 2));

        $this->assertCount(2, $paginator->items());
        $this->assertSame(3, $paginator->total());
    }
}
