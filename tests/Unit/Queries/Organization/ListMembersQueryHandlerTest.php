<?php

namespace Tests\Unit\Queries\Organization;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use App\Queries\Organization\ListMembersQuery;
use App\Queries\Organization\ListMembersQueryHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\CursorPaginator;
use Tests\TestCase;

class ListMembersQueryHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_members_with_cursor_pagination(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);
        $member = User::factory()->create(['name' => 'Member User']);
        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $handler = new ListMembersQueryHandler;
        $result = $handler->handle(new ListMembersQuery(
            organizationId: $organization->id,
            search: null,
            roles: null,
        ));

        $this->assertInstanceOf(CursorPaginator::class, $result);
        $ids = collect($result->items())->pluck('id')->all();
        $this->assertContains($owner->id, $ids);
        $this->assertContains($member->id, $ids);
    }

    public function test_filters_by_role(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);
        $member = User::factory()->create();
        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $handler = new ListMembersQueryHandler;
        $result = $handler->handle(new ListMembersQuery(
            organizationId: $organization->id,
            search: null,
            roles: Role::Member->value,
        ));

        $this->assertInstanceOf(CursorPaginator::class, $result);
        $ids = collect($result->items())->pluck('id')->all();
        $this->assertContains($member->id, $ids);
        $this->assertNotContains($owner->id, $ids);
    }

    public function test_filters_by_search_on_name_or_email(): void
    {
        $organization = Organization::factory()->create();
        $match = User::factory()->create(['name' => 'Zeta Person', 'email' => 'zeta@example.com']);
        $other = User::factory()->create(['name' => 'Alpha', 'email' => 'alpha@example.com']);
        $organization->members()->attach($match->id, ['role' => Role::Member->value]);
        $organization->members()->attach($other->id, ['role' => Role::Member->value]);

        $handler = new ListMembersQueryHandler;
        $result = $handler->handle(new ListMembersQuery(
            organizationId: $organization->id,
            search: 'Zeta',
            roles: null,
        ));

        $ids = collect($result->items())->pluck('id')->all();
        $this->assertContains($match->id, $ids);
        $this->assertNotContains($other->id, $ids);
    }
}
