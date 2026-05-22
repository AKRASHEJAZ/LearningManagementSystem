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
        <div class="min-vh-100 d-flex align-items-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-7 col-lg-5">
                        <div class="text-center mb-3">
                            <a href="/" class="text-decoration-none text-dark">
                                <x-application-logo class="d-inline-block" style="width:64px;height:64px;" />
                            </a>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
