<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorCanViewStudentResultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tutor_can_view_student_results_page(): void
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

        $enrollment = CourseEnrollment::query()->create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'accepted',
            'accepted_at' => now(),
            'updated_by' => $admin->id,
        ]);

        $this
            ->actingAs($tutor)
            ->get(route('courses.results.student', [$course->slug, $enrollment], absolute: false))
            ->assertOk()
            ->assertSee('Results');
    }
}
