<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h1 class="h5 mb-0">User approvals</h1>
            <div class="btn-group btn-group-sm" role="group" aria-label="Filter">
                <a class="btn btn-outline-secondary @if($status === 'pending') active @endif" href="{{ route('admin.user-approvals.index', ['status' => 'pending']) }}">Pending</a>
                <a class="btn btn-outline-secondary @if($status === 'rejected') active @endif" href="{{ route('admin.user-approvals.index', ['status' => 'rejected']) }}">Rejected</a>
                <a class="btn btn-outline-secondary @if($status === 'approved') active @endif" href="{{ route('admin.user-approvals.index', ['status' => 'approved']) }}">Approved</a>
            </div>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width: 180px;">Status</th>
                        <th style="width: 140px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge text-bg-secondary">{{ $user->approval_status }}</span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.user-approvals.show', $user) }}">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">No users.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($users, 'links'))
            <div class="card-body border-top">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
