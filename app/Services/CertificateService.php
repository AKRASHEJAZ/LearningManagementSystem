<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Support\Str;

class CertificateService
{
    public function __construct(private readonly AchievementService $achievementService)
    {
    }

    public function issueFor(User $student, Course $course, ?User $issuedBy = null): Certificate
    {
        /** @var Certificate $certificate */
        $certificate = Certificate::query()->firstOrCreate(
            ['user_id' => $student->id, 'course_id' => $course->id],
            [
                'uuid' => (string) Str::uuid(),
                'certificate_number' => $this->generateCertificateNumber($course->id, $student->id),
                'issued_by' => $issuedBy?->id,
                'issued_at' => now(),
                'status' => 'active',
            ]
        );

        if (! $certificate->issued_at) {
            $certificate->forceFill([
                'issued_by' => $issuedBy?->id,
                'issued_at' => now(),
                'status' => 'active',
            ])->save();
        }

        $this->achievementService->onCourseCompleted($student, $course);

        return $certificate;
    }

    private function generateCertificateNumber(int $courseId, int $userId): string
    {
        // Human-friendly but not guessable enough to be used as the only verifier (we use uuid in URLs).
        return 'C-'.$courseId.'-'.$userId.'-'.strtoupper(Str::random(6));
    }
}
