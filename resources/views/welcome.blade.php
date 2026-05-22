<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $brandName = $appSettings['institute.name'] ?? config('app.name', 'Learning Platform');
            $primaryColor = $appSettings['brand.primary_color'] ?? '#0d6efd';
            $logoPath = $appSettings['brand.logo_path'] ?? null;
        @endphp

        <title>{{ $brandName }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root { --lms-primary: {{ $primaryColor }}; }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if ($logoPath)
                            <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo" style="width:40px;height:40px;object-fit:contain;">
                        @else
                            <x-application-logo style="width:40px;height:40px;" />
                        @endif
                        <div>
                            <div class="h4 mb-0">{{ $brandName }}</div>
                            <div class="text-secondary">A practical, community-driven learning platform for college.</div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap gap-2">
                                @auth
                                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Go to Dashboard</a>
                                @else
                                    <a class="btn btn-primary" href="{{ route('login') }}">Log in</a>
                                    <a class="btn btn-outline-primary" href="{{ route('register') }}">Create account</a>
                                @endauth
                            </div>

                            <hr class="my-4">
                            <ul class="mb-0 text-secondary">
                                <li>Students can learn and track progress.</li>
                                <li>Students can become tutors per course.</li>
                                <li>Certificates will be publicly verifiable (QR → verification page).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
