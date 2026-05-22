<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\TutorApplication;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::query()
            ->where('status', 'published')
            ->orderBy('title')
            ->paginate(18);

        return view('courses.index', [
            'courses' => $courses,
        ]);
    }

    public function show(Course $course)
    {
        abort_unless($course->status === 'published', 404);

        $userId = request()->user()?->id;
        $enrollment = null;
        if ($userId) {
            $enrollment = CourseEnrollment::query()
                ->where('course_id', $course->id)
                ->where('user_id', $userId)
                ->first();
        }

        $acceptedCount = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('status', 'accepted')
            ->count();

        $isStaff = false;
        if (request()->user()) {
            $isStaff = request()->user()->isAdmin()
                || CourseTutor::query()
                    ->where('course_id', $course->id)
                    ->where('user_id', request()->user()->id)
                    ->where('status', 'active')
                    ->exists();
        }

        $tutorApplication = null;
        if (request()->user()) {
            $tutorApplication = TutorApplication::query()
                ->where('course_id', $course->id)
                ->where('user_id', request()->user()->id)
                ->first();
        }

        return view('courses.show', [
            'course' => $course,
            'enrollment' => $enrollment,
            'acceptedCount' => $acceptedCount,
            'isStaff' => $isStaff,
            'tutorApplication' => $tutorApplication,
        ]);
    }
}
