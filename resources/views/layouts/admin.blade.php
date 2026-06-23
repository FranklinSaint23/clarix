<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — {{ 'E-Learning' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sidebar fixe */
        .sidebar {
            min-height: 100vh;
            width: 240px;
            background: #1e293b;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            padding-top: 1rem;
            transition: transform .3s;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: .6rem 1.25rem;
            border-radius: .375rem;
            margin: .1rem .75rem;
            font-size: .875rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link i { width: 1.25rem; }
        .sidebar-brand {
            padding: .5rem 1.25rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            margin-bottom: .75rem;
        }
        .main-content {
            margin-left: 240px;
            min-height: 100vh;
            background: #f8fafc;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav class="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2">
            <i class="bi bi-mortarboard-fill text-primary fs-5"></i>
            <span class="text-white fw-semibold">E-Learning</span>
        </a>
    </div>

    @include('components.sidebar')
</nav>

{{-- Contenu principal --}}
<div class="main-content">

    {{-- Topbar --}}
    <div class="topbar d-flex align-items-center justify-content-between">
        <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <h6 class="mb-0 fw-semibold">@yield('page-title', 'Tableau de bord')</h6>

        <div class="d-flex align-items-center gap-3">
            {{-- Notifications --}}
            <a href="#" class="text-muted position-relative">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                    3
                </span>
            </a>

            {{-- Avatar utilisateur --}}
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                         style="width:34px;height:34px;font-size:.8rem;font-weight:600;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="small fw-medium d-none d-md-inline">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Contenu --}}
    <div class="p-4">
        @include('components.alert')
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('show');
    });
</script>
@stack('scripts')
</body>
</html>
