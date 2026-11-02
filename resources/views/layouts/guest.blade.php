<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Shoe Haven') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 py-4">
            <div class="mb-4 text-center">
                <a href="/" class="text-decoration-none d-inline-flex flex-column align-items-center">
                    <div class="d-flex align-items-center justify-content-center rounded-circle mb-2"
                         style="width: 56px; height: 56px; background-color: #fff3e6; border: 2px solid #ff6600;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ff6600" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 18c0-1.5 1-2 2-2h1.5l1-2 2.5 1.5 3-3 2.5 2 3-1.5c1 0 2 .5 2 2v3a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-1z"/>
                            <path d="M3 18h18"/>
                        </svg>
                    </div>
                    <h2 class="fw-bold mb-0" style="color: #ff6600; letter-spacing: -0.5px;">
                        Shoe Haven
                    </h2>
                    <small class="text-uppercase" style="color: #8a8a8a; font-size: 0.7rem; letter-spacing: 1.5px;">
                        Your Trusted Shoe Store
                    </small>
                </a>
            </div>
            <div class="w-100 bg-white shadow-sm rounded p-4" style="max-width: 420px;">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>