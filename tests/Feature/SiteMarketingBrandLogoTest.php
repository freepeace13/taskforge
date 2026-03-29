<?php

namespace Tests\Feature;

use Tests\TestCase;

class SiteMarketingBrandLogoTest extends TestCase
{
    public function test_site_home_includes_brand_logo_in_header(): void
    {
        $response = $this->get(route('site.home'));

        $response->assertOk();
        $response->assertSee('brand_64x64.png', false);
    }
}
