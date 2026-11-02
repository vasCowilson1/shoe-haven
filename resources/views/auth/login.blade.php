<x-guest-layout>
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="form-control"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                class="form-control"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="form-check mb-3">
            <input
                class="form-check-input"
                type="checkbox"
                name="remember"
                id="remember_me"
            >
            <label class="form-check-label" for="remember_me">
                {{ __('Remember me') }}
            </label>
        </div>

        <div class="d-grid mb-3">
            <a href="{{ route('google.login') }}" class="btn btn-outline-dark">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="18"
                     height="18"
                     fill="currentColor"
                     class="me-2"
                     viewBox="0 0 16 16">
                    <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.482-2.384 5.885h.001C11.978 15.292 10.15 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.609 2.233l-2.273 2.273C10.725 3.91 9.91 3.5 8 3.5A4.5 4.5 0 1 0 8 12.5c2.586 0 3.56-1.854 3.713-2.814H8V6.558h7.545z"/>
                </svg>

                Continue with Google
            </a>
        </div>

        <div class="d-grid gap-2">
            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a class="text-decoration-none" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        @endif

    </form>
</x-guest-layout>