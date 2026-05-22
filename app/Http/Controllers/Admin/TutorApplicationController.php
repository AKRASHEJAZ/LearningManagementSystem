<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TutorApplicationDecisionRequest;
use App\Models\CourseTutor;
use App\Models\TutorApplication;

class TutorApplicationController extends Controller
{
    public function index()
    {
        $applications = TutorApplication::query()
            ->with(['course', 'user'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.tutor-applications.index', [
            'applications' => $applications,
        ]);
    }

    public function approve(TutorApplicationDecisionRequest $request, TutorApplication $tutorApplication)
    {
        $tutorApplication->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->validated('review_notes'),
        ]);

        CourseTutor::query()->updateOrCreate(
            ['course_id' => $tutorApplication->course_id, 'user_id' => $tutorApplication->user_id],
            [
                'status' => 'active',
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
            ]
        );

        return back()->with('status', 'Tutor application approved.');
    }

    public function reject(TutorApplicationDecisionRequest $request, TutorApplication $tutorApplication)
    {
        $tutorApplication->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->validated('review_notes'),
        ]);

        return back()->with('status', 'Tutor application rejected.');
    }
}
