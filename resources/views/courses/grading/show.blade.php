<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Grading</div>
                <div class="text-secondary small">{{ $course->title }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.evaluations.index', $course->slug) }}">Evaluations</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.manage', $course->slug) }}">Participants</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($evaluations->count() === 0)
        <div class="alert alert-warning">No evaluations yet. Create evaluations first.</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th style="width: 190px;">Overall status</th>
                        @foreach($evaluations as $evaluation)
                            <th style="min-width: 260px;">{{ $evaluation->title }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $enrollment)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $enrollment->user->name }}</div>
                                <div class="text-secondary small">{{ $enrollment->user->email }}</div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('courses.grading.result', [$course->slug, $enrollment]) }}">
                                        @csrf
                                        <select class="form-select form-select-sm" name="result_status" onchange="this.form.submit()">
                                            <option value="learning" @selected(($enrollment->result_status ?? 'learning') === 'learning')>Learning</option>
                                            <option value="passed" @selected($enrollment->result_status === 'passed')>Passed</option>
                                            <option value="failed" @selected($enrollment->result_status === 'failed')>Failed</option>
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('courses.grading.complete', [$course->slug, $enrollment]) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" type="submit" @disabled(($enrollment->result_status ?? 'learning') !== 'passed')>
                                            Complete
                                        </button>
                                    </form>

                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.results.student', [$course->slug, $enrollment]) }}">
                                        Results
                                    </a>
                                </div>
                            </td>
                            @foreach($evaluations as $evaluation)
                                @php
                                    $key = $evaluation->id.':'.$enrollment->user_id;
                                    $grade = $grades->get($key)?->first();
                                @endphp
                                <td>
                                    <form method="POST" action="{{ route('courses.grading.upsert', [$course->slug, $evaluation, $enrollment]) }}">
                                        @csrf
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <select class="form-select form-select-sm" name="status">
                                                    <option value="learning" @selected(($grade->status ?? 'learning') === 'learning')>Learning</option>
                                                    <option value="passed" @selected(($grade->status ?? null) === 'passed')>Passed</option>
                                                    <option value="failed" @selected(($grade->status ?? null) === 'failed')>Failed</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <input class="form-control form-control-sm" type="number" name="score" min="0" max="{{ $evaluation->max_score ?? 1000 }}" value="{{ $grade->score ?? '' }}" placeholder="Score">
                                            </div>
                                            <div class="col-12">
                                                <textarea class="form-control form-control-sm" name="feedback" rows="2" placeholder="Feedback (optional)">{{ $grade->feedback ?? '' }}</textarea>
                                            </div>
                                            <div class="col-12 d-flex justify-content-end">
                                                <button class="btn btn-sm btn-outline-primary" type="submit">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ 2 + $evaluations->count() }}" class="text-center text-secondary py-4">No accepted students yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
