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

    {{-- Locked evaluations list --}}
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <div class="fw-semibold mb-1">Evaluations</div>
            <div class="text-secondary small mb-3">
                Existing evaluations are <strong>locked</strong> and cannot be edited. You may add new ones.
            </div>

            @if($evaluations->count() > 0)
                <div class="table-responsive mb-3">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th style="width:110px;">Max score</th>
                                <th>Description</th>
                                <th style="width:90px;">Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluations as $ev)
                                <tr>
                                    <td class="text-secondary small">{{ $ev->position }}</td>
                                    <td class="fw-semibold">{{ $ev->title }}</td>
                                    <td class="text-secondary">{{ $ev->max_score ?? '—' }}</td>
                                    <td class="text-secondary small">{{ $ev->description ?? '—' }}</td>
                                    <td>
                                        @if($ev->is_required)
                                            <span class="badge text-bg-primary">Yes</span>
                                        @else
                                            <span class="badge text-bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-secondary small mb-3">No evaluations yet.</div>
            @endif

            {{-- Add more evaluations --}}
            <form method="POST" action="{{ route('courses.evaluations.store', $course->slug) }}" id="add-eval-form">
                @csrf
                <div class="table-responsive">
                    <table class="table align-middle mb-2">
                        <thead>
                            <tr>
                                <th>Title <span class="text-danger">*</span></th>
                                <th style="width:110px;">Max score</th>
                                <th>Description</th>
                                <th style="width:90px;">Required</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="new-eval-rows">
                            {{-- rows injected by JS --}}
                        </tbody>
                    </table>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-eval-row">+ Add row</button>
                    <button type="submit" class="btn btn-sm btn-primary d-none" id="save-evals-btn">Save new evaluations</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            let idx = 0;
            const tbody = document.getElementById('new-eval-rows');
            const saveBtn = document.getElementById('save-evals-btn');

            function addRow() {
                const i = idx++;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input class="form-control form-control-sm" type="text" name="title" placeholder="e.g. Week 3 Quiz" required></td>
                    <td><input class="form-control form-control-sm" type="number" name="max_score" min="1" max="1000" placeholder="e.g. 100"></td>
                    <td><input class="form-control form-control-sm" type="text" name="description" placeholder="Optional"></td>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center mb-0">
                            <input class="form-check-input" type="checkbox" name="is_required" value="1" checked>
                        </div>
                    </td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">✕</button></td>
                `;
                tr.querySelector('.remove-row').addEventListener('click', () => {
                    tr.remove();
                    if (!tbody.hasChildNodes()) saveBtn.classList.add('d-none');
                });
                tbody.appendChild(tr);
                saveBtn.classList.remove('d-none');
            }

            document.getElementById('add-eval-row').addEventListener('click', addRow);
        })();
    </script>
</x-admin-layout>
