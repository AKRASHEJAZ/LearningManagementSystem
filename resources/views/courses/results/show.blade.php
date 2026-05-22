<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Results</div>
                <div class="text-secondary small">{{ $course->title }}</div>
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.show', $course->slug) }}">Back to course</a>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold">Overall status</div>
                    <div class="text-secondary small">Set by tutor during grading.</div>
                    <div class="mt-2">
                        @php
                            $overall = $enrollment->result_status ?? 'learning';
                            $badge = match ($overall) {
                                'passed' => 'text-bg-success',
                                'failed' => 'text-bg-danger',
                                default => 'text-bg-warning',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($overall) }}</span>
                    </div>
                </div>
            </div>

            @if($enrollment->status !== 'completed')
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <div class="fw-semibold">Certificate</div>
                        <div class="text-secondary small">Available after you are marked completed in the course.</div>
                    </div>
                </div>
            @elseif($certificate)
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <div class="fw-semibold">Certificate</div>
                        <div class="text-secondary small">Publicly verifiable certificate with QR.</div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a class="btn btn-sm btn-primary" href="{{ route('certificates.verify.show', $certificate) }}" target="_blank" rel="noopener">
                                View verification
                            </a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('certificates.verify.pdf', $certificate) }}">
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Evaluation</th>
                                <th style="width: 140px;">Status</th>
                                <th style="width: 100px;">Score</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evaluations as $evaluation)
                                @php
                                    $grade = $grades->get($evaluation->id);
                                    $status = $grade->status ?? 'learning';
                                    $badge = match ($status) {
                                        'passed' => 'text-bg-success',
                                        'failed' => 'text-bg-danger',
                                        default => 'text-bg-warning',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $evaluation->title }}</div>
                                        @if($evaluation->description)
                                            <div class="text-secondary small">{{ $evaluation->description }}</div>
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $badge }}">{{ ucfirst($status) }}</span></td>
                                    <td class="text-secondary small">
                                        @if(!is_null($grade?->score))
                                            {{ $grade->score }}@if($evaluation->max_score)/{{ $evaluation->max_score }}@endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-secondary small">
                                        {{ $grade?->feedback ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-4">No evaluations yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
