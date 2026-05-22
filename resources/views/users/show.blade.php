<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">{{ $profileUser->name }}</div>
                <div class="text-secondary small">Profile · Progress · Achievements</div>
            </div>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold">Progress</div>
                    <div class="text-secondary small">Course participation overview.</div>
                    <div class="row g-2 mt-2">
                        <div class="col-4">
                            <div class="border rounded-3 p-2 text-center">
                                <div class="fw-semibold">{{ $progress['applied'] }}</div>
                                <div class="text-secondary small">Applied</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-2 text-center">
                                <div class="fw-semibold">{{ $progress['accepted'] }}</div>
                                <div class="text-secondary small">Accepted</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-2 text-center">
                                <div class="fw-semibold">{{ $progress['completed'] }}</div>
                                <div class="text-secondary small">Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="fw-semibold">Certificates</div>
                    <div class="text-secondary small">Recent course completions.</div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($certificates as $certificate)
                        <div class="list-group-item">
                            <div class="fw-semibold">{{ $certificate->course->title }}</div>
                            <div class="text-secondary small">{{ ($certificate->issued_at ?? $certificate->created_at)?->format('M j, Y') }}</div>
                            <div class="d-flex gap-2 flex-wrap mt-2">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('certificates.verify.show', $certificate) }}" target="_blank" rel="noopener">Verify</a>
                                <a class="btn btn-sm btn-primary" href="{{ route('certificates.verify.pdf', $certificate) }}">PDF</a>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-secondary">No certificates yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <div>
                    <div class="fw-semibold">Achievements</div>
                    <div class="text-secondary small">Badges unlocked across your learning journey.</div>
                </div>
                <div class="text-secondary small">
                    Unlocked: {{ $earned->count() }} / {{ $allAchievements->count() }}
                </div>
            </div>

            <div class="row g-3">
                @forelse($allAchievements as $achievement)
                    @php
                        $ua = $earned->get($achievement->id);
                        $earnedAt = $ua?->earned_at?->format('M j, Y');
                        $rule = $achievement->rule;
                    @endphp
                    <div class="col-12 col-md-6">
                        <button type="button"
                            class="btn p-0 text-start w-100 border-0 bg-transparent"
                            data-bs-toggle="modal"
                            data-bs-target="#achievementModal{{ $achievement->id }}"
                            aria-label="View achievement details">
                            <x-achievement-badge
                                :name="$achievement->name"
                                :description="$achievement->description"
                                :tier="$achievement->tier"
                                :icon="$achievement->icon"
                                :points="$achievement->points"
                                :earnedAt="$earnedAt"
                                :locked="! (bool) $ua"
                            />
                        </button>

                        <div class="modal fade" id="achievementModal{{ $achievement->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div>
                                            <div class="h6 mb-0">{{ $achievement->name }}</div>
                                            <div class="text-secondary small">{{ ucfirst($achievement->tier) }} · {{ $achievement->points }} XP</div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if($achievement->description)
                                            <div class="mb-3">{{ $achievement->description }}</div>
                                        @endif

                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                            <div class="fw-semibold">How to unlock</div>
                                            @if($ua)
                                                <span class="badge text-bg-success">Unlocked {{ $earnedAt }}</span>
                                            @else
                                                <span class="badge text-bg-light border">Locked</span>
                                            @endif
                                        </div>

                                        @if(! $rule || ! $rule->is_active)
                                            <div class="text-secondary small">No unlock rule configured. Ask an admin if you think this is a mistake.</div>
                                        @elseif($rule->type === \App\Services\AchievementService::RULE_COURSE_COUNT)
                                            <div class="text-secondary small">
                                                Complete at least <span class="fw-semibold">{{ $rule->min_course_completions }}</span> courses.
                                            </div>
                                        @elseif($rule->type === \App\Services\AchievementService::RULE_SPECIFIC_COURSES)
                                            <div class="text-secondary small mb-2">Complete all of these courses:</div>
                                            <ul class="small mb-0">
                                                @foreach($rule->courses as $rc)
                                                    <li>{{ $rc->course?->title ?? 'Course #'.$rc->course_id }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-secondary small">Unknown rule type.</div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-secondary">No achievements configured yet.</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
