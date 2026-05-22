<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorStudentExclusivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_tutor_cannot_apply_as_student_for_same_course(): void
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
            ->post(route('courses.apply', $course->slug, absolute: false), ['note' => 'hi'])
            ->assertRedirect(route('courses.show', $course->slug, absolute: false));

        $this->assertDatabaseMissing('course_enrollments', [
            'course_id' => $course->id,
            'user_id' => $tutor->id,
            'status' => 'applied',
        ]);
    }

    public function test_admin_cannot_assign_student_as_tutor_for_same_course(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        CourseEnrollment::query()->create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'applied',
            'applied_at' => now(),
            'updated_by' => $student->id,
        ]);

        $this
            ->actingAs($admin)
            ->post(route('admin.courses.tutors.store', $course, absolute: false), [
                'user_id' => $student->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('course_tutors', [
            'course_id' => $course->id,
            'user_id' => $student->id,
        ]);
    }
}
