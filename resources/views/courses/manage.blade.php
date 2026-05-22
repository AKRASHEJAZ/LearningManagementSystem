<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Participants</div>
                <div class="text-secondary small">{{ $course->title }}</div>
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.show', $course->slug) }}">View course</a>
        </div>
    </x-slot>

    @include('courses.partials.manage-participants')
</x-app-layout>

