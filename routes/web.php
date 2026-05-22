<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Admin\CourseEnrollmentController;
use App\Http\Controllers\Admin\CourseTutorController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\TutorApplicationController;
use App\Http\Controllers\Admin\TutorApplicationController as AdminTutorApplicationController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SearchController as AdminSearchController;
use App\Http\Controllers\CourseEvaluationController;
use App\Http\Controllers\CourseGradingController;
use App\Http\Controllers\CourseResultsController;
use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\MyLearningController;
use App\Http\Controllers\MyTutoringController;
use App\Http\Controllers\PublicCertificateController;
use App\Http\Controllers\MyCertificatesController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/approval', function () {
    return view('approval.notice');
})->middleware('auth')->name('approval.notice');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'approved'])->name('dashboard');

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course:slug}/apply', [EnrollmentController::class, 'apply'])->name('courses.apply');
    Route::post('/courses/{course:slug}/withdraw', [EnrollmentController::class, 'withdraw'])->name('courses.withdraw');
    Route::post('/courses/{course:slug}/tutor-apply', [TutorApplicationController::class, 'store'])->name('courses.tutor-apply');
    Route::get('/courses/{course:slug}/results', [CourseResultsController::class, 'show'])->name('courses.results.show');
    Route::get('/courses/{course:slug}/results/student/{enrollment}', [CourseResultsController::class, 'showForTutor'])
        ->middleware('courseTutor:course')
        ->name('courses.results.student');

    Route::get('/courses/{course:slug}/manage', [CourseEnrollmentController::class, 'index'])
        ->middleware('courseStaff:course')
        ->name('courses.manage');
    Route::post('/courses/{course:slug}/manage/enrollments/{enrollment}/accept', [CourseEnrollmentController::class, 'accept'])
        ->middleware('courseStaff:course')
        ->name('courses.manage.accept');
    Route::post('/courses/{course:slug}/manage/enrollments/{enrollment}/reject', [CourseEnrollmentController::class, 'reject'])
        ->middleware('courseStaff:course')
        ->name('courses.manage.reject');
    Route::post('/courses/{course:slug}/manage/start', [CourseEnrollmentController::class, 'start'])
        ->middleware('courseStaff:course')
        ->name('courses.manage.start');
    Route::post('/courses/{course:slug}/manage/end', [CourseEnrollmentController::class, 'end'])
        ->middleware('courseStaff:course')
        ->name('courses.manage.end');
    Route::post('/courses/{course:slug}/manage/bulk-complete', [CourseEnrollmentController::class, 'bulkComplete'])
        ->middleware('courseStaff:course')
        ->name('courses.manage.bulk-complete');

    // Evaluations (course staff) + Grading (tutors only)
    Route::get('/courses/{course:slug}/evaluations', [CourseEvaluationController::class, 'index'])
        ->middleware('courseStaff:course')
        ->name('courses.evaluations.index');
    Route::post('/courses/{course:slug}/evaluations', [CourseEvaluationController::class, 'store'])
        ->middleware('courseStaff:course')
        ->name('courses.evaluations.store');
    Route::get('/courses/{course:slug}/evaluations/{evaluation}/edit', [CourseEvaluationController::class, 'edit'])
        ->middleware('courseStaff:course')
        ->name('courses.evaluations.edit');
    Route::put('/courses/{course:slug}/evaluations/{evaluation}', [CourseEvaluationController::class, 'update'])
        ->middleware('courseStaff:course')
        ->name('courses.evaluations.update');
    Route::delete('/courses/{course:slug}/evaluations/{evaluation}', [CourseEvaluationController::class, 'destroy'])
        ->middleware('courseStaff:course')
        ->name('courses.evaluations.destroy');

    Route::get('/courses/{course:slug}/grading', [CourseGradingController::class, 'show'])
        ->middleware('courseTutor:course')
        ->name('courses.grading.show');
    Route::post('/courses/{course:slug}/grading/{evaluation}/student/{enrollment}', [CourseGradingController::class, 'upsertGrade'])
        ->middleware('courseTutor:course')
        ->name('courses.grading.upsert');
    Route::post('/courses/{course:slug}/grading/student/{enrollment}/result', [CourseGradingController::class, 'updateResult'])
        ->middleware('courseTutor:course')
        ->name('courses.grading.result');
    Route::post('/courses/{course:slug}/grading/student/{enrollment}/complete', [CourseGradingController::class, 'markCompleted'])
        ->middleware('courseTutor:course')
        ->name('courses.grading.complete');

    // Tutor↔student assignment mapping (course staff)
    Route::get('/courses/{course:slug}/assignments', [CourseAssignmentController::class, 'index'])
        ->middleware('courseStaff:course')
        ->name('courses.assignments.index');
    Route::post('/courses/{course:slug}/assignments', [CourseAssignmentController::class, 'store'])
        ->middleware('courseStaff:course')
        ->name('courses.assignments.store');
    Route::delete('/courses/{course:slug}/assignments/{assignment}', [CourseAssignmentController::class, 'destroy'])
        ->middleware('courseStaff:course')
        ->name('courses.assignments.destroy');

    Route::get('/my-learning', [MyLearningController::class, 'index'])->name('learning.index');
    Route::get('/my-tutoring', [MyTutoringController::class, 'index'])->name('tutoring.index');
    Route::get('/my-certificates', [MyCertificatesController::class, 'index'])->name('certificates.mine');
    Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('users.show');
    Route::get('/me', fn () => redirect()->route('users.show', request()->user()))
        ->name('me');
});

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/search', AdminSearchController::class)->name('admin.search');

    Route::get('/user-approvals', [UserApprovalController::class, 'index'])->name('admin.user-approvals.index');
    Route::get('/user-approvals/{user}', [UserApprovalController::class, 'show'])->name('admin.user-approvals.show');
    Route::post('/user-approvals/{user}/approve', [UserApprovalController::class, 'approve'])->name('admin.user-approvals.approve');
    Route::post('/user-approvals/{user}/reject', [UserApprovalController::class, 'reject'])->name('admin.user-approvals.reject');

    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::post('/users/{user}/admin', [UserManagementController::class, 'updateAdmin'])->name('admin.users.admin');

    Route::get('/tutor-applications', [AdminTutorApplicationController::class, 'index'])->name('admin.tutor-applications.index');
    Route::post('/tutor-applications/{tutorApplication}/approve', [AdminTutorApplicationController::class, 'approve'])->name('admin.tutor-applications.approve');
    Route::post('/tutor-applications/{tutorApplication}/reject', [AdminTutorApplicationController::class, 'reject'])->name('admin.tutor-applications.reject');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('admin.settings.edit');
    Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');

    Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('admin.courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('admin.courses.store');
    Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('admin.courses.edit');
    Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');

    Route::get('/courses/{course}/tutors', [CourseTutorController::class, 'index'])->name('admin.courses.tutors');
    Route::post('/courses/{course}/tutors', [CourseTutorController::class, 'store'])->name('admin.courses.tutors.store');
    Route::delete('/courses/{course}/tutors/{courseTutor}', [CourseTutorController::class, 'destroy'])->name('admin.courses.tutors.destroy');

    Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('admin.certificates.index');
    Route::post('/certificates/{certificate}/revoke', [AdminCertificateController::class, 'revoke'])->name('admin.certificates.revoke');
    Route::post('/certificates/{certificate}/reactivate', [AdminCertificateController::class, 'reactivate'])->name('admin.certificates.reactivate');
    Route::post('/certificates/{certificate}/regenerate', [AdminCertificateController::class, 'regenerate'])->name('admin.certificates.regenerate');

    Route::get('/achievements', [AdminAchievementController::class, 'index'])->name('admin.achievements.index');
    Route::get('/achievements/create', [AdminAchievementController::class, 'create'])->name('admin.achievements.create');
    Route::post('/achievements', [AdminAchievementController::class, 'store'])->name('admin.achievements.store');
    Route::get('/achievements/{achievement}/edit', [AdminAchievementController::class, 'edit'])->name('admin.achievements.edit');
    Route::put('/achievements/{achievement}', [AdminAchievementController::class, 'update'])->name('admin.achievements.update');
});

// Public certificate verification (global, unauthenticated)
Route::get('/verify/certificate/{certificate}', [PublicCertificateController::class, 'show'])
    ->name('certificates.verify.show');
Route::get('/verify/certificate/{certificate}/pdf', [PublicCertificateController::class, 'pdf'])
    ->name('certificates.verify.pdf');

require __DIR__.'/auth.php';
