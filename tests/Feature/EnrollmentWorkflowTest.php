<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_apply_and_staff_can_accept_then_start_course(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create([
            'created_by' => $admin->id,
            'max_participants' => 1,
            'started_at' => null,
            'ended_at' => null,
        ]);

        $this
            ->actingAs($student)
            ->post(route('courses.apply', $course->slug, absolute: false), ['note' => 'hi'])
            ->assertRedirect(route('courses.show', $course->slug, absolute: false));

        $enrollment = CourseEnrollment::query()->where('course_id', $course->id)->where('user_id', $student->id)->first();
        $this->assertNotNull($enrollment);
        $this->assertSame('applied', $enrollment->status);

        $this
            ->actingAs($admin)
            ->post(route('courses.manage.accept', [$course->slug, $enrollment->id], absolute: false))
            ->assertRedirect(route('courses.manage', $course->slug, absolute: false));

        $this->assertSame('accepted', $enrollment->refresh()->status);

        $this
            ->actingAs($admin)
            ->post(route('courses.manage.start', $course->slug, absolute: false))
            ->assertRedirect(route('courses.manage', $course->slug, absolute: false));

        $this->assertNotNull($course->refresh()->started_at);
    }

    public function test_tutor_can_access_manage_page(): void
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
            ->get(route('courses.manage', $course->slug, absolute: false))
            ->assertOk()
            ->assertDontSee('System settings');
    }
}
