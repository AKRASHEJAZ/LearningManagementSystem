@php
    $brandName = $appSettings['institute.name'] ?? config('app.name', 'Learning Platform');
    $logoPath = $appSettings['brand.logo_path'] ?? null;
    $primaryColor = $appSettings['brand.primary_color'] ?? '#0d6efd';

    $status = $certificate->status ?? 'active';
    $isRevoked = $status === 'revoked';
@endphp

<x-public-layout :title="__('Certificate verification')">
    <div class="container py-4 py-lg-5">
        <div class="mx-auto" style="max-width: 1100px;">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="p-3 p-lg-4">
                    <div class="brand-accent mb-3"></div>

                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            @if($logoPath)
                                <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $brandName }} logo" style="height: 44px; width: auto;">
                            @endif
                            <div>
                                <div class="text-secondary small">Certificate verification</div>
                                <div class="h5 mb-0">{{ $brandName }}</div>
                            </div>
                        </div>

                        <div class="text-start text-lg-end">
                            <div class="text-secondary small">Verification ID</div>
                            <div class="font-monospace small">{{ $certificate->uuid }}</div>
                        </div>
                    </div>
                </div>

                <div class="px-3 px-lg-4 pb-4">
                    <div class="row g-3">
                        <div class="col-12 col-lg-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="fw-semibold">Validity</div>
                                        @if($isRevoked)
                                            <span class="badge text-bg-danger">Revoked</span>
                                        @else
                                            <span class="badge text-bg-success">Valid</span>
                                        @endif
                                    </div>

                                    <hr>

                                    <div class="text-secondary small">Student</div>
                                    <div class="fw-semibold">{{ $certificate->user->name }}</div>

                                    <div class="mt-3 text-secondary small">Course</div>
                                    <div class="fw-semibold">{{ $certificate->course->title }}</div>

                                    <div class="mt-3 text-secondary small">Issued</div>
                                    <div class="fw-semibold">{{ $issuedAt->format('M j, Y') }}</div>

                                    @if($certificate->certificate_number)
                                        <div class="mt-3 text-secondary small">Certificate #</div>
                                        <div class="fw-semibold">{{ $certificate->certificate_number }}</div>
                                    @endif

                                    @if($isRevoked)
                                        <div class="mt-3 text-secondary small">Revocation reason</div>
                                        <div class="text-danger">{{ $certificate->revocation_reason ?: '—' }}</div>
                                    @endif

                                    @if($summary['has_any_score'] || $summary['sum_max'])
                                        <hr>
                                        <div class="fw-semibold">Score summary</div>
                                        <div class="text-secondary small">
                                            @if($summary['has_any_score'])
                                                Total: <span class="fw-semibold">{{ $summary['sum_score'] }}</span>
                                            @else
                                                Total: —
                                            @endif
                                            @if($summary['sum_max'])
                                                <span class="text-secondary">/</span> {{ $summary['sum_max'] }}
                                            @endif
                                        </div>
                                    @endif

                                    <hr>

                                    <div class="text-secondary small">Verify URL</div>
                                    <div class="small text-break">
                                        <a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mt-3">
                                <div class="card-body">
                                    <div class="fw-semibold">QR</div>
                                    <div class="text-secondary small">Scan to open this verification page.</div>

                                    <div class="mt-3 d-flex justify-content-center">
                                        {!! QrCode::size(160)->margin(1)->generate($verifyUrl) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8">
                            <div class="card shadow-sm">
                                <div class="card-body border-bottom">
                                    <div class="h6 mb-0">Assessment performance</div>
                                    <div class="text-secondary small">Per-evaluation status, score, and feedback.</div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Evaluation</th>
                                                <th style="width: 140px;">Status</th>
                                                <th style="width: 110px;">Score</th>
                                                <th>Feedback</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($evaluations as $evaluation)
                                                @php
                                                    $grade = $grades->get($evaluation->id);
                                                    $gStatus = $grade->status ?? 'learning';
                                                    $badge = match ($gStatus) {
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
                                                    <td><span class="badge {{ $badge }}">{{ ucfirst($gStatus) }}</span></td>
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
                                                <tr>
                                                    <td colspan="4" class="text-center text-secondary py-4">No evaluations found for this course.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-secondary small mt-3">
                                This page is public. It intentionally does not show student email.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
