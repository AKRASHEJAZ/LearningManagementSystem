<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentStoreRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTutor;
use App\Models\TutorStudentAssignment;
use Illuminate\Http\Request;

class CourseAssignmentController extends Controller
{
    public function index(Course $course)
    {
        $tutors = CourseTutor::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        $students = CourseEnrollment::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->whereIn('status', ['accepted', 'completed'])
            ->orderBy('accepted_at')
            ->get();

        $assignments = TutorStudentAssignment::query()
            ->with(['tutor', 'student'])
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->get()
            ->groupBy(fn (TutorStudentAssignment $a) => $a->student_user_id);

        return view('courses.assignments.index', [
            'course' => $course,
            'tutors' => $tutors,
            'students' => $students,
            'assignments' => $assignments,
        ]);
    }

    public function store(AssignmentStoreRequest $request, Course $course)
    {
        $tutorId = (int) $request->validated('tutor_user_id');
        $studentId = (int) $request->validated('student_user_id');

        $isTutor = CourseTutor::query()
            ->where('course_id', $course->id)
            ->where('user_id', $tutorId)
            ->where('status', 'active')
            ->exists();
        abort_unless($isTutor, 422);

        $isStudent = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $studentId)
            ->whereIn('status', ['accepted', 'completed'])
            ->exists();
        abort_unless($isStudent, 422);

        TutorStudentAssignment::query()->updateOrCreate(
            [
                'course_id' => $course->id,
                'tutor_user_id' => $tutorId,
                'student_user_id' => $studentId,
            ],
            [
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
                'status' => 'active',
            ]
        );

        return redirect()->back()->with('status', 'Tutor assigned to student.');
    }

    public function destroy(Course $course, TutorStudentAssignment $assignment)
    {
        abort_unless($assignment->course_id === $course->id, 404);

        $assignment->update([
            'status' => 'ended',
        ]);

        return redirect()->back()->with('status', 'Assignment ended.');
    }
}
