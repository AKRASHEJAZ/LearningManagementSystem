<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h1 class="h5 mb-0">User #{{ $user->id }}</h1>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.user-approvals.index', ['status' => $user->approval_status]) }}">Back</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-secondary">Name</dt>
                        <dd class="col-sm-8">{{ $user->name }}</dd>

                        <dt class="col-sm-4 text-secondary">Email</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>

                        <dt class="col-sm-4 text-secondary">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge text-bg-secondary">{{ $user->approval_status }}</span>
                        </dd>

                        <dt class="col-sm-4 text-secondary">Registered</dt>
                        <dd class="col-sm-8">{{ $user->created_at }}</dd>

                        @if($user->rejection_reason)
                            <dt class="col-sm-4 text-secondary">Rejection reason</dt>
                            <dd class="col-sm-8">{{ $user->rejection_reason }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Actions</div>

                    <div class="d-flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.user-approvals.approve', $user) }}">
                            @csrf
                            <button class="btn btn-success" type="submit" @disabled($user->approval_status === 'approved')>Approve</button>
                        </form>
                    </div>

                    <hr>

                    <form method="POST" action="{{ route('admin.user-approvals.reject', $user) }}">
                        @csrf

                        <div class="mb-2">
                            <label class="form-label" for="rejection_reason">Rejection reason (optional)</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3">{{ old('rejection_reason', $user->rejection_reason) }}</textarea>
                            <x-input-error :messages="$errors->get('rejection_reason')" />
                        </div>

                        <button class="btn btn-danger" type="submit" @disabled($user->approval_status === 'rejected')>Reject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
