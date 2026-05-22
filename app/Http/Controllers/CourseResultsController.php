<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Certificate;
use App\Models\Evaluation;
use App\Models\EvaluationGrade;
use App\Services\CertificateService;

class CourseResultsController extends Controller
{
    public function __construct(private readonly CertificateService $certificateService)
    {
    }

    public function show(Course $course)
    {
        $user = request()->user();
        abort_unless($user, 403);

        abort_unless($course->isPublished(), 404);

        $enrollment = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['accepted', 'completed'])
            ->first();

        abort_unless($enrollment, 403);

        return $this->renderResults($course, $enrollment, $user->id);
    }

    public function showForTutor(Course $course, CourseEnrollment $enrollment)
    {
        abort_unless($enrollment->course_id === $course->id, 404);
        abort_unless(in_array($enrollment->status, ['accepted', 'completed'], true), 404);

        return $this->renderResults($course, $enrollment, $enrollment->user_id);
    }

    private function renderResults(Course $course, CourseEnrollment $enrollment, int $studentUserId)
    {
        $evaluations = Evaluation::query()
            ->where('course_id', $course->id)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $grades = EvaluationGrade::query()
            ->where('course_id', $course->id)
            ->where('student_user_id', $studentUserId)
            ->get()
            ->keyBy('evaluation_id');

        $certificate = Certificate::query()
            ->where('course_id', $course->id)
            ->where('user_id', $studentUserId)
            ->first();

        // Backfill certificates for previously completed enrollments (e.g., completed before certificates feature landed).
        if ($enrollment->status === 'completed' && ! $certificate) {
            $certificate = $this->certificateService->issueFor($enrollment->user, $course);
        }

        return view('courses.results.show', [
            'course' => $course,
            'enrollment' => $enrollment,
            'evaluations' => $evaluations,
            'grades' => $grades,
            'studentUserId' => $studentUserId,
            'certificate' => $certificate,
        ]);
    }
}
