<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Edit course</div>
                <div class="text-secondary small">{{ $course->title }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.manage', $course->slug) }}">Participants</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.tutors', $course) }}">Tutors</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.index') }}">Back</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.courses.update', $course) }}">
        @csrf
        @method('PUT')
        @include('admin.courses.partials.form', ['course' => $course])
    </form>
</x-admin-layout>
