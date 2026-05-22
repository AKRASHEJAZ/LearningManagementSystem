@php
    $tierOptions = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
    $iconOptions = [
        'badge' => 'Badge',
        'shield' => 'Shield',
        'trophy' => 'Trophy',
        'medal' => 'Medal',
        'crown' => 'Crown',
    ];

    $ruleType = old('rule_type', $achievement->rule->type ?? '');
    $ruleActive = (bool) old('rule_is_active', $achievement->rule->is_active ?? true);
    $minCount = old('min_course_completions', $achievement->rule->min_course_completions ?? 1);
    $selectedCourses = old('course_ids', $ruleCourseIds ?? []);
@endphp

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <label class="form-label">Key</label>
        <input class="form-control" name="key" value="{{ old('key', $achievement->key) }}" placeholder="e.g. git.guru" required>
        <div class="form-text">Use dot-separated keys (letters/numbers). Used internally.</div>
    </div>
    <div class="col-12 col-lg-6">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" value="{{ old('name', $achievement->name) }}" required>
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <input class="form-control" name="description" value="{{ old('description', $achievement->description) }}" placeholder="Short description (optional)">
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Tier</label>
        <select class="form-select" name="tier" required>
            @foreach($tierOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('tier', $achievement->tier) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-4">
        <label class="form-label">Icon</label>
        <select class="form-select" name="icon" required>
            @foreach($iconOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('icon', $achievement->icon) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-2">
        <label class="form-label">Points</label>
        <input class="form-control" type="number" name="points" min="0" value="{{ old('points', $achievement->points ?? 0) }}" required>
    </div>
    <div class="col-12 col-md-2">
        <label class="form-label">Active</label>
        <select class="form-select" name="is_active" required>
            <option value="1" @selected((int) old('is_active', (int) ($achievement->is_active ?? 1)) === 1)>Yes</option>
            <option value="0" @selected((int) old('is_active', (int) ($achievement->is_active ?? 1)) === 0)>No</option>
        </select>
    </div>
</div>

<hr class="my-4">

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <div class="fw-semibold">Awarding rule</div>
        <div class="text-secondary small">Admin-controlled rules based on course completion.</div>
    </div>
    <div class="form-check">
        <input type="hidden" name="rule_is_active" value="0">
        <input class="form-check-input" type="checkbox" value="1" id="ruleActive" name="rule_is_active" @checked($ruleActive)>
        <label class="form-check-label" for="ruleActive">Rule active</label>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-lg-4">
        <label class="form-label">Rule type</label>
        <select class="form-select" name="rule_type">
            <option value="">No rule</option>
            <option value="course_count" @selected($ruleType === 'course_count')>Completed course count</option>
            <option value="specific_courses" @selected($ruleType === 'specific_courses')>Specific courses (all required)</option>
        </select>
    </div>

    <div class="col-12 col-lg-4">
        <label class="form-label">Min completions</label>
        <input class="form-control" type="number" min="1" name="min_course_completions" value="{{ $minCount }}">
        <div class="form-text">Used only for “Completed course count”.</div>
    </div>

    <div class="col-12 col-lg-4">
        <label class="form-label">Required courses</label>
        <div class="border rounded-3 p-2" style="max-height: 220px; overflow:auto;">
            @forelse($courses as $course)
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="course_ids[]"
                        value="{{ $course->id }}"
                        id="course{{ $course->id }}"
                        @checked(in_array($course->id, $selectedCourses))
                    >
                    <label class="form-check-label" for="course{{ $course->id }}">
                        {{ $course->title }}
                    </label>
                </div>
            @empty
                <div class="text-secondary small">No courses found.</div>
            @endforelse
        </div>
        <div class="form-text">Used only for “Specific courses”. Tick all required courses (e.g., Git Basics + Git Advanced → Git Guru).</div>
    </div>
</div>
