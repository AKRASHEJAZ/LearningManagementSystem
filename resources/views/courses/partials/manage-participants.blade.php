@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="row g-3">
    <div class="col-12 col-xl-7">
        <div class="card shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <div class="fw-semibold">Applications</div>
                    <div class="text-secondary small">
                        @if($course->isStarted())
                            Applications closed (course started).
                        @else
                            Accept up to the max participant limit, then start the course.
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if($course->max_participants)
                        <span class="badge text-bg-light border">Capacity: {{ $accepted->count() }} / {{ $course->max_participants }}</span>
                    @endif

                    @if(!$course->isStarted())
                        <form method="POST" action="{{ route('courses.manage.start', $course->slug) }}">
                            @csrf
                            <button class="btn btn-sm btn-primary" type="submit">Mark started</button>
                        </form>
                    @elseif(!$course->isEnded())
                        <form method="POST" action="{{ route('courses.manage.end', $course->slug) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger" type="submit">End course</button>
                        </form>
                    @else
                        <span class="badge text-bg-secondary">Ended</span>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="width: 160px;">Applied</th>
                            <th style="width: 260px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applied as $enrollment)
                            <tr>
                                <td class="fw-semibold">{{ $enrollment->user->name }}</td>
                                <td class="text-secondary small">{{ $enrollment->user->email }}</td>
                                <td class="text-secondary small">{{ $enrollment->applied_at }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form method="POST" action="{{ route('courses.manage.accept', [$course->slug, $enrollment->id]) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit" @disabled($course->isStarted() || $isFull)>Accept</button>
                                        </form>
                                        <form method="POST" action="{{ route('courses.manage.reject', [$course->slug, $enrollment->id]) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit" @disabled($course->isStarted())>Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">No applications.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <div class="fw-semibold">Evaluations</div>
                    <div class="text-secondary small">Manage evaluation items and grading.</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.assignments.index', $course->slug) }}">Assignments</a>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.evaluations.index', $course->slug) }}">Evaluations</a>
                    @php
                        $isTutorForCourse = \App\Models\CourseTutor::where('course_id', $course->id)
                            ->where('user_id', auth()->id())
                            ->where('status', 'active')
                            ->exists();
                    @endphp
                    @if ($isTutorForCourse)
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('courses.grading.show', $course->slug) }}">Grading</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="fw-semibold">Accepted</div>
                <div class="text-secondary small">These students are in the course.</div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 36px;"></th>
                            <th>Name</th>
                            <th style="width: 140px;">Accepted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form method="POST" action="{{ route('courses.manage.bulk-complete', $course->slug) }}">
                            @csrf
                            @forelse($accepted as $enrollment)
                                <tr>
                                    <td>
                                        <input class="form-check-input" type="checkbox" name="enrollment_ids[]" value="{{ $enrollment->id }}" @disabled(! $course->isStarted())>
                                    </td>
                                    <td class="fw-semibold">{{ $enrollment->user->name }}</td>
                                    <td class="text-secondary small">{{ $enrollment->accepted_at }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-secondary py-4">No accepted students yet.</td></tr>
                            @endforelse
                            @if($accepted->count() > 0)
                                <tr>
                                    <td colspan="3" class="p-3 border-top">
                                        <button class="btn btn-sm btn-primary" type="submit" @disabled(! $course->isStarted())>Mark selected completed</button>
                                        @if(! $course->isStarted())
                                            <div class="text-secondary small mt-1">Start the course before marking completion.</div>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </form>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="fw-semibold">Completed</div>
                <div class="text-secondary small">Students marked completed.</div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th style="width: 140px;">Score</th>
                            <th style="width: 160px;">Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($completed as $enrollment)
                            <tr>
                                <td class="fw-semibold">{{ $enrollment->user->name }}</td>
                                <td class="text-secondary small">
                                    @php
                                        $s = $completedScores[$enrollment->user_id]['sum'] ?? null;
                                        $m = $completedScores[$enrollment->user_id]['max'] ?? null;
                                    @endphp
                                    @if(!is_null($s))
                                        {{ $s }}@if(!is_null($m))/{{ $m }}@endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-secondary small">{{ $enrollment->completed_at }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-secondary py-4">No completions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
