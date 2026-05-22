<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="h5 mb-0">Achievements</div>
                <div class="text-secondary small">Create and manage achievements and their awarding rules.</div>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.achievements.create') }}">New achievement</a>
        </div>
    </x-slot>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-2" method="GET">
                <div class="col-12 col-lg-9">
                    <input class="form-control" name="q" value="{{ $q }}" placeholder="Search by name or key">
                </div>
                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit">Search</button>
                    <a class="btn btn-outline-secondary w-100" href="{{ route('admin.achievements.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Key</th>
                        <th style="width: 120px;">Tier</th>
                        <th style="width: 110px;">Points</th>
                        <th style="width: 110px;">Active</th>
                        <th style="width: 120px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($achievements as $achievement)
                        <tr>
                            <td class="fw-semibold">{{ $achievement->name }}</td>
                            <td class="text-secondary small font-monospace">{{ $achievement->key }}</td>
                            <td><span class="badge text-bg-light border">{{ ucfirst($achievement->tier) }}</span></td>
                            <td class="text-secondary small">{{ $achievement->points }} XP</td>
                            <td>
                                @if($achievement->is_active)
                                    <span class="badge text-bg-success">Yes</span>
                                @else
                                    <span class="badge text-bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.achievements.edit', $achievement) }}">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">No achievements.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $achievements->links() }}
        </div>
    </div>
</x-admin-layout>

