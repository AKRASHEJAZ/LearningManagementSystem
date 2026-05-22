<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Tutors</div>
                <div class="text-secondary small">{{ $course->title }}</div>
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.index') }}">Back</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Add tutor</div>

                    <form class="mb-3" method="GET" action="{{ route('admin.courses.tutors', $course) }}">
                        <label class="form-label" for="q">Search user</label>
                        <div class="input-group">
                            <input class="form-control" id="q" name="q" type="text" value="{{ $q }}" placeholder="Search by name or email">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                        <div class="form-text">Shows approved users (and admins) not already assigned as tutors.</div>
                    </form>

                    @if ($q !== '')
                        <div class="fw-semibold mb-2">Results</div>
                        <div class="list-group">
                            @forelse($candidates as $candidate)
                                <div class="list-group-item d-flex align-items-center justify-content-between gap-2">
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate">{{ $candidate->name }}</div>
                                        <div class="text-secondary small text-truncate">{{ $candidate->email }}</div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.courses.tutors.store', $course) }}">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $candidate->id }}">
                                        <button class="btn btn-sm btn-primary" type="submit">Add</button>
                                    </form>
                                </div>
                            @empty
                                <div class="list-group-item text-secondary">No matching users.</div>
                            @endforelse
                        </div>
                    @else
                        <div class="text-secondary small">Search for a user to add them as a tutor.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th style="width: 140px;">Status</th>
                                <th style="width: 120px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tutors as $tutor)
                                <tr>
                                    <td class="fw-semibold">{{ $tutor->user->name }}</td>
                                    <td class="text-secondary small">{{ $tutor->user->email }}</td>
                                    <td><span class="badge text-bg-success">{{ $tutor->status }}</span></td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.courses.tutors.destroy', [$course, $tutor]) }}" onsubmit="return confirm('Remove tutor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-4">No tutors assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
