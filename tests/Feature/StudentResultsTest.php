<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Evaluation;
use App\Models\EvaluationGrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentResultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepted_student_can_view_results_page_and_others_cannot(): void
    {
        $admin = User::factory()->admin()->create();
        $tutor = User::factory()->create();
        $student = User::factory()->create();
        $other = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        $enrollment = CourseEnrollment::query()->create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'accepted',
            'accepted_at' => now(),
            'updated_by' => $admin->id,
            'result_status' => 'learning',
        ]);

        $evaluation = Evaluation::query()->create([
            'course_id' => $course->id,
            'title' => 'Quiz 1',
            'description' => null,
            'max_score' => 10,
            'is_required' => true,
            'position' => 1,
            'created_by' => $admin->id,
        ]);

        EvaluationGrade::query()->create([
            'evaluation_id' => $evaluation->id,
            'course_id' => $course->id,
            'student_user_id' => $student->id,
            'graded_by_user_id' => $tutor->id,
            'score' => 8,
            'status' => 'passed',
            'feedback' => 'Good',
            'graded_at' => now(),
        ]);

        $this
            ->actingAs($student)
            ->get(route('courses.results.show', $course->slug, absolute: false))
            ->assertOk()
            ->assertSee('Results')
            ->assertSee('Quiz 1')
            ->assertSee('Good');

        $this
            ->actingAs($other)
            ->get(route('courses.results.show', $course->slug, absolute: false))
            ->assertForbidden();
    }
}
