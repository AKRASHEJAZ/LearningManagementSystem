<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AchievementsAwardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_approval_awards_verified_member_achievement(): void
    {
        Achievement::query()->create([
            'key' => 'account.approved',
            'name' => 'Verified Member',
            'description' => null,
            'icon' => 'shield',
            'points' => 10,
            'tier' => 'bronze',
            'is_active' => true,
        ]);

        $admin = User::factory()->admin()->create();
        $user = User::factory()->pendingApproval()->create();

        $this
            ->actingAs($admin)
            ->post(route('admin.user-approvals.approve', $user, absolute: false))
            ->assertRedirect();

        $this->assertSame('approved', $user->refresh()->approval_status);
        $this->assertTrue(UserAchievement::query()
            ->where('user_id', $user->id)
            ->whereHas('achievement', fn ($q) => $q->where('key', 'account.approved'))
            ->exists());
    }

    public function test_course_completion_issues_certificate_and_awards_completion_achievement(): void
    {
        $achievement = Achievement::query()->create([
            'key' => 'course.completed.first',
            'name' => 'First Completion',
            'description' => null,
            'icon' => 'trophy',
            'points' => 25,
            'tier' => 'bronze',
            'is_active' => true,
        ]);

        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        \App\Models\AchievementRule::query()->create([
            'achievement_id' => $achievement->id,
            'type' => \App\Services\AchievementService::RULE_COURSE_COUNT,
            'min_course_completions' => 1,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        /** @var CertificateService $service */
        $service = app(CertificateService::class);
        $service->issueFor($student, $course, $admin);

        $this->assertTrue(Certificate::query()->where('user_id', $student->id)->where('course_id', $course->id)->exists());
        $this->assertTrue(UserAchievement::query()
            ->where('user_id', $student->id)
            ->where('achievement_id', $achievement->id)
            ->exists());
    }
}
