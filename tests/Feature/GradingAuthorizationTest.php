<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseTutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_access_grading_unless_assigned_as_tutor(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        $this
            ->actingAs($admin)
            ->get(route('courses.grading.show', $course->slug, absolute: false))
            ->assertForbidden();
    }

    public function test_tutor_can_access_grading(): void
    {
        $admin = User::factory()->admin()->create();
        $tutor = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        CourseTutor::query()->create([
            'course_id' => $course->id,
            'user_id' => $tutor->id,
            'status' => 'active',
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        $this
            ->actingAs($tutor)
            ->get(route('courses.grading.show', $course->slug, absolute: false))
            ->assertOk();
    }
}
