<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\CourseTutor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCourseTutor
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $courseParam = 'course'): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $course = $request->route($courseParam);
        $courseId = is_object($course) && isset($course->id) ? (int) $course->id : null;
        if (! $courseId) {
            abort(403);
        }

        $isTutor = CourseTutor::query()
            ->where('course_id', $courseId)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        abort_unless($isTutor, 403);

        return $next($request);
    }
}
