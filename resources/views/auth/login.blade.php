<x-guest-layout>
    <h1 class="h5 mb-3">Log in</h1>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">
                {{ __('Remember me') }}
            </label>
        </div>

        <div class="d-flex align-items-center justify-content-between gap-3">
            @if (Route::has('password.request'))
                <a class="small text-decoration-none" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @else
                <span></span>
            @endif

            <x-primary-button>{{ __('Log in') }}</x-primary-button>
        </div>

        <hr class="my-4">
        <div class="small">
            <a class="text-decoration-none" href="{{ route('register') }}">Create an account</a>
        </div>
    </form>
</x-guest-layout>
