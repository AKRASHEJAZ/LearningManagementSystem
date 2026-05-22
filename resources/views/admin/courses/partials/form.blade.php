@php
    $title = old('title', $course?->title);
    $slug = old('slug', $course?->slug);
    $summary = old('summary', $course?->summary);
    $duration = old('duration', $course?->duration);
    $description = old('description', $course?->description);
    $completionCriteria = old('completion_criteria', $course?->completion_criteria);
    $maxParticipants = old('max_participants', $course?->max_participants);
    $status = old('status', $course?->status ?? 'draft');
@endphp

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" id="title" name="title" type="text" value="{{ $title }}" required>
                    <x-input-error :messages="$errors->get('title')" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="slug">Slug (optional)</label>
                    <input class="form-control" id="slug" name="slug" type="text" value="{{ $slug }}" placeholder="e.g. web-development-basics">
                    <div class="form-text">Leave empty to auto-generate from title.</div>
                    <x-input-error :messages="$errors->get('slug')" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="summary">Summary (optional)</label>
                    <input class="form-control" id="summary" name="summary" type="text" value="{{ $summary }}">
                    <x-input-error :messages="$errors->get('summary')" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="duration">Duration (optional)</label>
                    <input class="form-control" id="duration" name="duration" type="text" value="{{ $duration }}" placeholder="e.g. 6 weeks, 12 hours">
                    <x-input-error :messages="$errors->get('duration')" />
                </div>

                <div class="mb-0">
                    <label class="form-label" for="description">Description (optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="8">{{ $description }}</textarea>
                    <x-input-error :messages="$errors->get('description')" />
                </div>

                <div class="mt-3">
                    <label class="form-label" for="completion_criteria">Completion criteria (optional)</label>
                    <textarea class="form-control" id="completion_criteria" name="completion_criteria" rows="5" placeholder="What a student must do to complete this course">{{ $completionCriteria }}</textarea>
                    <div class="form-text">Course-level guidance only. Actual completion will be tracked per student later.</div>
                    <x-input-error :messages="$errors->get('completion_criteria')" />
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="draft" @selected($status === 'draft')>Draft</option>
                        <option value="published" @selected($status === 'published')>Published</option>
                        <option value="archived" @selected($status === 'archived')>Archived</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="max_participants">Max participants (optional)</label>
                    <input class="form-control" id="max_participants" name="max_participants" type="number" min="1" max="500" value="{{ $maxParticipants }}" placeholder="e.g. 20">
                    <div class="form-text">If set, only up to this many students can be accepted.</div>
                    <x-input-error :messages="$errors->get('max_participants')" />
                </div>

                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </div>
    </div>
</div>
