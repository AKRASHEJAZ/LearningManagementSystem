<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Certificates</div>
                <div class="text-secondary small">Revoke/reactivate certificates and regenerate PDFs.</div>
            </div>
        </div>
    </x-slot>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-lg-6">
                    <label class="form-label mb-1">Search</label>
                    <input class="form-control" name="q" value="{{ $q }}" placeholder="Student name/email, course, uuid, certificate #">
                </div>
                <div class="col-12 col-lg-3">
                    <label class="form-label mb-1">Status</label>
                    <select class="form-select" name="status">
                        <option value="" @selected($status === '')>All</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="revoked" @selected($status === 'revoked')>Revoked</option>
                    </select>
                </div>
                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary w-100" href="{{ route('admin.certificates.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th style="width: 140px;">Issued</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 340px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                        @php
                            $cStatus = $certificate->status ?? 'active';
                            $badge = $cStatus === 'revoked' ? 'text-bg-danger' : 'text-bg-success';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $certificate->user->name }}</div>
                                <div class="text-secondary small">{{ $certificate->user->email }}</div>
                                <div class="text-secondary small">ID: <span class="font-monospace">{{ $certificate->uuid }}</span></div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $certificate->course->title }}</div>
                                <div class="text-secondary small"># {{ $certificate->certificate_number ?: '—' }}</div>
                            </td>
                            <td class="text-secondary small">{{ ($certificate->issued_at ?? $certificate->created_at)?->format('M j, Y') }}</td>
                            <td><span class="badge {{ $badge }}">{{ ucfirst($cStatus) }}</span></td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('certificates.verify.show', $certificate) }}" target="_blank" rel="noopener">Verify</a>
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('certificates.verify.pdf', $certificate) }}" target="_blank" rel="noopener">PDF</a>

                                    <form method="POST" action="{{ route('admin.certificates.regenerate', $certificate) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary" type="submit">Regen PDF</button>
                                    </form>

                                    @if($cStatus === 'revoked')
                                        <form method="POST" action="{{ route('admin.certificates.reactivate', $certificate) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit">Reactivate</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.certificates.revoke', $certificate) }}" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            <input class="form-control form-control-sm" name="reason" placeholder="Reason (optional)" style="width: 170px;">
                                            <button class="btn btn-sm btn-danger" type="submit">Revoke</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">No certificates.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white">
            {{ $certificates->links() }}
        </div>
    </div>
</x-admin-layout>

