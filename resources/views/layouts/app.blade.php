<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $brandName = $appSettings['institute.name'] ?? config('app.name', 'Learning Platform');
            $primaryColor = $appSettings['brand.primary_color'] ?? '#0d6efd';
        @endphp

        <title>{{ $brandName }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root { --lms-primary: {{ $primaryColor }}; }
        </style>
    </head>
    <body>
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white border-bottom">
                <div class="container py-3">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="py-4">
            <div class="container">
                {{ $slot }}
            </div>
        </main>
    </body>
</html>
