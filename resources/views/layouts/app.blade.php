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
        @include('layouts.navigation')

        @isset($header)
            <div class="bg-white shadow-sm border-bottom">
                <div class="container py-3">
                    <h5 class="mb-0">{{ $header }}</h5>
                </div>
            </div>
        @endisset

        <main class="py-4">
            <div class="container">
                {{ $slot }}
            </div>
        </main>
    </body>
</html>
