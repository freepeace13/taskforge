<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaDashboardTest extends TestCase
{
    public function test_inertia_dashboard_route_renders_dashboard_component(): void
    {
        $response = $this->get(route('inertia.dashboard'));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Dashboard')
                ->has('user')
                ->has('stats.projects')
        );
    }
}
