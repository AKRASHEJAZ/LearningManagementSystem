<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAchievementsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_achievement_with_specific_courses_rule(): void
    {
        $admin = User::factory()->admin()->create();
        $courseA = Course::factory()->published()->create(['created_by' => $admin->id, 'title' => 'Git Basics', 'slug' => 'git-basics']);
        $courseB = Course::factory()->published()->create(['created_by' => $admin->id, 'title' => 'Git Advanced', 'slug' => 'git-advanced']);

        $this
            ->actingAs($admin)
            ->post(route('admin.achievements.store', absolute: false), [
                'key' => 'git.guru',
                'name' => 'Git Guru',
                'description' => 'Completed Git Basics + Git Advanced',
                'icon' => 'crown',
                'tier' => 'gold',
                'points' => 200,
                'is_active' => true,
                'rule_type' => 'specific_courses',
                'rule_is_active' => true,
                'course_ids' => [$courseA->id, $courseB->id],
            ])
            ->assertRedirect();

        $achievement = Achievement::query()->where('key', 'git.guru')->first();
        $this->assertNotNull($achievement);
        $this->assertNotNull($achievement->rule);
        $this->assertSame('specific_courses', $achievement->rule->type);
        $this->assertCount(2, $achievement->rule->courses);
    }

    public function test_admin_can_remove_rule_by_setting_rule_type_empty(): void
    {
        $admin = User::factory()->admin()->create();
        $courseA = Course::factory()->published()->create(['created_by' => $admin->id, 'title' => 'Git Basics', 'slug' => 'git-basics']);

        $this
            ->actingAs($admin)
            ->post(route('admin.achievements.store', absolute: false), [
                'key' => 'git.guru',
                'name' => 'Git Guru',
                'description' => null,
                'icon' => 'crown',
                'tier' => 'gold',
                'points' => 200,
                'is_active' => true,
                'rule_type' => 'specific_courses',
                'rule_is_active' => true,
                'course_ids' => [$courseA->id],
            ])
            ->assertRedirect();

        $achievement = Achievement::query()->where('key', 'git.guru')->firstOrFail();
        $this->assertNotNull($achievement->rule);

        $this
            ->actingAs($admin)
            ->put(route('admin.achievements.update', $achievement, absolute: false), [
                'key' => 'git.guru',
                'name' => 'Git Guru',
                'description' => null,
                'icon' => 'crown',
                'tier' => 'gold',
                'points' => 200,
                'is_active' => true,
                'rule_type' => '',
                'rule_is_active' => true,
            ])
            ->assertRedirect();

        $achievement->refresh();
        $this->assertNull($achievement->rule);
    }
}
