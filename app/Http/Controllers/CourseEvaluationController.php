<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationStoreRequest;
use App\Models\Course;
use App\Models\Evaluation;

class CourseEvaluationController extends Controller
{
    public function index(Course $course)
    {
        $evaluations = Evaluation::query()
            ->where('course_id', $course->id)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return view('courses.evaluations.index', [
            'course'      => $course,
            'evaluations' => $evaluations,
        ]);
    }

    /**
     * Add a single evaluation to an existing course (admin only).
     * Used from the admin course edit page's "add more" form.
     */
    public function store(EvaluationStoreRequest $request, Course $course)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $maxPosition = (int) Evaluation::query()->where('course_id', $course->id)->max('position');

        Evaluation::query()->create([
            'course_id'   => $course->id,
            'title'       => $request->validated('title'),
            'description' => $request->validated('description'),
            'max_score'   => $request->validated('max_score'),
            'is_required' => (bool) $request->validated('is_required'),
            'position'    => $maxPosition + 1,
            'created_by'  => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.courses.edit', $course)
            ->with('status', 'Evaluation added.');
    }
}
