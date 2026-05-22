<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseStoreRequest;
use App\Http\Requests\Admin\CourseUpdateRequest;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::query()
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.courses.index', [
            'courses' => $courses,
        ]);
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(CourseStoreRequest $request)
    {
        $course = Course::query()->create([
            'title' => $request->validated('title'),
            'slug' => $request->preparedSlug(),
            'summary' => $request->validated('summary'),
            'duration' => $request->validated('duration'),
            'description' => $request->validated('description'),
            'completion_criteria' => $request->validated('completion_criteria'),
            'max_participants' => $request->validated('max_participants'),
            'status' => $request->validated('status'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.courses.edit', $course)
            ->with('status', 'Course created.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', [
            'course' => $course,
        ]);
    }

    public function update(CourseUpdateRequest $request, Course $course)
    {
        $course->update([
            'title' => $request->validated('title'),
            'slug' => $request->preparedSlug(),
            'summary' => $request->validated('summary'),
            'duration' => $request->validated('duration'),
            'description' => $request->validated('description'),
            'completion_criteria' => $request->validated('completion_criteria'),
            'max_participants' => $request->validated('max_participants'),
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('admin.courses.edit', $course)
            ->with('status', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('status', 'Course deleted.');
    }
}
