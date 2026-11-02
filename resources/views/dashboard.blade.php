<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="card-text">{{ __("You're logged in!") }}</p>
        </div>
    </div>
</x-app-layout>
