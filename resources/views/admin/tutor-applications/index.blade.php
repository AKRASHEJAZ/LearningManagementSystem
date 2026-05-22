<x-admin-layout>
    <x-slot name="header">
        <div>
            <div class="h5 mb-0">Tutor applications</div>
            <div class="text-secondary small">Review and approve tutors per course.</div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Course</th>
                        <th>User</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 280px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td>{{ $app->id }}</td>
                            <td class="fw-semibold">{{ $app->course->title }}</td>
                            <td>
                                <div class="fw-semibold">{{ $app->user->name }}</div>
                                <div class="text-secondary small">{{ $app->user->email }}</div>
                            </td>
                            <td><span class="badge text-bg-light border">{{ $app->status }}</span></td>
                            <td class="text-end">
                                @if($app->status === 'pending')
                                    <form class="d-inline" method="POST" action="{{ route('admin.tutor-applications.approve', $app) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success" type="submit">Approve</button>
                                    </form>
                                    <form class="d-inline" method="POST" action="{{ route('admin.tutor-applications.reject', $app) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Reject</button>
                                    </form>
                                @else
                                    <span class="text-secondary small">{{ $app->reviewed_at }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($app->motivation)
                            <tr>
                                <td></td>
                                <td colspan="4" class="text-secondary small">
                                    <span class="fw-semibold">Motivation:</span> {{ $app->motivation }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">No tutor applications.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $applications->links() }}
        </div>
    </div>
</x-admin-layout>

