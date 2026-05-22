<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;

class MyLearningController extends Controller
{
    public function index()
    {
        $user = request()->user();
        abort_unless($user, 403);

        $enrollments = CourseEnrollment::query()
            ->with('course')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();

        return view('learning.index', [
            'enrollments' => $enrollments,
        ]);
    }
}
