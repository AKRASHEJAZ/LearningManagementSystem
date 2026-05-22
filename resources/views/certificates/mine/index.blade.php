<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">My certificates</div>
                <div class="text-secondary small">Download or verify your certificates.</div>
            </div>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th style="width: 160px;">Issued</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 220px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                        @php
                            $status = $certificate->status ?? 'active';
                            $badge = $status === 'revoked' ? 'text-bg-danger' : 'text-bg-success';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $certificate->course->title }}</div>
                                <div class="text-secondary small">ID: <span class="font-monospace">{{ $certificate->uuid }}</span></div>
                            </td>
                            <td class="text-secondary small">
                                {{ ($certificate->issued_at ?? $certificate->created_at)?->format('M j, Y') }}
                            </td>
                            <td><span class="badge {{ $badge }}">{{ ucfirst($status) }}</span></td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('certificates.verify.show', $certificate) }}" target="_blank" rel="noopener">
                                        Verify
                                    </a>
                                    <a class="btn btn-sm btn-primary" href="{{ route('certificates.verify.pdf', $certificate) }}">
                                        Download PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-5">No certificates yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($certificates, 'links'))
            <div class="card-footer bg-white">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

