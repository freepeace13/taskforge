<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaKanbanTest extends TestCase
{
    use RefreshDatabase;

    public function test_inertia_kanban_route_renders_tasks_index_component(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Index'),
        );
    }
}
