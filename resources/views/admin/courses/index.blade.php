<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Courses</div>
                <div class="text-secondary small">Create and publish courses.</div>
            </div>
            <a class="btn btn-sm btn-primary" href="{{ route('admin.courses.create') }}">New course</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Title</th>
                        <th style="width: 140px;">Status</th>
                        <th style="width: 160px;">Updated</th>
                        <th style="width: 220px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $course->title }}</div>
                                <div class="text-secondary small">{{ $course->slug }}</div>
                            </td>
                            <td>
                                @php
                                    $badge = match ($course->status) {
                                        'published' => 'text-bg-success',
                                        'archived' => 'text-bg-secondary',
                                        default => 'text-bg-warning',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $course->status }}</span>
                            </td>
                            <td class="text-secondary small">{{ $course->updated_at }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.courses.edit', $course) }}">Edit</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.manage', $course->slug) }}">Participants</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.tutors', $course) }}">Tutors</a>
                                <form class="d-inline" method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">No courses yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $courses->links() }}
        </div>
    </div>
</x-admin-layout>
