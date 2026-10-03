<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Evaluations</div>
                <div class="text-secondary small">{{ $course->title }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.manage', $course->slug) }}">Participants</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.show', $course->slug) }}">View course</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body pb-2">
            <div class="fw-semibold mb-1">Course assessments</div>
            <div class="text-secondary small mb-3">
                These evaluations are set by the admin and are <strong>locked</strong>.
                @if(auth()->user()->isAdmin())
                    To add more, go to the <a href="{{ route('admin.courses.edit', $course) }}">course edit page</a>.
                @endif
            </div>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th style="width: 120px;">Required</th>
                        <th style="width: 120px;">Max score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $evaluation)
                        <tr>
                            <td class="text-secondary small">{{ $evaluation->position }}</td>
                            <td>
                                <div class="fw-semibold">{{ $evaluation->title }}</div>
                                @if($evaluation->description)
                                    <div class="text-secondary small">{{ $evaluation->description }}</div>
                                @endif
                            </td>
                            <td>
                                @if($evaluation->is_required)
                                    <span class="badge text-bg-primary">Yes</span>
                                @else
                                    <span class="badge text-bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-secondary small">{{ $evaluation->max_score ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">No evaluations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
