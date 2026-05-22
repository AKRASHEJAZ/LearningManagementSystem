<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\TutorStudentAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorStudentAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_staff_can_assign_tutor_to_accepted_student(): void
    {
        $admin = User::factory()->admin()->create();
        $tutor = User::factory()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        CourseTutor::query()->create([
            'course_id' => $course->id,
            'user_id' => $tutor->id,
            'status' => 'active',
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        CourseEnrollment::query()->create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'accepted',
            'accepted_at' => now(),
            'updated_by' => $admin->id,
        ]);

        $this
            ->actingAs($admin)
            ->post(route('courses.assignments.store', $course->slug, absolute: false), [
                'tutor_user_id' => $tutor->id,
                'student_user_id' => $student->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tutor_student_assignments', [
            'course_id' => $course->id,
            'tutor_user_id' => $tutor->id,
            'student_user_id' => $student->id,
            'status' => 'active',
        ]);
    }
}
