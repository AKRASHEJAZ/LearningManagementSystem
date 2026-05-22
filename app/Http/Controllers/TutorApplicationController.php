<?php

namespace App\Http\Controllers;

use App\Http\Requests\TutorApplicationStoreRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\TutorApplication;

class TutorApplicationController extends Controller
{
    public function store(TutorApplicationStoreRequest $request, Course $course)
    {
        abort_unless($course->isPublished(), 404);

        $user = $request->user();

        $isTutor = CourseTutor::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
        abort_if($isTutor, 422);

        $hasStudentRecord = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['applied', 'accepted', 'completed'])
            ->exists();
        abort_if($hasStudentRecord, 422);

        TutorApplication::query()->updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            [
                'motivation' => $request->validated('motivation'),
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ]
        );

        return redirect()
            ->route('courses.show', $course->slug)
            ->with('status', 'Tutor application submitted.');
    }
}
