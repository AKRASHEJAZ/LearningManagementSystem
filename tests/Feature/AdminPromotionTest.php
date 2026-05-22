<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_promote_user_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['is_admin' => false]);

        $this
            ->actingAs($admin)
            ->post(route('admin.users.admin', $user, absolute: false), ['make_admin' => 1])
            ->assertRedirect();

        $this->assertTrue((bool) $user->refresh()->is_admin);
    }

    public function test_admin_cannot_demote_last_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['is_admin' => false]);

        $this
            ->actingAs($admin)
            ->post(route('admin.users.admin', $admin, absolute: false), ['make_admin' => 0])
            ->assertRedirect();

        $this->assertTrue((bool) $admin->refresh()->is_admin);
    }

    public function test_non_admin_cannot_promote_users(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create(['is_admin' => false]);

        $this
            ->actingAs($user)
            ->post(route('admin.users.admin', $target, absolute: false), ['make_admin' => 1])
            ->assertForbidden();
    }
}
