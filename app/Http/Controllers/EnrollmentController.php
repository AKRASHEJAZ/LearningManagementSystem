<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentApplyRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;

class EnrollmentController extends Controller
{
    public function apply(EnrollmentApplyRequest $request, Course $course)
    {
        abort_unless($course->applicationsOpen(), 403);

        $isTutor = CourseTutor::query()
            ->where('course_id', $course->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->exists();

        if ($isTutor) {
            return redirect()
                ->route('courses.show', $course->slug)
                ->with('status', 'You are a tutor for this course and cannot apply as a student.');
        }

        $existing = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing && in_array($existing->status, ['applied', 'accepted', 'completed'], true)) {
            return redirect()
                ->route('courses.show', $course->slug)
                ->with('status', 'You already applied.');
        }

        CourseEnrollment::query()->updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $request->user()->id,
            ],
            [
                'status' => 'applied',
                'applied_at' => now(),
                'accepted_at' => null,
                'rejected_at' => null,
                'withdrawn_at' => null,
                'completed_at' => null,
                'updated_by' => $request->user()->id,
                'note' => $request->validated('note'),
            ]
        );

        return redirect()
            ->route('courses.show', $course->slug)
            ->with('status', 'Application submitted.');
    }

    public function withdraw(Course $course)
    {
        $user = request()->user();
        abort_unless($user, 403);

        $enrollment = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        abort_if($course->isStarted(), 403);

        $enrollment->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
            'updated_by' => $user->id,
        ]);

        return redirect()
            ->route('courses.show', $course->slug)
            ->with('status', 'Application withdrawn.');
    }
}
