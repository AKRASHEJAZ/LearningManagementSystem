<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Models\UserAchievement;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        abort_unless(request()->user()?->isApproved(), 403);

        $certificates = Certificate::query()
            ->with(['course:id,title,slug'])
            ->where('user_id', $user->id)
            ->orderByDesc('issued_at')
            ->limit(10)
            ->get();

        $progress = [
            'applied' => (int) CourseEnrollment::query()->where('user_id', $user->id)->where('status', 'applied')->count(),
            'accepted' => (int) CourseEnrollment::query()->where('user_id', $user->id)->where('status', 'accepted')->count(),
            'completed' => (int) CourseEnrollment::query()->where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        $allAchievements = Achievement::query()
            ->with(['rule.courses.course:id,title'])
            ->where('is_active', true)
            ->orderByRaw("case tier when 'platinum' then 1 when 'gold' then 2 when 'silver' then 3 else 4 end")
            ->orderBy('points')
            ->get();

        $earned = UserAchievement::query()
            ->with('achievement')
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('achievement_id');

        return view('users.show', [
            'profileUser' => $user,
            'progress' => $progress,
            'certificates' => $certificates,
            'allAchievements' => $allAchievements,
            'earned' => $earned,
        ]);
    }
}
