<x-app-layout>
    <x-slot name="header">
        <h1 class="h5 mb-0">Account approval</h1>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            @if (auth()->user()->isRejected())
                <div class="alert alert-danger">
                    <div class="fw-semibold">Your account was not approved.</div>
                    @if (auth()->user()->rejection_reason)
                        <div class="mt-1">{{ auth()->user()->rejection_reason }}</div>
                    @endif
                </div>
            @else
                <div class="alert alert-warning">
                    <div class="fw-semibold">Your account is pending approval.</div>
                    <div class="mt-1">An admin must approve your account before you can use the platform.</div>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="text-secondary small">
                        Signed in as <span class="fw-semibold">{{ auth()->user()->email }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Log out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

