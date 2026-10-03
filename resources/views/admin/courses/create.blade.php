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

        {{-- Inline evaluations builder --}}
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <div class="fw-semibold mb-1">Evaluations</div>
                <div class="text-secondary small mb-3">Define the assessments for this course. These will be locked after creation and shared across all tutors.</div>

                <div class="table-responsive">
                    <table class="table align-middle mb-2" id="eval-table">
                        <thead>
                            <tr>
                                <th>Title <span class="text-danger">*</span></th>
                                <th style="width:110px;">Max score</th>
                                <th>Description</th>
                                <th style="width:90px;">Required</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="eval-rows">
                            {{-- rows injected by JS --}}
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary" id="add-eval-row">+ Add evaluation</button>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Create course</button>
        </div>
    </form>

    <script>
        (function () {
            let idx = 0;
            const tbody = document.getElementById('eval-rows');

            function addRow(data) {
                const i = idx++;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input class="form-control form-control-sm" type="text" name="evaluations[${i}][title]" value="${data?.title ?? ''}" placeholder="e.g. Final Project" required></td>
                    <td><input class="form-control form-control-sm" type="number" name="evaluations[${i}][max_score]" value="${data?.max_score ?? ''}" min="1" max="1000" placeholder="e.g. 100"></td>
                    <td><input class="form-control form-control-sm" type="text" name="evaluations[${i}][description]" value="${data?.description ?? ''}" placeholder="Optional description"></td>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center mb-0">
                            <input class="form-check-input" type="checkbox" name="evaluations[${i}][is_required]" value="1" ${data?.is_required !== false ? 'checked' : ''}>
                        </div>
                    </td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">✕</button></td>
                `;
                tr.querySelector('.remove-row').addEventListener('click', () => tr.remove());
                tbody.appendChild(tr);
            }

            document.getElementById('add-eval-row').addEventListener('click', () => addRow({}));

            // Restore old() values on validation error
            @if(old('evaluations'))
                @foreach(old('evaluations') as $ev)
                    addRow({
                        title: @json($ev['title'] ?? ''),
                        max_score: @json($ev['max_score'] ?? ''),
                        description: @json($ev['description'] ?? ''),
                        is_required: @json(isset($ev['is_required']) ? (bool)$ev['is_required'] : true),
                    });
                @endforeach
            @endif
        })();
    </script>
</x-admin-layout>

