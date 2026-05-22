<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationStoreRequest;
use App\Http\Requests\EvaluationUpdateRequest;
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
            'course' => $course,
            'evaluations' => $evaluations,
        ]);
    }

    public function store(EvaluationStoreRequest $request, Course $course)
    {
        $maxPosition = (int) Evaluation::query()->where('course_id', $course->id)->max('position');

        Evaluation::query()->create([
            'course_id' => $course->id,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'max_score' => $request->validated('max_score'),
            'is_required' => (bool) $request->validated('is_required'),
            'position' => $maxPosition + 1,
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('courses.evaluations.index', $course->slug)
            ->with('status', 'Evaluation created.');
    }

    public function edit(Course $course, Evaluation $evaluation)
    {
        abort_unless($evaluation->course_id === $course->id, 404);

        return view('courses.evaluations.edit', [
            'course' => $course,
            'evaluation' => $evaluation,
        ]);
    }

    public function update(EvaluationUpdateRequest $request, Course $course, Evaluation $evaluation)
    {
        abort_unless($evaluation->course_id === $course->id, 404);

        $evaluation->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'max_score' => $request->validated('max_score'),
            'is_required' => (bool) $request->validated('is_required'),
        ]);

        return redirect()
            ->route('courses.evaluations.index', $course->slug)
            ->with('status', 'Evaluation updated.');
    }

    public function destroy(Course $course, Evaluation $evaluation)
    {
        abort_unless($evaluation->course_id === $course->id, 404);
        $evaluation->delete();

        return redirect()
            ->route('courses.evaluations.index', $course->slug)
            ->with('status', 'Evaluation deleted.');
    }
}
