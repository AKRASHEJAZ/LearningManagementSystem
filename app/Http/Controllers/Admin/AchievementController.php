<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AchievementStoreRequest;
use App\Http\Requests\Admin\AchievementUpdateRequest;
use App\Models\Achievement;
use App\Models\AchievementRule;
use App\Models\AchievementRuleCourse;
use App\Models\Course;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->string('q')->toString());

        $query = Achievement::query()->orderByDesc('is_active')->orderBy('tier')->orderBy('points')->orderBy('name');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('key', 'like', "%{$q}%");
            });
        }

        $achievements = $query->paginate(30)->withQueryString();

        return view('admin.achievements.index', [
            'achievements' => $achievements,
            'q' => $q,
        ]);
    }

    public function create()
    {
        $courses = Course::query()->orderBy('title')->get(['id', 'title']);

        return view('admin.achievements.create', [
            'courses' => $courses,
        ]);
    }

    public function store(AchievementStoreRequest $request)
    {
        $validated = $request->validated();
        $rulePayload = $this->extractRulePayload($validated);

        $achievement = Achievement::query()->create($validated);
        $this->upsertRule($achievement, $rulePayload, $request->user()?->id);

        return redirect()
            ->route('admin.achievements.edit', $achievement)
            ->with('status', 'Achievement created.');
    }

    public function edit(Achievement $achievement)
    {
        $achievement->load('rule');
        $courseIds = [];
        if ($achievement->rule) {
            $courseIds = AchievementRuleCourse::query()
                ->where('achievement_rule_id', $achievement->rule->id)
                ->pluck('course_id')
                ->all();
        }

        $courses = Course::query()->orderBy('title')->get(['id', 'title']);

        return view('admin.achievements.edit', [
            'achievement' => $achievement,
            'courses' => $courses,
            'ruleCourseIds' => $courseIds,
        ]);
    }

    public function update(AchievementUpdateRequest $request, Achievement $achievement)
    {
        $validated = $request->validated();
        $rulePayload = $this->extractRulePayload($validated);

        $achievement->update($validated);
        $this->upsertRule($achievement, $rulePayload, $request->user()?->id);

        return redirect()
            ->route('admin.achievements.edit', $achievement)
            ->with('status', 'Achievement updated.');
    }

    public function toggle(Request $request, Achievement $achievement)
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $achievement->update(['is_active' => (bool) $request->boolean('is_active')]);

        return redirect()->back()->with('status', 'Achievement updated.');
    }

    private function extractRulePayload(array &$validated): array
    {
        $ruleKeys = ['rule_type', 'rule_is_active', 'min_course_completions', 'course_ids'];
        $payload = [];
        foreach ($ruleKeys as $k) {
            if (array_key_exists($k, $validated)) {
                $payload[$k] = $validated[$k];
                unset($validated[$k]);
            }
        }
        return $payload;
    }

    private function upsertRule(Achievement $achievement, array $payload, ?int $createdByUserId): void
    {
        $type = $payload['rule_type'] ?? null;
        if (! $type) {
            AchievementRule::query()->where('achievement_id', $achievement->id)->delete();
            return;
        }

        /** @var AchievementRule $rule */
        $rule = AchievementRule::query()->updateOrCreate(
            ['achievement_id' => $achievement->id],
            [
                'type' => $type,
                'min_course_completions' => $type === 'course_count' ? ($payload['min_course_completions'] ?? null) : null,
                'is_active' => (bool) ($payload['rule_is_active'] ?? true),
                'created_by' => $createdByUserId,
            ]
        );

        if ($type !== 'specific_courses') {
            AchievementRuleCourse::query()->where('achievement_rule_id', $rule->id)->delete();
            return;
        }

        $courseIds = array_values(array_unique(array_map('intval', $payload['course_ids'] ?? [])));
        AchievementRuleCourse::query()->where('achievement_rule_id', $rule->id)->whereNotIn('course_id', $courseIds)->delete();
        foreach ($courseIds as $courseId) {
            AchievementRuleCourse::query()->firstOrCreate([
                'achievement_rule_id' => $rule->id,
                'course_id' => $courseId,
            ]);
        }
    }
}
