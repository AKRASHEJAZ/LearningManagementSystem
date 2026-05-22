<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseTutor;
use App\Models\TutorApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_apply_to_be_tutor_and_admin_can_approve(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        $this
            ->actingAs($student)
            ->post(route('courses.tutor-apply', $course->slug, absolute: false), [
                'motivation' => 'I can help.',
            ])
            ->assertRedirect(route('courses.show', $course->slug, absolute: false));

        $app = TutorApplication::query()->where('course_id', $course->id)->where('user_id', $student->id)->first();
        $this->assertNotNull($app);
        $this->assertSame('pending', $app->status);

        $this
            ->actingAs($admin)
            ->post(route('admin.tutor-applications.approve', $app, absolute: false))
            ->assertRedirect();

        $this->assertSame('approved', $app->refresh()->status);
        $this->assertDatabaseHas('course_tutors', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);
    }
}
