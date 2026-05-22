<x-admin-layout>
    <x-slot name="header">
        <div>
            <div class="h5 mb-0">Search</div>
            <div class="text-secondary small">Admin search across users, courses, certificates, and achievements.</div>
        </div>
    </x-slot>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.search') }}" class="d-flex gap-2">
                <input class="form-control" name="q" value="{{ $q }}" placeholder="Type to search...">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
        </div>
    </div>

    @if($q === '')
        <div class="text-secondary">Enter a search query above.</div>
    @else
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body border-bottom">
                        <div class="fw-semibold">Users</div>
                        <div class="text-secondary small">Top matches</div>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($users as $user)
                            <div class="list-group-item">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="text-secondary small">{{ $user->email }}</div>
                                        <div class="text-secondary small">Status: {{ $user->approval_status }} @if($user->is_admin) · Admin @endif</div>
                                    </div>
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.user-approvals.show', $user) }}">View</a>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-secondary">No users found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body border-bottom">
                        <div class="fw-semibold">Courses</div>
                        <div class="text-secondary small">Top matches</div>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($courses as $course)
                            <div class="list-group-item">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $course->title }}</div>
                                        <div class="text-secondary small">Status: {{ $course->status }}</div>
                                    </div>
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.edit', $course) }}">Edit</a>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-secondary">No courses found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body border-bottom">
                        <div class="fw-semibold">Certificates</div>
                        <div class="text-secondary small">Top matches</div>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($certificates as $certificate)
                            @php
                                $status = $certificate->status ?? 'active';
                                $badge = $status === 'revoked' ? 'text-bg-danger' : 'text-bg-success';
                            @endphp
                            <div class="list-group-item">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $certificate->user->name }} — {{ $certificate->course->title }}</div>
                                        <div class="text-secondary small">ID: <span class="font-monospace">{{ $certificate->uuid }}</span></div>
                                        <div><span class="badge {{ $badge }}">{{ ucfirst($status) }}</span></div>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('certificates.verify.show', $certificate) }}" target="_blank" rel="noopener">Verify</a>
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.certificates.index', ['q' => $certificate->uuid]) }}">Admin</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-secondary">No certificates found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body border-bottom">
                        <div class="fw-semibold">Achievements</div>
                        <div class="text-secondary small">Top matches</div>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($achievements as $a)
                            <div class="list-group-item">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $a->name }}</div>
                                        <div class="text-secondary small font-monospace">{{ $a->key }}</div>
                                        <div class="text-secondary small">{{ ucfirst($a->tier) }} · {{ $a->points }} XP @if(! $a->is_active) · Inactive @endif</div>
                                    </div>
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.achievements.edit', $a) }}">Edit</a>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-secondary">No achievements found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-admin-layout>

