@php
    $issuedAt = $certificate->issued_at ?? $certificate->created_at;
    $status = $certificate->status ?? 'active';
    $isRevoked = $status === 'revoked';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Certificate</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { margin: 0; font-family: Helvetica, Arial, sans-serif; color: #111827; }

        .sheet { border: 1px solid #e5e7eb; padding: 10mm; }
        .accent { height: 6px; background: {{ $primaryColor }}; border-radius: 999px; }
        .muted { color: #6b7280; }
        .mono { font-family: Courier, monospace; font-size: 10px; }

        .title { font-size: 28px; font-weight: 800; margin: 14px 0 2px; }
        .subtitle { font-size: 12px; margin: 0 0 16px; }

        .name { font-size: 22px; font-weight: 800; margin: 10px 0 4px; }
        .course { font-size: 14px; margin: 0; }

        .meta { margin-top: 10px; font-size: 11px; }

        .grid { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .grid td { vertical-align: top; }
        .leftcol { width: 36%; padding-right: 10px; }
        .rightcol { width: 64%; padding-left: 10px; }

        .card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; }
        .hr { height: 1px; background: #e5e7eb; margin: 10px 0; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .badge-bad { background: #fee2e2; color: #991b1b; }
        .badge-warn { background: #fef9c3; color: #854d0e; }

        table.table { width: 100%; border-collapse: collapse; }
        table.table th, table.table td { border-top: 1px solid #e5e7eb; padding: 7px 6px; text-align: left; vertical-align: top; font-size: 11px; }
        table.table th { color: #374151; }

        .qr { width: 130px; height: 130px; border: 1px solid #e5e7eb; padding: 6px; }
        .watermark { position: fixed; top: 44%; left: 34%; transform: rotate(-20deg); font-size: 64px; font-weight: 800; color: rgba(239, 68, 68, 0.12); letter-spacing: 3px; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="accent"></div>

        @if($isRevoked)
            <div class="watermark">REVOKED</div>
        @endif

        <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="muted" style="letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">{{ $brandName }}</div>
                </td>
                <td style="vertical-align: middle; text-align: right;">
                    @if($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="Logo" style="height: 38px; width: auto;">
                    @endif
                </td>
            </tr>
        </table>

        <div class="title">Certificate of Completion</div>
        <div class="subtitle muted">This certifies that the student successfully completed the course and the assessments listed below.</div>

        <div class="name">{{ $certificate->user->name }}</div>
        <p class="course">has successfully completed <strong>{{ $certificate->course->title }}</strong></p>

        <div class="meta muted">
            Issued: <strong>{{ $issuedAt->format('M j, Y') }}</strong>
            @if($certificate->certificate_number)
                &nbsp; • &nbsp; Certificate #: <strong>{{ $certificate->certificate_number }}</strong>
            @endif
        </div>

        <table class="grid">
            <tr>
                <td class="leftcol">
                    <div class="card">
                        <div style="display: table; width: 100%;">
                            <div style="display: table-cell; vertical-align: middle;">
                                <strong>Certificate status</strong>
                            </div>
                            <div style="display: table-cell; text-align: right; vertical-align: middle;">
                                @if($isRevoked)
                                    <span class="badge badge-bad">Revoked</span>
                                @else
                                    <span class="badge badge-ok">Active</span>
                                @endif
                            </div>
                        </div>

                        @if($isRevoked)
                            <div class="hr"></div>
                            <div class="muted" style="font-size: 11px;">Revocation reason</div>
                            <div style="color:#991b1b; font-size: 11px;">{{ $certificate->revocation_reason ?: '—' }}</div>
                        @endif

                        @if($summary['has_any_score'] || $summary['sum_max'])
                            <div class="hr"></div>
                            <div><strong>Score summary</strong></div>
                            <div class="muted" style="font-size: 11px;">
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
                        <div><strong>Verify</strong></div>
                        <div class="muted" style="font-size: 11px;">Scan QR or open:</div>
                        <div class="mono">{{ $verifyUrl }}</div>
                        <div class="muted" style="font-size: 11px; margin-top: 6px;">Verification ID: <span class="mono">{{ $certificate->uuid }}</span></div>

                        <div style="text-align:center; margin-top: 10px;">
                            <img class="qr" src="{{ $qrDataUri }}" alt="QR code">
                        </div>
                    </div>
                </td>

                <td class="rightcol">
                    <div class="card">
                        <div style="margin-bottom: 6px;">
                            <strong>Assessment performance</strong>
                            <div class="muted" style="font-size: 11px;">Per-evaluation status, score, and feedback.</div>
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
                                                <div class="muted" style="font-size: 11px;">{{ $evaluation->description }}</div>
                                            @endif
                                        </td>
                                        <td><span class="{{ $badge }}">{{ ucfirst($gStatus) }}</span></td>
                                        <td class="muted">
                                            @if(!is_null($grade?->score))
                                                {{ $grade->score }}@if($evaluation->max_score)/{{ $evaluation->max_score }}@endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="muted">{{ $grade?->feedback ?: '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="muted" style="padding: 12px;">No evaluations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="muted" style="font-size: 11px; margin-top: 10px;">
                            This certificate PDF intentionally does not show student email.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

