<x-guest-layout>
    <h1 class="h5 mb-3">Forgot password</h1>

    <div class="mb-3 text-secondary small">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="d-flex align-items-center justify-content-between gap-3">
            <a class="small text-decoration-none" href="{{ route('login') }}">Back to login</a>
            <x-primary-button>{{ __('Email Password Reset Link') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
