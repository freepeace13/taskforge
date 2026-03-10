<?php

namespace Tests\Feature;

use Tests\TestCase;

class GuestRedirectTest extends TestCase
{
    public function test_guest_redirected_to_login_when_accessing_protected_page(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
