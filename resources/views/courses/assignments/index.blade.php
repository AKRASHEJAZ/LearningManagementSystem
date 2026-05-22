<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Tutor assignments</div>
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
                    <div class="fw-semibold mb-2">Assign tutor to student</div>
                    <form method="POST" action="{{ route('courses.assignments.store', $course->slug) }}">
                        @csrf

                        <div class="mb-2">
                            <label class="form-label" for="tutor_user_id">Tutor</label>
                            <select class="form-select" id="tutor_user_id" name="tutor_user_id" required>
                                <option value="">Select tutor</option>
                                @foreach($tutors as $t)
                                    <option value="{{ $t->user_id }}" @selected(old('tutor_user_id') == $t->user_id)>{{ $t->user->name }} ({{ $t->user->email }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tutor_user_id')" />
                        </div>

                        <div class="mb-2">
                            <label class="form-label" for="student_user_id">Student</label>
                            <select class="form-select" id="student_user_id" name="student_user_id" required>
                                <option value="">Select student</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->user_id }}" @selected(old('student_user_id') == $s->user_id)>{{ $s->user->name }} ({{ $s->user->email }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('student_user_id')" />
                        </div>

                        <button class="btn btn-primary btn-sm" type="submit" @disabled($tutors->count() === 0 || $students->count() === 0)>
                            Assign
                        </button>
                        @if($tutors->count() === 0)
                            <div class="text-secondary small mt-2">No tutors assigned to this course yet.</div>
                        @endif
                        @if($students->count() === 0)
                            <div class="text-secondary small mt-2">No accepted students yet.</div>
                        @endif
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
                                <th>Student</th>
                                <th>Tutor(s)</th>
                                <th style="width: 120px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $s)
                                @php
                                    $assigned = $assignments->get($s->user_id) ?? collect();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $s->user->name }}</div>
                                        <div class="text-secondary small">{{ $s->user->email }}</div>
                                    </td>
                                    <td>
                                        @if($assigned->count() === 0)
                                            <span class="text-secondary small">Unassigned</span>
                                        @else
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($assigned as $a)
                                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                                        <div class="small">
                                                            <span class="fw-semibold">{{ $a->tutor->name }}</span>
                                                            <span class="text-secondary">({{ $a->tutor->email }})</span>
                                                        </div>
                                                        <form method="POST" action="{{ route('courses.assignments.destroy', [$course->slug, $a]) }}" onsubmit="return confirm('End this assignment?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger" type="submit">End</button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end text-secondary small">
                                        {{ ucfirst($s->status) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-secondary py-4">No students.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

