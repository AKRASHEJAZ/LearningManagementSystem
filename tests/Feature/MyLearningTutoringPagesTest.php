<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\TutorStudentAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyLearningTutoringPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_my_learning_page_lists_enrollments(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        CourseEnrollment::query()->create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'accepted',
            'accepted_at' => now(),
            'updated_by' => $admin->id,
        ]);

        $this
            ->actingAs($student)
            ->get(route('learning.index', absolute: false))
            ->assertOk()
            ->assertSee($course->title);
    }

    public function test_my_tutoring_page_lists_assigned_students(): void
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

        TutorStudentAssignment::query()->create([
            'course_id' => $course->id,
            'tutor_user_id' => $tutor->id,
            'student_user_id' => $student->id,
            'status' => 'active',
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        $this
            ->actingAs($tutor)
            ->get(route('tutoring.index', absolute: false))
            ->assertOk()
            ->assertSee($student->email)
            ->assertSee($course->title);
    }
}
