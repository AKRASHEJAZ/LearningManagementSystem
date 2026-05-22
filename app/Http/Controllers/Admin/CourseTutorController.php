<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseTutorStoreRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\User;
use Illuminate\Http\Request;

class CourseTutorController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $tutors = CourseTutor::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->orderByDesc('id')
            ->get();

        $existingTutorIds = $tutors->pluck('user_id')->all();

        $q = $request->query('q');
        $candidates = collect();
        if (is_string($q) && trim($q) !== '') {
            $term = trim($q);
            $candidates = User::query()
                ->where(function ($query) use ($term): void {
                    $query
                        ->where('name', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%');
                })
                ->where(function ($query): void {
                    $query->where('approval_status', 'approved')->orWhere('is_admin', true);
                })
                ->whereNotIn('id', $existingTutorIds ?: [0])
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'name', 'email', 'approval_status', 'is_admin']);
        }

        return view('admin.courses.tutors', [
            'course' => $course,
            'tutors' => $tutors,
            'q' => is_string($q) ? $q : '',
            'candidates' => $candidates,
        ]);
    }

    public function store(CourseTutorStoreRequest $request, Course $course)
    {
        $user = User::query()->findOrFail((int) $request->validated('user_id'));

        if (! $user->is_admin && $user->approval_status !== 'approved') {
            return redirect()->back()
                ->with('status', 'User must be approved before becoming a tutor.');
        }

        $hasStudentRecord = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['applied', 'accepted', 'completed'])
            ->exists();

        if ($hasStudentRecord) {
            return redirect()->back()
                ->with('status', 'This user is a student/applicant for this course and cannot be assigned as tutor.');
        }

        CourseTutor::query()->updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            [
                'status' => 'active',
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
            ]
        );

        return redirect()->back()->with('status', 'Tutor added.');
    }

    public function destroy(Course $course, CourseTutor $courseTutor)
    {
        abort_unless($courseTutor->course_id === $course->id, 404);
        $courseTutor->delete();

        return redirect()
            ->route('admin.courses.tutors', $course)
            ->with('status', 'Tutor removed.');
    }
}
