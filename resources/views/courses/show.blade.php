<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="h5 mb-0">{{ $course->title }}</div>
            @if ($course->summary)
                <div class="text-secondary small">{{ $course->summary }}</div>
            @endif
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    @if ($course->duration)
                        <div class="mb-3">
                            <span class="badge text-bg-light border">Duration: {{ $course->duration }}</span>
                        </div>
                    @endif

                    @if ($course->description)
                        <div style="white-space: pre-wrap;">{{ $course->description }}</div>
                    @else
                        <div class="text-secondary">No description yet.</div>
                    @endif
                </div>
            </div>

            @if ($course->completion_criteria)
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <div class="fw-semibold mb-1">Completion criteria</div>
                        <div class="text-secondary small mb-2">What you need to do to complete this course.</div>
                        <div style="white-space: pre-wrap;">{{ $course->completion_criteria }}</div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold">Next</div>
                    @php
                        $capacity = $course->max_participants;
                        $isFull = is_int($capacity) && $capacity > 0 && $acceptedCount >= $capacity;
                    @endphp

                    @if ($course->isEnded())
                        <div class="text-secondary small mt-1">This course has ended.</div>
                    @elseif ($course->isStarted())
                        <div class="text-secondary small mt-1">This course has started. New applications are closed.</div>
                    @else
                        <div class="text-secondary small mt-1">Apply to join. Admin/tutor will select participants.</div>
                    @endif

                    @if ($capacity)
                        <div class="text-secondary small mt-2">Capacity: {{ $acceptedCount }} / {{ $capacity }}</div>
                    @endif

                    <div class="mt-3">
                        @if ($isStaff)
                            <a class="btn btn-outline-secondary btn-sm mb-2" href="{{ route('courses.manage', $course->slug) }}">Manage participants</a>
                            @if (\App\Models\CourseTutor::where('course_id', $course->id)->where('user_id', auth()->id())->where('status', 'active')->exists())
                                <a class="btn btn-outline-primary btn-sm mb-2" href="{{ route('courses.grading.show', $course->slug) }}">Grading</a>
                            @endif
                            <a class="btn btn-outline-secondary btn-sm mb-2" href="{{ route('courses.evaluations.index', $course->slug) }}">Evaluations</a>
                        @elseif (in_array($enrollment?->status, ['accepted', 'completed'], true))
                            <a class="btn btn-outline-primary btn-sm mb-2" href="{{ route('courses.results.show', $course->slug) }}">View results</a>
                        @endif

                        @if ($enrollment?->status === 'accepted')
                            <span class="badge text-bg-success">Accepted</span>
                        @elseif ($enrollment?->status === 'applied')
                            <span class="badge text-bg-warning">Applied</span>
                            <form class="mt-2" method="POST" action="{{ route('courses.withdraw', $course->slug) }}">
                                @csrf
                                <button class="btn btn-outline-secondary btn-sm" type="submit" @disabled($course->isStarted())>Withdraw</button>
                            </form>
                        @elseif ($enrollment?->status === 'rejected')
                            <span class="badge text-bg-danger">Rejected</span>
                        @elseif ($enrollment?->status === 'completed')
                            <span class="badge text-bg-primary">Completed</span>
                        @elseif ($isStaff)
                            <div class="text-secondary small">You are a tutor for this course and cannot apply as a student.</div>
                        @else
                            <form method="POST" action="{{ route('courses.apply', $course->slug) }}">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small" for="note">Application note (optional)</label>
                                    <textarea class="form-control form-control-sm" id="note" name="note" rows="3" placeholder="Why do you want to join?">{{ old('note') }}</textarea>
                                    <x-input-error :messages="$errors->get('note')" />
                                </div>
                                <button class="btn btn-primary btn-sm" type="submit" @disabled(!$course->applicationsOpen() || $isFull)>
                                    Apply
                                </button>
                                @if ($isFull)
                                    <div class="text-danger small mt-2">This course is full.</div>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @if (! $isStaff && ! in_array($enrollment?->status, ['applied', 'accepted', 'completed'], true))
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <div class="fw-semibold">Tutor</div>
                        <div class="text-secondary small">Want to help teach this course?</div>

                        @if ($tutorApplication)
                            <div class="mt-2">
                                <span class="badge text-bg-light border">Status: {{ $tutorApplication->status }}</span>
                            </div>
                            @if ($tutorApplication->status === 'rejected' && $tutorApplication->review_notes)
                                <div class="text-secondary small mt-2">{{ $tutorApplication->review_notes }}</div>
                            @endif
                        @else
                            <form class="mt-2" method="POST" action="{{ route('courses.tutor-apply', $course->slug) }}">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small" for="motivation">Motivation (optional)</label>
                                    <textarea class="form-control form-control-sm" id="motivation" name="motivation" rows="3" placeholder="Why do you want to tutor this course?">{{ old('motivation') }}</textarea>
                                    <x-input-error :messages="$errors->get('motivation')" />
                                </div>
                                <button class="btn btn-outline-primary btn-sm" type="submit">Apply to be tutor</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
