<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="h5 mb-0">Courses</div>
            <div class="text-secondary small">Browse published courses.</div>
        </div>
    </x-slot>

    <div class="row g-3">
        @forelse($courses as $course)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ $course->title }}</div>
                        @if ($course->summary)
                            <div class="text-secondary small mt-1">{{ $course->summary }}</div>
                        @endif
                        @if ($course->duration)
                            <div class="text-secondary small mt-2">Duration: {{ $course->duration }}</div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-top">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('courses.show', $course->slug) }}">View</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-secondary">No published courses yet.</div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $courses->links() }}
    </div>
</x-app-layout>
