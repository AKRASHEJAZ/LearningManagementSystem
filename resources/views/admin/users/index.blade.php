<x-admin-layout>
    <x-slot name="header">
        <div>
            <div class="h5 mb-0">User management</div>
            <div class="text-secondary small">Promote/demote admins. (Use approvals to control access.)</div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width: 140px;">Approval</th>
                        <th style="width: 120px;">Admin</th>
                        <th style="width: 200px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td class="text-secondary small">{{ $user->email }}</td>
                            <td><span class="badge text-bg-light border">{{ $user->approval_status }}</span></td>
                            <td>
                                @if($user->is_admin)
                                    <span class="badge text-bg-primary">Yes</span>
                                @else
                                    <span class="badge text-bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(auth()->id() === $user->id)
                                    <span class="text-secondary small">Current user</span>
                                @else
                                    <form class="d-inline" method="POST" action="{{ route('admin.users.admin', $user) }}">
                                        @csrf
                                        <input type="hidden" name="make_admin" value="{{ $user->is_admin ? 0 : 1 }}">
                                        <button class="btn btn-sm {{ $user->is_admin ? 'btn-outline-danger' : 'btn-outline-primary' }}" type="submit">
                                            {{ $user->is_admin ? 'Demote' : 'Promote' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $users->links() }}
        </div>
    </div>
</x-admin-layout>

