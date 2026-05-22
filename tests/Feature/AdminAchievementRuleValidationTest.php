<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAchievementRuleValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_specific_courses_rule_requires_at_least_one_course(): void
    {
        $admin = User::factory()->admin()->create();

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
                'course_ids' => [],
            ])
            ->assertSessionHasErrors(['course_ids']);
    }

    public function test_course_count_rule_requires_min_completions(): void
    {
        $admin = User::factory()->admin()->create();

        $this
            ->actingAs($admin)
            ->post(route('admin.achievements.store', absolute: false), [
                'key' => 'learner.level1',
                'name' => 'Level 1',
                'description' => null,
                'icon' => 'badge',
                'tier' => 'bronze',
                'points' => 10,
                'is_active' => true,
                'rule_type' => 'course_count',
                'rule_is_active' => true,
                // min_course_completions missing
            ])
            ->assertSessionHasErrors(['min_course_completions']);
    }
}

