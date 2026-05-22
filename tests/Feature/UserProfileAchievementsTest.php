<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileAchievementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_shows_achievements_grid(): void
    {
        $viewer = User::factory()->create();
        $profileUser = User::factory()->create();

        $achievement = Achievement::query()->create([
            'key' => 'account.approved',
            'name' => 'Verified Member',
            'description' => 'Approved account',
            'icon' => 'shield',
            'points' => 10,
            'tier' => 'bronze',
            'is_active' => true,
        ]);

        UserAchievement::query()->create([
            'user_id' => $profileUser->id,
            'achievement_id' => $achievement->id,
            'earned_at' => now(),
        ]);

        $this
            ->actingAs($viewer)
            ->get(route('users.show', $profileUser, absolute: false))
            ->assertOk()
            ->assertSee('Achievements')
            ->assertSee('Verified Member');
    }
}

