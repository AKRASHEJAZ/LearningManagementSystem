<section>
    <header>
        <h2 class="h6 mb-1">{{ __('Delete Account') }}</h2>
        <p class="text-secondary small mb-0">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}</p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" class="mt-3">
        @csrf
        @method('delete')

        <div class="mb-3">
            <x-input-label for="delete_password" value="{{ __('Password') }}" />
            <x-text-input id="delete_password" name="password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->userDeletion->get('password')" />
        </div>

        <x-danger-button onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.')">
            {{ __('Delete Account') }}
        </x-danger-button>
    </form>
</section>
