<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Edit evaluation</div>
                <div class="text-secondary small">{{ $course->title }}</div>
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.evaluations.index', $course->slug) }}">Back</a>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('courses.evaluations.update', [$course->slug, $evaluation]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" id="title" name="title" type="text" value="{{ old('title', $evaluation->title) }}" required>
                    <x-input-error :messages="$errors->get('title')" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="max_score">Max score (optional)</label>
                    <input class="form-control" id="max_score" name="max_score" type="number" min="1" max="1000" value="{{ old('max_score', $evaluation->max_score) }}">
                    <x-input-error :messages="$errors->get('max_score')" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description (optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $evaluation->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" />
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1" @checked(old('is_required', $evaluation->is_required))>
                    <label class="form-check-label" for="is_required">Required</label>
                </div>

                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>
</x-app-layout>

