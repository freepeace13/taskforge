<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaProjectsTest extends TestCase
{
    public function test_inertia_projects_route_renders_projects_index_component(): void
    {
        $response = $this->get(route('projects.index'));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Projects/Index')
                ->has('projects')
        );
    }
}
