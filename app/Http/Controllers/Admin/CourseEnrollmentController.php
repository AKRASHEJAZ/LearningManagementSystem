<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseBulkCompleteRequest;
use App\Http\Requests\Admin\CourseEnrollmentDecisionRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Evaluation;
use App\Models\EvaluationGrade;
use App\Services\CertificateService;

class CourseEnrollmentController extends Controller
{
    public function __construct(private readonly CertificateService $certificateService)
    {
    }

    public function index(Course $course)
    {
        $applied = CourseEnrollment::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->where('status', 'applied')
            ->orderBy('applied_at')
            ->get();

        $accepted = CourseEnrollment::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->where('status', 'accepted')
            ->orderBy('accepted_at')
            ->get();

        $completed = CourseEnrollment::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->get();

        $capacity = $course->max_participants;
        $isFull = is_int($capacity) && $capacity > 0 && $accepted->count() >= $capacity;

        $completedScores = [];
        if ($completed->count() > 0) {
            $evaluationMaxTotal = (int) Evaluation::query()
                ->where('course_id', $course->id)
                ->sum('max_score');

            $studentIds = $completed->pluck('user_id')->all();
            $gradeRows = EvaluationGrade::query()
                ->where('course_id', $course->id)
                ->whereIn('student_user_id', $studentIds)
                ->get(['student_user_id', 'score']);

            $sumByStudent = [];
            foreach ($gradeRows as $row) {
                if ($row->score === null) {
                    continue;
                }
                $sumByStudent[$row->student_user_id] = ($sumByStudent[$row->student_user_id] ?? 0) + (int) $row->score;
            }

            foreach ($studentIds as $studentId) {
                $completedScores[$studentId] = [
                    'sum' => $sumByStudent[$studentId] ?? null,
                    'max' => $evaluationMaxTotal > 0 ? $evaluationMaxTotal : null,
                ];
            }
        }

        $view = request()->user()?->isAdmin()
            ? 'admin.courses.participants'
            : 'courses.manage';

        return view($view, [
            'course' => $course,
            'applied' => $applied,
            'accepted' => $accepted,
            'completed' => $completed,
            'isFull' => $isFull,
            'completedScores' => $completedScores,
        ]);
    }

    public function accept(CourseEnrollmentDecisionRequest $request, Course $course, CourseEnrollment $enrollment)
    {
        abort_unless($enrollment->course_id === $course->id, 404);
        abort_if($course->isStarted(), 403);
        abort_unless($enrollment->status === 'applied', 403);

        $capacity = $course->max_participants;
        if (is_int($capacity) && $capacity > 0) {
            $acceptedCount = CourseEnrollment::query()
                ->where('course_id', $course->id)
                ->where('status', 'accepted')
                ->count();
            abort_if($acceptedCount >= $capacity, 422);
        }

        $enrollment->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'updated_by' => $request->user()->id,
            'note' => $request->validated('note'),
        ]);

        return redirect()
            ->route('courses.manage', $course)
            ->with('status', 'Student accepted.');
    }

    public function reject(CourseEnrollmentDecisionRequest $request, Course $course, CourseEnrollment $enrollment)
    {
        abort_unless($enrollment->course_id === $course->id, 404);
        abort_if($course->isStarted(), 403);
        abort_unless($enrollment->status === 'applied', 403);

        $enrollment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'updated_by' => $request->user()->id,
            'note' => $request->validated('note'),
        ]);

        return redirect()
            ->route('courses.manage', $course)
            ->with('status', 'Student rejected.');
    }

    public function start(Course $course)
    {
        abort_if($course->isStarted(), 422);
        abort_if($course->isEnded(), 422);

        $course->update(['started_at' => now()]);

        return redirect()
            ->route('courses.manage', $course)
            ->with('status', 'Course started. Applications are now closed.');
    }

    public function end(Course $course)
    {
        abort_if($course->isEnded(), 422);
        abort_if(! $course->isStarted(), 422);

        $course->update(['ended_at' => now()]);

        return redirect()
            ->route('courses.manage', $course)
            ->with('status', 'Course ended.');
    }

    public function bulkComplete(CourseBulkCompleteRequest $request, Course $course)
    {
        abort_if(! $course->isStarted(), 422);

        $ids = $request->validated('enrollment_ids');

        $enrollments = CourseEnrollment::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->whereIn('id', $ids)
            ->where('status', 'accepted')
            ->get();

        CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->whereIn('id', $ids)
            ->where('status', 'accepted')
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_by' => $request->user()->id,
            ]);

        foreach ($enrollments as $enrollment) {
            $this->certificateService->issueFor($enrollment->user, $course, $request->user());
        }

        return redirect()
            ->route('courses.manage', $course)
            ->with('status', 'Marked selected students as completed.');
    }
}
