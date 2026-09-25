<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ANotes - Notes Manager')</title>
    <meta name="theme-color" content="#1f6b4f">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@auth app-auth @else app-guest @endauth">
    @auth
        <div class="app-shell">
            <aside class="app-sidebar" id="appSidebar" aria-label="Main navigation">
                <div class="sidebar-inner">
                    <a class="app-brand" href="{{ route('home') }}" aria-label="ANotes home">
                        <span class="brand-mark">A</span>
                        <span>ANotes</span>
                    </a>

                    <nav class="sidebar-nav">
                        <p class="sidebar-label">Workspace</p>
                        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span>
                        </a>
                        <a class="sidebar-link {{ request()->routeIs('notes.*') ? 'active' : '' }}" href="{{ route('notes.index') }}">
                            <i class="bi bi-journal-text" aria-hidden="true"></i><span>Notes</span>
                        </a>
                        <a class="sidebar-link {{ request()->routeIs('reminders.*') ? 'active' : '' }}" href="{{ route('reminders.index') }}">
                            <i class="bi bi-calendar2-check" aria-hidden="true"></i><span>Reminders</span>
                        </a>
                        <a class="sidebar-link {{ request()->routeIs('trash.*') ? 'active' : '' }}" href="{{ route('trash.index') }}">
                            <i class="bi bi-trash3" aria-hidden="true"></i><span>Trash</span>
                        </a>

                        <p class="sidebar-label sidebar-label-spaced">Support</p>
                        <a class="sidebar-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            <i class="bi bi-chat-square-text" aria-hidden="true"></i><span>Contact</span>
                        </a>
                    </nav>

                    <div class="sidebar-user">
                        <div class="user-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="user-copy">
                            <strong>{{ auth()->user()->name }}</strong>
                            <span>{{ auth()->user()->email }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="icon-button" aria-label="Log out" title="Log out">
                                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="app-sidebar-backdrop" data-sidebar-close></div>
            <section class="app-main-panel">
                <header class="app-topbar">
                    <button type="button" class="mobile-menu-button" data-sidebar-toggle aria-controls="appSidebar" aria-expanded="false" aria-label="Open navigation">
                        <i class="bi bi-list" aria-hidden="true"></i>
                    </button>
                    <div>
                        <p class="topbar-kicker">Your workspace</p>
                        <h1 class="topbar-title">{{ auth()->user()->name }}'s ANotes</h1>
                    </div>
                    <div class="topbar-tools">
                        <button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false" aria-label="Enable dark theme" title="Enable dark theme">
                            <i class="bi bi-moon-stars" aria-hidden="true"></i>
                        </button>
                        <div class="topbar-date"><i class="bi bi-sun me-2" aria-hidden="true"></i>{{ now()->format('l, M j') }}</div>
                    </div>
                </header>
                <main class="app-content">
                    @yield('content')
                </main>
                <footer class="app-footer">&copy; {{ date('Y') }} ANotes <span>Made for clearer thinking.</span></footer>
            </section>
        </div>
    @else
        <div class="guest-shell">
            <header class="guest-header">
                <a class="app-brand" href="{{ route('home') }}" aria-label="ANotes home">
                    <span class="brand-mark">A</span>
                    <span>ANotes</span>
                </a>
                <nav class="guest-nav" aria-label="Public navigation">
                    <a class="guest-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    <a class="guest-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                    <a class="guest-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">Services</a>
                    <a class="guest-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                </nav>
                <div class="guest-actions">
                    <button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false" aria-label="Enable dark theme" title="Enable dark theme">
                        <i class="bi bi-moon-stars" aria-hidden="true"></i>
                    </button>
                    <a class="guest-login" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Create account</a>
                </div>
            </header>
            <main class="guest-main">@yield('content')</main>
            <footer class="guest-footer">&copy; {{ date('Y') }} ANotes <span>Made for clearer thinking.</span></footer>
        </div>
    @endauth

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
