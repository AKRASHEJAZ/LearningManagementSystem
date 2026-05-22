<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_users_are_redirected_to_approval_notice(): void
    {
        $user = User::factory()->pendingApproval()->create();

        $this
            ->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('approval.notice', absolute: false));

        $this
            ->actingAs($user)
            ->get('/profile')
            ->assertRedirect(route('approval.notice', absolute: false));
    }

    public function test_rejected_users_can_view_rejection_notice(): void
    {
        $user = User::factory()->rejected('Nope')->create();

        $this
            ->actingAs($user)
            ->get('/approval')
            ->assertOk()
            ->assertSee('not approved');
    }
}
