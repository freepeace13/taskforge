<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaKanbanTest extends TestCase
{
    public function test_inertia_kanban_route_renders_tasks_index_component(): void
    {
        $response = $this->get(route('tasks.index'));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Index'),
        );
    }
}
