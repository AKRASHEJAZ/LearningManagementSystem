@php
    $brandName = $appSettings['institute.name'] ?? config('app.name', 'Learning Platform');
    $logoPath = $appSettings['brand.logo_path'] ?? null;
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            @if ($logoPath)
                <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo" style="width:24px;height:24px;object-fit:contain;">
            @else
                <x-application-logo style="width:24px;height:24px;" />
            @endif
            <span class="fw-semibold">{{ $brandName }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('courses.*')) active @endif" href="{{ route('courses.index') }}">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('learning.*')) active @endif" href="{{ route('learning.index') }}">My learning</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('tutoring.*')) active @endif" href="{{ route('tutoring.index') }}">My tutoring</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('certificates.*')) active @endif" href="{{ route('certificates.mine') }}">My certificates</a>
                </li>
                @if (Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('admin.*')) active @endif" href="{{ route('admin.dashboard') }}">Admin</a>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('me') }}">My profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        @if (Auth::user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.settings.edit') }}">Settings</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Log Out</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
