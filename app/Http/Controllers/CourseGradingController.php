<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentResultUpdateRequest;
use App\Http\Requests\GradeUpsertRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Evaluation;
use App\Models\EvaluationGrade;
use App\Services\CertificateService;

class CourseGradingController extends Controller
{
    public function __construct(private readonly CertificateService $certificateService)
    {
    }

    public function show(Course $course)
    {
        $evaluations = Evaluation::query()
            ->where('course_id', $course->id)
            ->orderBy('position')
            ->get();

        $user = request()->user();
        $studentQuery = CourseEnrollment::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->where('status', 'accepted')
            ->orderBy('accepted_at');

        if (!$user->isAdmin()) {
            $assignedStudentIds = \App\Models\TutorStudentAssignment::query()
                ->where('course_id', $course->id)
                ->where('tutor_user_id', $user->id)
                ->where('status', 'active')
                ->pluck('student_user_id');

            $studentQuery->whereIn('user_id', $assignedStudentIds);
        }

        $students = $studentQuery->get();

        $grades = EvaluationGrade::query()
            ->where('course_id', $course->id)
            ->whereIn('student_user_id', $students->pluck('user_id'))
            ->get()
            ->groupBy(fn (EvaluationGrade $g) => $g->evaluation_id.':'.$g->student_user_id);

        return view('courses.grading.show', [
            'course' => $course,
            'evaluations' => $evaluations,
            'students' => $students,
            'grades' => $grades,
        ]);
    }

    public function upsertGrade(GradeUpsertRequest $request, Course $course, Evaluation $evaluation, CourseEnrollment $enrollment)
    {
        abort_unless($evaluation->course_id === $course->id, 404);
        abort_unless($enrollment->course_id === $course->id, 404);
        abort_unless($enrollment->status === 'accepted', 422);

        EvaluationGrade::query()->updateOrCreate(
            ['evaluation_id' => $evaluation->id, 'student_user_id' => $enrollment->user_id],
            [
                'course_id' => $course->id,
                'graded_by_user_id' => $request->user()->id,
                'score' => $request->validated('score'),
                'status' => $request->validated('status'),
                'feedback' => $request->validated('feedback'),
                'graded_at' => now(),
            ]
        );

        return redirect()->back()->with('status', 'Grade saved.');
    }

    public function updateResult(EnrollmentResultUpdateRequest $request, Course $course, CourseEnrollment $enrollment)
    {
        abort_unless($enrollment->course_id === $course->id, 404);
        abort_unless($enrollment->status === 'accepted', 422);

        $enrollment->update([
            'result_status' => $request->validated('result_status'),
            'result_updated_at' => now(),
            'result_updated_by' => $request->user()->id,
        ]);

        return redirect()->back()->with('status', 'Student status updated.');
    }

    public function markCompleted(Course $course, CourseEnrollment $enrollment)
    {
        abort_unless($enrollment->course_id === $course->id, 404);
        abort_unless($enrollment->status === 'accepted', 422);

        if (($enrollment->result_status ?? 'learning') !== 'passed') {
            return redirect()->back()->with('status', 'Set overall status to Passed before marking completed.');
        }

        $enrollment->update([
            'status' => 'completed',
            'completed_at' => now(),
            'updated_by' => request()->user()->id,
        ]);

        $this->certificateService->issueFor($enrollment->user, $course, request()->user());

        return redirect()->back()->with('status', 'Student marked completed.');
    }
}
