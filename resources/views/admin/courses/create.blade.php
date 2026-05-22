<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="h5 mb-0">New course</div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.index') }}">Back</a>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.courses.store') }}">
        @csrf
        @include('admin.courses.partials.form', ['course' => null])
    </form>
</x-admin-layout>

