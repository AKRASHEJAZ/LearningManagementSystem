<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\AchievementRule;
use App\Models\AchievementRuleCourse;
use App\Models\Course;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    public const RULE_COURSE_COUNT = 'course_count';
    public const RULE_SPECIFIC_COURSES = 'specific_courses';

    public function award(User $user, string $key, array $meta = []): ?UserAchievement
    {
        $achievement = Achievement::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        if (! $achievement) {
            return null;
        }

        return DB::transaction(function () use ($user, $achievement, $meta) {
            /** @var UserAchievement $row */
            $row = UserAchievement::query()->firstOrCreate(
                ['user_id' => $user->id, 'achievement_id' => $achievement->id],
                ['earned_at' => now(), 'meta' => $meta ?: null]
            );

            if ($meta && ($row->meta ?? []) !== $meta) {
                $row->forceFill(['meta' => $meta])->save();
            }

            return $row;
        });
    }

    public function awardById(User $user, int $achievementId, array $meta = []): ?UserAchievement
    {
        $achievement = Achievement::query()
            ->where('id', $achievementId)
            ->where('is_active', true)
            ->first();

        if (! $achievement) {
            return null;
        }

        return DB::transaction(function () use ($user, $achievement, $meta) {
            /** @var UserAchievement $row */
            $row = UserAchievement::query()->firstOrCreate(
                ['user_id' => $user->id, 'achievement_id' => $achievement->id],
                ['earned_at' => now(), 'meta' => $meta ?: null]
            );

            if ($meta && ($row->meta ?? []) !== $meta) {
                $row->forceFill(['meta' => $meta])->save();
            }

            return $row;
        });
    }

    public function onUserApproved(User $user): void
    {
        $this->award($user, 'account.approved');
    }

    public function onCourseCompleted(User $student, Course $course): void
    {
        $this->evaluateRulesFor($student);
    }

    public function evaluateRulesFor(User $user): void
    {
        $userCourseIds = $user->certificates()
            ->where('status', 'active')
            ->pluck('course_id')
            ->unique()
            ->values()
            ->all();

        $completedCount = count($userCourseIds);

        $rules = AchievementRule::query()
            ->with(['achievement:id,is_active', 'courses:achievement_rule_id,course_id'])
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            if (! $rule->achievement || ! $rule->achievement->is_active) {
                continue;
            }

            $met = false;
            $meta = [];

            if ($rule->type === self::RULE_COURSE_COUNT) {
                $min = (int) ($rule->min_course_completions ?? 0);
                if ($min > 0 && $completedCount >= $min) {
                    $met = true;
                    $meta = ['count' => $completedCount, 'min' => $min];
                }
            } elseif ($rule->type === self::RULE_SPECIFIC_COURSES) {
                $courseIds = $rule->courses->pluck('course_id')->unique()->values()->all();
                if (count($courseIds) > 0) {
                    $missing = array_values(array_diff($courseIds, $userCourseIds));
                    if (count($missing) === 0) {
                        $met = true;
                        $meta = ['course_ids' => $courseIds];
                    }
                }
            }

            if ($met) {
                $this->awardById($user, $rule->achievement_id, $meta);
            }
        }
    }
}
