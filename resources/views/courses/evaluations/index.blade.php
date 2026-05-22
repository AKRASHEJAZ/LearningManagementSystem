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

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Add evaluation</div>
                    <form method="POST" action="{{ route('courses.evaluations.store', $course->slug) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label" for="title">Title</label>
                            <input class="form-control" id="title" name="title" type="text" value="{{ old('title') }}" required>
                            <x-input-error :messages="$errors->get('title')" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="max_score">Max score (optional)</label>
                            <input class="form-control" id="max_score" name="max_score" type="number" min="1" max="1000" value="{{ old('max_score') }}">
                            <x-input-error :messages="$errors->get('max_score')" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="description">Description (optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" />
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1" @checked(old('is_required', true))>
                            <label class="form-check-label" for="is_required">Required</label>
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">Create</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th style="width: 120px;">Required</th>
                                <th style="width: 120px;">Max</th>
                                <th style="width: 160px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evaluations as $evaluation)
                                <tr>
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
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('courses.evaluations.edit', [$course->slug, $evaluation]) }}">Edit</a>
                                        <form class="d-inline" method="POST" action="{{ route('courses.evaluations.destroy', [$course->slug, $evaluation]) }}" onsubmit="return confirm('Delete evaluation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-4">No evaluations yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

