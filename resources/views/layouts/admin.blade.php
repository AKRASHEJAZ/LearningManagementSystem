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

        <title>{{ $brandName }} — Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root { --lms-primary: {{ $primaryColor }}; }
        </style>
    </head>
    <body class="bg-light">
        <div class="admin-shell">
            <aside class="admin-sidebar">
                <div class="admin-brand">
                    <a class="d-flex align-items-center gap-2 text-decoration-none text-white" href="{{ route('admin.dashboard') }}">
                        @if ($logoPath)
                            <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo" style="width:28px;height:28px;object-fit:contain;">
                        @else
                            <x-application-logo style="width:28px;height:28px;fill:#fff;" />
                        @endif
                        <div class="lh-sm">
                            <div class="fw-semibold">{{ $brandName }}</div>
                            <div class="admin-subtitle">Admin</div>
                        </div>
                    </a>
                </div>

                <nav class="admin-nav">
                    <a class="admin-nav-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">
                        Dashboard
                    </a>
                    <a class="admin-nav-link @if(request()->routeIs('admin.courses.*')) active @endif" href="{{ route('admin.courses.index') }}">Courses</a>
                    <a class="admin-nav-link @if(request()->routeIs('admin.user-approvals.*')) active @endif" href="{{ route('admin.user-approvals.index') }}">
                        User approvals
                    </a>
                    <a class="admin-nav-link @if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">
                        User management
                    </a>
                    <a class="admin-nav-link @if(request()->routeIs('admin.settings.*')) active @endif" href="{{ route('admin.settings.edit') }}">
                        System settings
                    </a>

                    <div class="admin-nav-section">Workflows</div>
                    <a class="admin-nav-link @if(request()->routeIs('admin.tutor-applications.*')) active @endif" href="{{ route('admin.tutor-applications.index') }}">Tutor applications</a>
                    <a class="admin-nav-link @if(request()->routeIs('admin.certificates.*')) active @endif" href="{{ route('admin.certificates.index') }}">Certificates</a>
                    <a class="admin-nav-link @if(request()->routeIs('admin.achievements.*')) active @endif" href="{{ route('admin.achievements.index') }}">Achievements</a>
                </nav>

                <div class="admin-sidebar-footer">
                    <a class="admin-nav-link" href="{{ route('dashboard') }}">Back to app</a>
                </div>
            </aside>

            <div class="admin-main">
                <header class="admin-topbar">
                    <div class="d-flex align-items-center gap-2 w-100">
                        <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarMobile" aria-controls="adminSidebarMobile">
                            Menu
                        </button>

                        <form class="ms-auto admin-search" role="search" method="GET" action="{{ route('admin.search') }}">
                            <input class="form-control form-control-sm" name="q" type="search" placeholder="Search users, courses, certificates..." aria-label="Search" value="{{ request('q') }}">
                        </form>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Log out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>

                @isset($header)
                    <div class="admin-pagehead">
                        <div class="container-fluid px-3 px-lg-4">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="admin-content">
                    <div class="container-fluid px-3 px-lg-4 py-3">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="adminSidebarMobile" aria-labelledby="adminSidebarMobileLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="adminSidebarMobileLabel">Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="list-group">
                    <a class="list-group-item list-group-item-action @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a class="list-group-item list-group-item-action @if(request()->routeIs('admin.user-approvals.*')) active @endif" href="{{ route('admin.user-approvals.index') }}">User approvals</a>
                    <a class="list-group-item list-group-item-action @if(request()->routeIs('admin.settings.*')) active @endif" href="{{ route('admin.settings.edit') }}">System settings</a>
                    <a class="list-group-item list-group-item-action @if(request()->routeIs('admin.courses.*')) active @endif" href="{{ route('admin.courses.index') }}">Courses</a>
                    <a class="list-group-item list-group-item-action @if(request()->routeIs('admin.certificates.*')) active @endif" href="{{ route('admin.certificates.index') }}">Certificates</a>
                    <a class="list-group-item list-group-item-action @if(request()->routeIs('admin.achievements.*')) active @endif" href="{{ route('admin.achievements.index') }}">Achievements</a>
                    <a class="list-group-item list-group-item-action" href="{{ route('dashboard') }}">Back to app</a>
                </div>
            </div>
        </div>
    </body>
</html>
