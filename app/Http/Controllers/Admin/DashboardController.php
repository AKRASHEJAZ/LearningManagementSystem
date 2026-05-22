<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\Setting;
use App\Models\TutorApplication;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'pending_users' => (int) User::query()->where('is_admin', false)->where('approval_status', 'pending')->count(),
            'approved_users' => (int) User::query()->where('is_admin', false)->where('approval_status', 'approved')->count(),
            'published_courses' => (int) Course::query()->where('status', 'published')->count(),
            'active_tutors' => (int) CourseTutor::query()->where('status', 'active')->distinct('user_id')->count('user_id'),
            'accepted_enrollments' => (int) CourseEnrollment::query()->where('status', 'accepted')->count(),
            'certificates_issued' => (int) Certificate::query()->where('status', 'active')->count(),
            'pending_tutor_apps' => (int) TutorApplication::query()->where('status', 'pending')->count(),
            'settings' => (int) Setting::query()->count(),
            'achievements' => (int) Achievement::query()->where('is_active', true)->count(),
        ];

        $recent = [
            'users_pending' => User::query()
                ->where('is_admin', false)
                ->where('approval_status', 'pending')
                ->orderByDesc('id')
                ->limit(5)
                ->get(['id', 'name', 'email', 'created_at']),
            'courses' => Course::query()
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get(['id', 'title', 'status', 'updated_at']),
            'certificates' => Certificate::query()
                ->with(['user:id,name', 'course:id,title'])
                ->orderByDesc('issued_at')
                ->limit(5)
                ->get(),
            'tutor_apps' => TutorApplication::query()
                ->with(['user:id,name,email', 'course:id,title'])
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'recent' => $recent,
        ]);
    }
}

