<x-app-layout>
    <x-slot name="header">
        <h1 class="h5 mb-0">{{ __('Profile') }}</h1>
    </x-slot>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success">Profile updated.</div>
            @endif
            @if (session('status') === 'password-updated')
                <div class="alert alert-success">Password updated.</div>
            @endif

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
