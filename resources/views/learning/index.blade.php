<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="h5 mb-0">My learning</div>
            <div class="text-secondary small">Your course applications and enrollments.</div>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th style="width: 140px;">Status</th>
                        <th style="width: 200px;">Updated</th>
                        <th style="width: 160px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $e)
                        <tr>
                            <td class="fw-semibold">{{ $e->course->title }}</td>
                            <td><span class="badge text-bg-light border">{{ $e->status }}</span></td>
                            <td class="text-secondary small">{{ $e->updated_at }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('courses.show', $e->course->slug) }}">Open</a>
                                @if(in_array($e->status, ['accepted', 'completed'], true))
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.results.show', $e->course->slug) }}">Results</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">No courses yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

