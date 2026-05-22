<x-admin-layout>
    <x-slot name="header">
        <div>
            <div class="h5 mb-0">Dashboard</div>
            <div class="text-secondary small">Admin overview</div>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="text-secondary small">Pending approvals</div>
                            <div class="h4 mb-0">{{ $stats['pending_users'] }}</div>
                            <a class="small" href="{{ route('admin.user-approvals.index', ['status' => 'pending']) }}">Review</a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="text-secondary small">Published courses</div>
                            <div class="h4 mb-0">{{ $stats['published_courses'] }}</div>
                            <a class="small" href="{{ route('admin.courses.index') }}">Manage</a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="text-secondary small">Active tutors</div>
                            <div class="h4 mb-0">{{ $stats['active_tutors'] }}</div>
                            <div class="text-secondary small">Across all courses</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="text-secondary small">Certificates issued</div>
                            <div class="h4 mb-0">{{ $stats['certificates_issued'] }}</div>
                            <a class="small" href="{{ route('admin.certificates.index') }}">View</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">Quick actions</div>
                                <div class="text-secondary small">Common admin tasks.</div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.user-approvals.index') }}">User approvals</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.index') }}">User management</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.courses.create') }}">New course</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.achievements.index') }}">Achievements</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.settings.edit') }}">Settings</a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-3">
                        <div class="card-body border-bottom">
                            <div class="fw-semibold">Recent activity</div>
                            <div class="text-secondary small">Last updates across the platform.</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th style="width: 130px;">Status</th>
                                        <th style="width: 160px;">Updated</th>
                                        <th style="width: 90px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent['courses'] as $course)
                                        <tr>
                                            <td class="fw-semibold">{{ $course->title }}</td>
                                            <td><span class="badge text-bg-light border">{{ $course->status }}</span></td>
                                            <td class="text-secondary small">{{ $course->updated_at?->format('M j, Y') }}</td>
                                            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.edit', $course) }}">Edit</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-secondary py-4">No courses.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="fw-semibold">System status</div>
                            <div class="text-secondary small mt-1">Storage: {{ config('filesystems.default') }} · Cache: {{ config('cache.default') }}</div>
                            <div class="text-secondary small mt-1">Laravel {{ app()->version() }}</div>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-3">
                        <div class="card-body border-bottom">
                            <div class="fw-semibold">Pending users</div>
                            <div class="text-secondary small">Newest registrations awaiting approval.</div>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($recent['users_pending'] as $u)
                                <a class="list-group-item list-group-item-action" href="{{ route('admin.user-approvals.show', $u) }}">
                                    <div class="fw-semibold">{{ $u->name }}</div>
                                    <div class="text-secondary small">{{ $u->email }}</div>
                                </a>
                            @empty
                                <div class="list-group-item text-secondary">No pending users.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card shadow-sm mt-3">
                        <div class="card-body border-bottom">
                            <div class="fw-semibold">Recent certificates</div>
                            <div class="text-secondary small">Latest issued certificates.</div>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($recent['certificates'] as $c)
                                <a class="list-group-item list-group-item-action" href="{{ route('certificates.verify.show', $c) }}" target="_blank" rel="noopener">
                                    <div class="fw-semibold">{{ $c->user->name }} — {{ $c->course->title }}</div>
                                    <div class="text-secondary small">{{ ($c->issued_at ?? $c->created_at)?->format('M j, Y') }}</div>
                                </a>
                            @empty
                                <div class="list-group-item text-secondary">No certificates yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
