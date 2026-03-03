<?php

namespace Tests\Unit\Policies;

use App\Models\OrganizationInvite;
use App\Models\User;
use App\Policies\InvitationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_accept_allows_matching_email_and_denies_mismatch(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $invite = OrganizationInvite::factory()->create([
            'email' => 'user@example.com',
        ]);

        $policy = new InvitationPolicy;

        $this->assertTrue($policy->accept($user, $invite));

        $otherUser = User::factory()->create(['email' => 'other@example.com']);

        $this->assertTrue($policy->accept($otherUser, $invite)->denied());
    }
}
