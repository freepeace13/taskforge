<?php

namespace Tests\Feature\Project;

use App\Enums\Role;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAbbrevTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_store_generates_unique_abbrev_from_name(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);

        $this->actingAs($user)->post(route('projects.store', [
            'org' => $organization->slug,
        ]), [
            'name' => 'Campaign Builder',
            'description' => null,
        ])->assertRedirect();

        $this->actingAs($user)->post(route('projects.store', [
            'org' => $organization->slug,
        ]), [
            'name' => 'Campaign Builder',
            'description' => null,
        ])->assertRedirect();

        $abbrevs = Project::query()
            ->where('organization_id', $organization->id)
            ->orderBy('id')
            ->pluck('abbrev')
            ->all();

        $this->assertSame(['CB', 'CB2'], $abbrevs);
    }
}
