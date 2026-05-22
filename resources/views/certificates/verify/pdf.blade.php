@php
    $status = $certificate->status ?? 'active';
    $isRevoked = $status === 'revoked';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Certificate verification</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { margin: 0; font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111827; }
        .sheet { border: 1px solid #e5e7eb; padding: 8mm; }
        .accent { height: 6px; background: {{ $primaryColor }}; border-radius: 999px; margin-bottom: 8px; }
        .muted { color: #6b7280; }
        .mono { font-family: Courier, monospace; font-size: 10px; }
        .h1 { font-size: 15px; font-weight: 700; margin: 0; }
        .small { font-size: 11px; }
        .card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 9px; }
        .hr { height: 1px; background: #e5e7eb; margin: 8px 0; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .badge-bad { background: #fee2e2; color: #991b1b; }
        .badge-warn { background: #fef9c3; color: #854d0e; }
        .layout { width: 100%; border-collapse: collapse; }
        .layout td { vertical-align: top; }
        .leftcol { width: 38%; padding-right: 10px; }
        .rightcol { width: 62%; padding-left: 10px; }
        table.table { width: 100%; border-collapse: collapse; }
        table.table th, table.table td { border-top: 1px solid #e5e7eb; padding: 8px 6px; text-align: left; vertical-align: top; }
        table.table th { font-size: 11px; color: #374151; }
        .qr { width: 135px; height: 135px; border: 1px solid #e5e7eb; padding: 6px; }
        .watermark { position: fixed; top: 45%; left: 28%; transform: rotate(-20deg); font-size: 64px; font-weight: 800; color: rgba(239, 68, 68, 0.12); letter-spacing: 3px; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="accent"></div>

        @if($isRevoked)
            <div class="watermark">REVOKED</div>
        @endif

        <table style="width:100%; border-collapse: collapse; margin-bottom: 10px;">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="h1">Certificate verification</div>
                    <div class="muted small">{{ $brandName }}</div>
                </td>
                <td style="vertical-align: middle; text-align: right;">
                    <div class="muted small">Verification ID</div>
                    <div class="mono">{{ $certificate->uuid }}</div>
                </td>
                <td style="vertical-align: middle; text-align: right; width: 60px;">
                    @if($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="Logo" style="height: 34px; width: auto;">
                    @endif
                </td>
            </tr>
        </table>

        <table class="layout">
            <tr>
                <td class="leftcol">
                    <div class="card">
                        <div style="display: table; width: 100%;">
                            <div style="display: table-cell; vertical-align: middle;">
                                <strong>Validity</strong>
                            </div>
                            <div style="display: table-cell; text-align: right; vertical-align: middle;">
                                @if($isRevoked)
                                    <span class="badge badge-bad">Revoked</span>
                                @else
                                    <span class="badge badge-ok">Valid</span>
                                @endif
                            </div>
                        </div>

                        <div class="hr"></div>

                        <div class="muted small">Student</div>
                        <div><strong>{{ $certificate->user->name }}</strong></div>

                        <div style="height: 6px;"></div>
                        <div class="muted small">Course</div>
                        <div><strong>{{ $certificate->course->title }}</strong></div>

                        <div style="height: 6px;"></div>
                        <div class="muted small">Issued</div>
                        <div><strong>{{ $issuedAt->format('M j, Y') }}</strong></div>

                        @if($certificate->certificate_number)
                            <div style="height: 6px;"></div>
                            <div class="muted small">Certificate #</div>
                            <div><strong>{{ $certificate->certificate_number }}</strong></div>
                        @endif

                        @if($isRevoked)
                            <div style="height: 6px;"></div>
                            <div class="muted small">Revocation reason</div>
                            <div style="color:#991b1b;">{{ $certificate->revocation_reason ?: '—' }}</div>
                        @endif

                        @if($summary['has_any_score'] || $summary['sum_max'])
                            <div class="hr"></div>
                            <div><strong>Score summary</strong></div>
                            <div class="muted small">
                                @if($summary['has_any_score'])
                                    Total: <strong>{{ $summary['sum_score'] }}</strong>
                                @else
                                    Total: —
                                @endif
                                @if($summary['sum_max'])
                                    / {{ $summary['sum_max'] }}
                                @endif
                            </div>
                        @endif

                        <div class="hr"></div>
                        <div class="muted small">Verify URL</div>
                        <div class="mono">{{ $verifyUrl }}</div>
                    </div>

                    <div style="height: 10px;"></div>

                    <div class="card">
                        <div><strong>QR</strong></div>
                        <div class="muted small">Scan to open this verification page.</div>
                        <div style="text-align:center; margin-top: 8px;">
                            <img class="qr" src="{{ $qrDataUri }}" alt="QR code">
                        </div>
                    </div>
                </td>

                <td class="rightcol">
                    <div class="card">
                        <div style="margin-bottom: 6px;">
                            <strong>Assessment performance</strong>
                            <div class="muted small">Per-evaluation status, score, and feedback.</div>
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Evaluation</th>
                                    <th style="width: 95px;">Status</th>
                                    <th style="width: 70px;">Score</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evaluations as $evaluation)
                                    @php
                                        $grade = $grades->get($evaluation->id);
                                        $gStatus = $grade->status ?? 'learning';
                                        $badge = match ($gStatus) {
                                            'passed' => 'badge badge-ok',
                                            'failed' => 'badge badge-bad',
                                            default => 'badge badge-warn',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $evaluation->title }}</strong>
                                            @if($evaluation->description)
                                                <div class="muted small">{{ $evaluation->description }}</div>
                                            @endif
                                        </td>
                                        <td><span class="{{ $badge }}">{{ ucfirst($gStatus) }}</span></td>
                                        <td class="muted small">
                                            @if(!is_null($grade?->score))
                                                {{ $grade->score }}@if($evaluation->max_score)/{{ $evaluation->max_score }}@endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="muted small">{{ $grade?->feedback ?: '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="muted" style="padding: 12px;">No evaluations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="muted small" style="margin-top: 10px;">
                            This PDF intentionally does not show student email.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
