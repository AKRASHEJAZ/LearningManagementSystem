<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Evaluation;
use App\Models\EvaluationGrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_certificate_verification_page_is_accessible_and_shows_performance(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        $certificate = Certificate::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'certificate_number' => 'C-TEST-123456',
            'user_id' => $student->id,
            'course_id' => $course->id,
            'issued_at' => now(),
            'status' => 'active',
        ]);

        $e1 = Evaluation::query()->create([
            'course_id' => $course->id,
            'title' => 'Quiz 1',
            'description' => 'Basics',
            'max_score' => 10,
            'is_required' => true,
            'position' => 1,
            'created_by' => $student->id,
        ]);

        $e2 = Evaluation::query()->create([
            'course_id' => $course->id,
            'title' => 'Project',
            'description' => null,
            'max_score' => 20,
            'is_required' => true,
            'position' => 2,
            'created_by' => $student->id,
        ]);

        EvaluationGrade::query()->create([
            'evaluation_id' => $e1->id,
            'course_id' => $course->id,
            'student_user_id' => $student->id,
            'graded_by_user_id' => $student->id,
            'score' => 8,
            'status' => 'passed',
            'feedback' => 'Good job',
            'graded_at' => now(),
        ]);

        EvaluationGrade::query()->create([
            'evaluation_id' => $e2->id,
            'course_id' => $course->id,
            'student_user_id' => $student->id,
            'graded_by_user_id' => $student->id,
            'score' => 15,
            'status' => 'learning',
            'feedback' => null,
            'graded_at' => now(),
        ]);

        $this
            ->get(route('certificates.verify.show', $certificate, absolute: false))
            ->assertOk()
            ->assertSee('Certificate verification')
            ->assertSee($student->name)
            ->assertSee('Quiz 1')
            ->assertSee('Project')
            ->assertDontSee($student->email);
    }

    public function test_public_certificate_pdf_download_returns_pdf(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        $certificate = Certificate::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'certificate_number' => 'C-TEST-123456',
            'user_id' => $student->id,
            'course_id' => $course->id,
            'issued_at' => now(),
            'status' => 'active',
        ]);

        $response = $this->get(route('certificates.verify.pdf', $certificate, absolute: false));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition');
    }
}
