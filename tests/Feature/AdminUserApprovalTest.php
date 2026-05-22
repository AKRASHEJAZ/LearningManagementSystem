<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_a_pending_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->pendingApproval()->create();

        $this
            ->actingAs($admin)
            ->post(route('admin.user-approvals.approve', $user, absolute: false))
            ->assertRedirect(route('admin.user-approvals.show', $user, absolute: false));

        $this->assertSame('approved', $user->refresh()->approval_status);
        $this->assertNotNull($user->approved_at);
        $this->assertSame($admin->id, $user->approved_by);
    }

    public function test_admin_can_reject_a_pending_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->pendingApproval()->create();

        $this
            ->actingAs($admin)
            ->post(route('admin.user-approvals.reject', $user, absolute: false), [
                'rejection_reason' => 'Incomplete details',
            ])
            ->assertRedirect(route('admin.user-approvals.show', $user, absolute: false));

        $this->assertSame('rejected', $user->refresh()->approval_status);
        $this->assertSame('Incomplete details', $user->rejection_reason);
        $this->assertSame($admin->id, $user->rejected_by);
    }

    public function test_non_admin_cannot_access_user_approvals(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('admin.user-approvals.index', absolute: false))
            ->assertForbidden();
    }
}
