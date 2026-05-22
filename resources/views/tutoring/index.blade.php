<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="h5 mb-0">My tutoring</div>
            <div class="text-secondary small">Courses you tutor and students assigned to you.</div>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold mb-2">My courses</div>
                    @if($courses->count() === 0)
                        <div class="text-secondary">You’re not assigned as tutor to any course yet.</div>
                    @else
                        <div class="list-group">
                            @foreach($courses as $c)
                                <a class="list-group-item list-group-item-action" href="{{ route('courses.manage', $c->slug) }}">
                                    <div class="fw-semibold">{{ $c->title }}</div>
                                    <div class="text-secondary small">Manage participants</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Assigned students</div>
                    @if($assignments->count() === 0)
                        <div class="text-secondary">No students assigned to you yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th style="width: 160px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $a)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $a->student->name }}</div>
                                                <div class="text-secondary small">{{ $a->student->email }}</div>
                                            </td>
                                            <td class="fw-semibold">{{ $a->course->title }}</td>
                                            <td class="text-end">
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('courses.grading.show', $a->course->slug) }}">Grade</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

