<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Project Tracker') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #8BC53F;
            --primary-dark: #2D8B50;
            --primary-light: #A8D96E;
            --bg-page: #F6F7F9;
            --bg-card: #FFFFFF;
            --border: #E2E5EA;
            --text-main: #1A1D23;
            --text-secondary: #5F6673;
            --urgent: #E5484D;
            --high: #F2802B;
            --medium: #E5B10F;
            --low: #8B93A1;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
        }

        .sidebar {
            min-height: 100vh;
            width: 250px;
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 1rem;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        .sidebar .brand {
            padding: 1rem 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: block;
            transition: all 0.3s;
        }

        .sidebar .brand:hover {
            transform: scale(1.02);
        }

        .sidebar .brand i {
            color: var(--primary);
            -webkit-text-fill-color: var(--primary);
        }

        .sidebar .nav-section {
            padding: 1.5rem 1rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-secondary);
            letter-spacing: 0.5px;
        }

        .sidebar .nav-link {
            padding: 0.75rem 1.5rem;
            color: var(--text-main);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            background-color: var(--bg-page);
            color: var(--primary);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(139, 197, 63, 0.1) 0%, transparent 100%);
            color: var(--primary);
            font-weight: 600;
            border-left: 3px solid var(--primary);
            padding-left: calc(1.5rem - 3px);
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        .top-bar {
            background: var(--bg-card);
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 197, 63, 0.3);
        }

        .badge-urgent { background-color: var(--urgent); }
        .badge-high { background-color: var(--high); }
        .badge-medium { background-color: var(--medium); }
        .badge-low { background-color: var(--low); }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 2px 8px rgba(139, 197, 63, 0.3);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1100;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(139, 197, 63, 0.3);
            transition: all 0.3s;
        }

        .mobile-menu-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(139, 197, 63, 0.4);
        }

        .mobile-menu-toggle i {
            font-size: 1.25rem;
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex;
            }

            .top-bar {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.25rem;
                margin-left: 3rem;
            }

            .user-menu span.text-secondary {
                display: none;
            }
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        @media (max-width: 768px) {
            .sidebar-overlay.active {
                display: block;
            }
        }

        /* Responsive Tables */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .card {
                margin-bottom: 1rem;
            }

            .btn-group {
                flex-direction: column;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <a href="{{ route('dashboard') }}" class="brand">
                <i class="bi bi-kanban"></i> Project Tracker
            </a>

            <nav class="mt-4">
                <!-- Main Navigation -->
                <div class="nav-section">Main</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.guide') }}" class="nav-link {{ request()->routeIs('admin.guide') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Admin Guide
                </a>
                <a href="{{ route('tasks.kanban') }}" class="nav-link {{ request()->routeIs('tasks.kanban') ? 'active' : '' }}">
                    <i class="bi bi-kanban"></i> Kanban Board
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
                    <i class="bi bi-list-ul"></i> Task List
                </a>

                @if(auth()->user()->hasAnyRole(['Implementation', 'Admin', 'Lead']))
                <a href="{{ route('tasks.create') }}" class="nav-link">
                    <i class="bi bi-plus-circle"></i> New Request
                </a>
                @endif

                @if(auth()->user()->hasRole('Developer'))
                <div class="nav-section">My Work</div>
                <a href="{{ route('tasks.kanban', ['assigned' => 'me']) }}" class="nav-link">
                    <i class="bi bi-list-task"></i> My Tasks
                </a>
                @endif

                @if(auth()->user()->hasRole('QA'))
                <div class="nav-section">QA</div>
                <a href="{{ route('tasks.index') }}?status=For QA UAT" class="nav-link">
                    <i class="bi bi-clipboard-check"></i> QA Queue
                </a>
                @endif

                @if(auth()->user()->hasRole('Implementation'))
                <div class="nav-section">UAT</div>
                <a href="{{ route('tasks.index') }}?status=For Implementation UAT" class="nav-link">
                    <i class="bi bi-check2-square"></i> UAT Queue
                </a>
                @endif

                @if(auth()->user()->hasRole('Admin'))
                <div class="nav-section">Administration</div>
                <a href="{{ route('admin.sites.index') }}" class="nav-link {{ request()->routeIs('admin.sites.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Sites
                </a>
                <a href="{{ route('admin.projects.index') }}" class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i> Projects
                </a>
                <a href="{{ route('admin.site-projects.index') }}" class="nav-link {{ request()->routeIs('admin.site-projects.*') ? 'active' : '' }}">
                    <i class="bi bi-link-45deg"></i> Site Projects
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
                @endif
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>

                <div class="user-menu">
                    <span class="text-secondary small">
                        @foreach(auth()->user()->roles ?? [] as $role)
                            <span class="badge bg-secondary">{{ $role }}</span>
                        @endforeach
                    </span>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="ms-2">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleMenu() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');

                // Change icon
                const icon = mobileMenuToggle.querySelector('i');
                if (sidebar.classList.contains('active')) {
                    icon.className = 'bi bi-x-lg';
                } else {
                    icon.className = 'bi bi-list';
                }
            }

            // Toggle on button click
            mobileMenuToggle.addEventListener('click', toggleMenu);

            // Close on overlay click
            sidebarOverlay.addEventListener('click', toggleMenu);

            // Close menu when clicking a nav link on mobile
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        toggleMenu();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    const icon = mobileMenuToggle.querySelector('i');
                    icon.className = 'bi bi-list';
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
