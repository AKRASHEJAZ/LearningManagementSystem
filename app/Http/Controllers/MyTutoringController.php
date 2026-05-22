<?php

namespace App\Http\Controllers;

use App\Models\CourseTutor;
use App\Models\TutorStudentAssignment;

class MyTutoringController extends Controller
{
    public function index()
    {
        $user = request()->user();
        abort_unless($user, 403);

        $courses = CourseTutor::query()
            ->with('course')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->get()
            ->map(fn (CourseTutor $t) => $t->course);

        $assignments = TutorStudentAssignment::query()
            ->with(['course', 'student'])
            ->where('tutor_user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->get();

        return view('tutoring.index', [
            'courses' => $courses,
            'assignments' => $assignments,
        ]);
    }
}
