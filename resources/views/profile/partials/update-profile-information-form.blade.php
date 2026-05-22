<section>
    <header>
        <h2 class="h6 mb-1">{{ __('Profile Information') }}</h2>
        <p class="text-secondary small mb-0">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-3">
        @csrf
        @method('patch')

        <div class="mb-3">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="small">
                    <div class="text-warning-emphasis">Your email address is unverified.</div>
                    <button form="send-verification" class="btn btn-link p-0 small">Re-send verification email</button>

                    @if (session('status') === 'verification-link-sent')
                        <div class="text-success mt-1">A new verification link has been sent to your email address.</div>
                    @endif
                </div>
            @endif
        </div>

        <x-primary-button>{{ __('Save') }}</x-primary-button>
    </form>
</section>
