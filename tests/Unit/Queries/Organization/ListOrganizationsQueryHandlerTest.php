<?php

namespace Tests\Unit\Queries\Organization;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use App\Queries\Organization\ListOrganizationsQuery;
use App\Queries\Organization\ListOrganizationsQueryHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\CursorPaginator;
use Tests\TestCase;

class ListOrganizationsQueryHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_user_organizations_with_cursor_pagination(): void
    {
        $user = User::factory()->create();
        $alpha = Organization::factory()->create(['name' => 'Alpha Corp']);
        $beta = Organization::factory()->create(['name' => 'Beta LLC']);
        $alpha->members()->attach($user->id, ['role' => Role::Member->value]);
        $beta->members()->attach($user->id, ['role' => Role::Member->value]);

        $handler = new ListOrganizationsQueryHandler;
        $result = $handler->handle(new ListOrganizationsQuery(userId: $user->id, search: null));

        $this->assertInstanceOf(CursorPaginator::class, $result);
        $this->assertGreaterThanOrEqual(2, $result->count());
    }

    public function test_filters_by_search_on_name(): void
    {
        $user = User::factory()->create();
        $match = Organization::factory()->create(['name' => 'UniqueSearchOrg']);
        $other = Organization::factory()->create(['name' => 'Other']);
        $match->members()->attach($user->id, ['role' => Role::Member->value]);
        $other->members()->attach($user->id, ['role' => Role::Member->value]);

        $handler = new ListOrganizationsQueryHandler;
        $result = $handler->handle(new ListOrganizationsQuery(userId: $user->id, search: 'UniqueSearch'));

        $this->assertInstanceOf(CursorPaginator::class, $result);
        $names = collect($result->items())->pluck('name')->all();
        $this->assertContains('UniqueSearchOrg', $names);
        $this->assertNotContains('Other', $names);
    }
}
