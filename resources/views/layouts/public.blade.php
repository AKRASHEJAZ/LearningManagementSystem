@php
    $brandName = $appSettings['institute.name'] ?? config('app.name', 'Learning Platform');
    $primaryColor = $appSettings['brand.primary_color'] ?? '#0d6efd';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $brandName }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --brand-primary: {{ $primaryColor }}; }
        a { color: var(--brand-primary); }
        .brand-accent { background: var(--brand-primary); height: 10px; border-radius: 999px; }
    </style>
</head>
<body class="bg-light">
    {{ $slot }}
</body>
</html>

