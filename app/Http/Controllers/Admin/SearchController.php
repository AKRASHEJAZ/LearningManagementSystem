<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim($request->string('q')->toString());

        $users = collect();
        $courses = collect();
        $certificates = collect();
        $achievements = collect();

        if ($q !== '') {
            $users = User::query()
                ->where(function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })
                ->orderByDesc('id')
                ->limit(10)
                ->get(['id', 'name', 'email', 'approval_status', 'is_admin']);

            $courses = Course::query()
                ->where('title', 'like', "%{$q}%")
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get(['id', 'title', 'status', 'slug']);

            $certificates = Certificate::query()
                ->with(['user:id,name,email', 'course:id,title'])
                ->where(function ($c) use ($q) {
                    $c->where('uuid', 'like', "%{$q}%")
                        ->orWhere('certificate_number', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
                        ->orWhereHas('course', fn ($co) => $co->where('title', 'like', "%{$q}%"));
                })
                ->orderByDesc('issued_at')
                ->limit(10)
                ->get();

            $achievements = Achievement::query()
                ->where(function ($a) use ($q) {
                    $a->where('name', 'like', "%{$q}%")
                        ->orWhere('key', 'like', "%{$q}%");
                })
                ->orderByDesc('is_active')
                ->orderBy('tier')
                ->limit(10)
                ->get(['id', 'name', 'key', 'tier', 'points', 'is_active']);
        }

        return view('admin.search', [
            'q' => $q,
            'users' => $users,
            'courses' => $courses,
            'certificates' => $certificates,
            'achievements' => $achievements,
        ]);
    }
}

