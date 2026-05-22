<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Edit achievement</div>
                <div class="text-secondary small">{{ $achievement->name }} · <span class="font-monospace">{{ $achievement->key }}</span></div>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('admin.achievements.index') }}">Back</a>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.achievements.update', $achievement) }}">
                @csrf
                @method('PUT')

                @include('admin.achievements._form', ['achievement' => $achievement, 'courses' => $courses, 'ruleCourseIds' => $ruleCourseIds])

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.achievements.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

