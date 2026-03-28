<?php

namespace Tests\Unit\Queries\Organization;

use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Queries\Organization\ListOrganizationInvitationsQuery;
use App\Queries\Organization\ListOrganizationInvitationsQueryHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\CursorPaginator;
use Tests\TestCase;

class ListOrganizationInvitationsQueryHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_pending_invitations(): void
    {
        $organization = Organization::factory()->create();
        OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'pending@example.com',
            'accepted_at' => null,
            'expires_at' => now()->addDay(),
        ]);
        OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'accepted@example.com',
            'accepted_at' => now(),
        ]);

        $handler = new ListOrganizationInvitationsQueryHandler;
        $result = $handler->handle(new ListOrganizationInvitationsQuery(
            organizationId: $organization->id,
            status: 'pending',
            search: null,
            perPage: 15,
        ));

        $this->assertInstanceOf(CursorPaginator::class, $result);
        $emails = collect($result->items())->pluck('email')->all();
        $this->assertContains('pending@example.com', $emails);
        $this->assertNotContains('accepted@example.com', $emails);
    }

    public function test_lists_accepted_invitations(): void
    {
        $organization = Organization::factory()->create();
        OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'gone@example.com',
            'accepted_at' => null,
        ]);
        OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'done@example.com',
            'accepted_at' => now(),
        ]);

        $handler = new ListOrganizationInvitationsQueryHandler;
        $result = $handler->handle(new ListOrganizationInvitationsQuery(
            organizationId: $organization->id,
            status: 'accepted',
            search: null,
            perPage: 15,
        ));

        $this->assertInstanceOf(CursorPaginator::class, $result);
        $emails = collect($result->items())->pluck('email')->all();
        $this->assertContains('done@example.com', $emails);
        $this->assertNotContains('gone@example.com', $emails);
    }

    public function test_filters_by_email_search(): void
    {
        $organization = Organization::factory()->create();
        OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'findme@example.com',
            'accepted_at' => null,
            'expires_at' => now()->addDay(),
        ]);
        OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'other@example.com',
            'accepted_at' => null,
            'expires_at' => now()->addDay(),
        ]);

        $handler = new ListOrganizationInvitationsQueryHandler;
        $result = $handler->handle(new ListOrganizationInvitationsQuery(
            organizationId: $organization->id,
            status: 'pending',
            search: 'findme',
            perPage: 15,
        ));

        $emails = collect($result->items())->pluck('email')->all();
        $this->assertSame(['findme@example.com'], $emails);
    }
}
