<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Event & Venue Booking')</title>
    <meta name="description" content="@yield('meta_description', 'Find and book premium event venues across Bangladesh. Create events, manage bookings, and sync with Google Calendar.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:       #6366f1;
            --primary-dark:  #4f46e5;
            --primary-light: #818cf8;
            --secondary:     #ec4899;
            --accent:        #f59e0b;
            --success:       #10b981;
            --danger:        #ef4444;
            --warning:       #f59e0b;
            --info:          #3b82f6;
            --bg:            #0a0a1a;
            --bg-surface:    #12122a;
            --bg-card:       #1a1a35;
            --bg-card-hover: #22224a;
            --border:        rgba(255,255,255,0.08);
            --border-strong: rgba(255,255,255,0.15);
            --text:          #f1f5f9;
            --text-muted:    #94a3b8;
            --text-dim:      #64748b;
            --glass:         rgba(255,255,255,0.04);
            --glass-hover:   rgba(255,255,255,0.08);
            --shadow:        0 25px 50px rgba(0,0,0,0.5);
            --radius:        16px;
            --radius-sm:     10px;
            --radius-lg:     24px;
            --nav-h:         72px;
            --transition:    all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated background gradient */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% -10%, rgba(99,102,241,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(236,72,153,0.1) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        body > * { position: relative; z-index: 1; }

        /* ── NAVBAR ── */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            height: var(--nav-h);
            background: rgba(10, 10, 26, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            gap: 1.5rem;
        }

        .nav-brand {
            display: flex; align-items: center; gap: .75rem;
            text-decoration: none; flex-shrink: 0;
        }
        .nav-brand .logo-icon {
            width: 42px; height: 42px; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #fff;
            box-shadow: 0 4px 15px rgba(99,102,241,0.4);
        }
        .nav-brand .logo-text {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800; font-size: 1.15rem;
            background: linear-gradient(135deg, #fff 40%, var(--primary-light));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1.1;
        }
        .nav-brand .logo-sub { font-size: .65rem; color: var(--text-muted); font-weight: 500; display: block; }

        .nav-links {
            display: flex; align-items: center; gap: .25rem;
            list-style: none; flex: 1; padding: 0;
        }
        .nav-links a {
            display: flex; align-items: center; gap: .4rem;
            padding: .5rem .85rem; border-radius: var(--radius-sm);
            color: var(--text-muted); text-decoration: none;
            font-size: .875rem; font-weight: 500;
            transition: var(--transition);
        }
        .nav-links a:hover, .nav-links a.active {
            color: var(--text); background: var(--glass-hover);
        }
        .nav-links a.active { color: var(--primary-light); }

        .nav-actions { display: flex; align-items: center; gap: .75rem; }

        .nav-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            border: 2px solid var(--primary);
            object-fit: cover; cursor: pointer;
        }

        .dropdown { position: relative; }
        .dropdown-menu {
            position: absolute; right: 0; top: calc(100% + .75rem);
            background: var(--bg-card); border: 1px solid var(--border-strong);
            border-radius: var(--radius); padding: .5rem;
            min-width: 200px; opacity: 0; visibility: hidden;
            transform: translateY(-10px); transition: var(--transition);
            box-shadow: var(--shadow);
        }
        .dropdown:hover .dropdown-menu, .dropdown.open .dropdown-menu {
            opacity: 1; visibility: visible; transform: translateY(0);
        }
        .dropdown-menu a, .dropdown-menu button {
            display: flex; align-items: center; gap: .6rem;
            padding: .6rem .9rem; border-radius: var(--radius-sm);
            color: var(--text-muted); text-decoration: none;
            font-size: .875rem; width: 100%;
            background: none; border: none; cursor: pointer;
            transition: var(--transition);
        }
        .dropdown-menu a:hover, .dropdown-menu button:hover {
            background: var(--glass-hover); color: var(--text);
        }
        .dropdown-menu .divider { height: 1px; background: var(--border); margin: .4rem 0; }
        .dropdown-menu .danger { color: var(--danger) !important; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .65rem 1.4rem; border-radius: var(--radius-sm);
            font-size: .875rem; font-weight: 600; cursor: pointer;
            border: none; text-decoration: none; transition: var(--transition);
            white-space: nowrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 4px 15px rgba(99,102,241,0.35);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99,102,241,0.5); }
        .btn-secondary {
            background: var(--glass); color: var(--text);
            border: 1px solid var(--border-strong);
        }
        .btn-secondary:hover { background: var(--glass-hover); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { opacity: .85; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-success:hover { opacity: .85; }
        .btn-sm { padding: .45rem 1rem; font-size: .8rem; }
        .btn-lg { padding: .85rem 2rem; font-size: 1rem; border-radius: var(--radius); }
        .btn-outline {
            background: transparent; color: var(--primary-light);
            border: 1px solid var(--primary);
        }
        .btn-outline:hover { background: var(--primary); color: #fff; }

        /* ── CARDS ── */
        .card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius); overflow: hidden;
            transition: var(--transition);
        }
        .card:hover { border-color: var(--border-strong); transform: translateY(-4px); box-shadow: var(--shadow); }
        .card-body { padding: 1.5rem; }
        .card-img { width: 100%; aspect-ratio: 16/9; object-fit: cover; display: block; }

        /* ── FORMS ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; margin-bottom: .5rem; font-size: .875rem; font-weight: 500; color: var(--text-muted); }
        .form-control {
            width: 100%; padding: .75rem 1rem;
            background: var(--bg-surface); border: 1px solid var(--border);
            border-radius: var(--radius-sm); color: var(--text);
            font-size: .9rem; font-family: inherit; transition: var(--transition);
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .form-control::placeholder { color: var(--text-dim); }
        textarea.form-control { min-height: 120px; resize: vertical; }
        select.form-control { appearance: none; cursor: pointer; }
        .input-icon { position: relative; }
        .input-icon i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-dim); }
        .input-icon .form-control { padding-left: 2.75rem; }

        /* ── BADGES ── */
        .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .3rem .75rem; border-radius: 50px; font-size: .75rem; font-weight: 600; }
        .badge-primary { background: rgba(99,102,241,0.15); color: var(--primary-light); }
        .badge-success { background: rgba(16,185,129,0.15); color: #34d399; }
        .badge-danger  { background: rgba(239,68,68,0.15);  color: #f87171; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .badge-info    { background: rgba(59,130,246,0.15); color: #60a5fa; }

        /* ── ALERTS ── */
        .alert { padding: 1rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: .75rem; font-size: .9rem; }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #34d399; }
        .alert-error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.3);  color: #f87171; }
        .alert-warning { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); color: #fbbf24; }

        /* ── CONTAINERS ── */
        .container { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
        .container-sm { max-width: 760px; margin: 0 auto; padding: 0 1.5rem; }
        .section { padding: 5rem 0; }
        .section-sm { padding: 3rem 0; }

        /* ── GRIDS ── */
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }

        /* ── STAT CARDS ── */
        .stat-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 1.5rem;
            display: flex; align-items: center; gap: 1rem;
            transition: var(--transition);
        }
        .stat-card:hover { border-color: var(--border-strong); transform: translateY(-2px); }
        .stat-icon { width: 56px; height: 56px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; }
        .stat-label { color: var(--text-muted); font-size: .85rem; margin-top: .25rem; }

        /* ── TABLE ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: .85rem 1rem; text-align: left; font-size: .75rem; font-weight: 600; color: var(--text-dim); text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid var(--border); }
        td { padding: .9rem 1rem; font-size: .875rem; border-bottom: 1px solid rgba(255,255,255,0.04); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: var(--glass); }

        /* ── PAGINATION ── */
        .pagination { display: flex; align-items: center; justify-content: center; gap: .5rem; padding: 2rem 0; }
        .page-link { padding: .5rem .85rem; border-radius: var(--radius-sm); background: var(--glass); border: 1px solid var(--border); color: var(--text-muted); font-size: .875rem; text-decoration: none; transition: var(--transition); }
        .page-link:hover, .page-link.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── FOOTER ── */
        .footer {
            background: var(--bg-surface); border-top: 1px solid var(--border);
            padding: 4rem 0 2rem;
        }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 3rem; margin-bottom: 3rem; }
        .footer-brand .logo-text { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.25rem; background: linear-gradient(135deg, #fff, var(--primary-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block; margin-bottom: .75rem; }
        .footer-brand p { color: var(--text-muted); font-size: .9rem; line-height: 1.7; }
        .footer h5 { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text-dim); margin-bottom: 1.25rem; }
        .footer ul { list-style: none; }
        .footer ul li { margin-bottom: .6rem; }
        .footer ul a { color: var(--text-muted); text-decoration: none; font-size: .9rem; transition: color .2s; }
        .footer ul a:hover { color: var(--primary-light); }
        .footer-bottom { border-top: 1px solid var(--border); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; color: var(--text-dim); font-size: .85rem; }
        .social-links { display: flex; gap: .75rem; margin-top: 1.25rem; }
        .social-links a { width: 36px; height: 36px; border-radius: 50%; background: var(--glass); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); text-decoration: none; transition: var(--transition); }
        .social-links a:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── PAGE HEADER ── */
        .page-header {
            padding: 4rem 0 3rem;
            background: linear-gradient(160deg, rgba(99,102,241,0.08) 0%, transparent 60%);
            border-bottom: 1px solid var(--border);
            margin-bottom: 3rem;
        }
        .page-header h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.5rem; font-weight: 800; margin-bottom: .5rem; }
        .page-header p { color: var(--text-muted); font-size: 1.1rem; }

        /* ── UTILITIES ── */
        .text-primary  { color: var(--primary-light); }
        .text-muted    { color: var(--text-muted); }
        .text-success  { color: #34d399; }
        .text-danger   { color: #f87171; }
        .text-warning  { color: #fbbf24; }
        .text-center   { text-align: center; }
        .text-gradient { background: linear-gradient(135deg, var(--primary-light), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .d-flex   { display: flex; }
        .d-grid   { display: grid; }
        .gap-1    { gap: .5rem; }
        .gap-2    { gap: 1rem; }
        .gap-3    { gap: 1.5rem; }
        .align-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center  { justify-content: center; }
        .fw-bold  { font-weight: 700; }
        .fs-sm    { font-size: .85rem; }
        .mt-1 { margin-top: .5rem;  } .mt-2 { margin-top: 1rem;   } .mt-3 { margin-top: 1.5rem; } .mt-4 { margin-top: 2rem; }
        .mb-1 { margin-bottom: .5rem; } .mb-2 { margin-bottom: 1rem; } .mb-3 { margin-bottom: 1.5rem; } .mb-4 { margin-bottom: 2rem; }
        .p-3 { padding: 1.5rem; } .p-4 { padding: 2rem; }
        .w-full { width: 100%; }
        .rounded { border-radius: var(--radius); }

        /* ── MOBILE NAV ── */
        .nav-toggle { display: none; background: none; border: none; color: var(--text); font-size: 1.3rem; cursor: pointer; padding: .5rem; }
        .mobile-nav { display: none; }

        @media (max-width: 1024px) {
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .nav-toggle { display: block; }
            .mobile-nav { display: flex; flex-direction: column; gap: .5rem; padding: 1rem; background: var(--bg-surface); border-bottom: 1px solid var(--border); }
            .mobile-nav a { padding: .85rem 1rem; border-radius: var(--radius-sm); color: var(--text-muted); text-decoration: none; font-size: .9rem; transition: var(--transition); }
            .mobile-nav a:hover { background: var(--glass-hover); color: var(--text); }
            .grid-3 { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
            .grid-4 { grid-template-columns: 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
            .page-header h1 { font-size: 1.75rem; }
            .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('home') }}" class="nav-brand">
            <div class="logo-icon"><i class="fas fa-calendar-star"></i></div>
            <div class="logo-text">
                EventVenue
                <span class="logo-sub">Smart Booking Platform</span>
            </div>
        </a>

        <ul class="nav-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-house"></i> Home</a></li>
            <li><a href="{{ route('venues.index') }}" class="{{ request()->routeIs('venues.*') ? 'active' : '' }}"><i class="fas fa-building"></i> Venues</a></li>
            <li><a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'active' : '' }}"><i class="fas fa-calendar-days"></i> Events</a></li>
            @auth
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fas fa-gauge"></i> Dashboard</a></li>
                <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}"><i class="fas fa-ticket"></i> My Bookings</a></li>
            @endauth
        </ul>

        <div class="nav-actions">
            @auth
                <div class="dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="nav-avatar">
                    <div class="dropdown-menu">
                        <div style="padding:.5rem .9rem .75rem; border-bottom:1px solid var(--border); margin-bottom:.25rem;">
                            <div style="font-weight:600;font-size:.9rem;">{{ auth()->user()->name }}</div>
                            <div style="color:var(--text-muted);font-size:.8rem;">{{ auth()->user()->email }}</div>
                        </div>
                        <a href="{{ route('dashboard') }}"><i class="fas fa-gauge"></i> Dashboard</a>
                        <a href="{{ route('bookings.index') }}"><i class="fas fa-ticket"></i> My Bookings</a>
                        <a href="{{ route('events.my') }}"><i class="fas fa-calendar-plus"></i> My Events</a>
                        <a href="{{ route('venues.my') }}"><i class="fas fa-building"></i> My Venues</a>
                        <a href="{{ route('support.index') }}"><i class="fas fa-headset"></i> Support</a>
                        @if(auth()->user()->isAdmin())
                            <div class="divider"></div>
                            <a href="{{ route('admin.dashboard') }}" style="color:var(--warning)"><i class="fas fa-shield-halved"></i> Admin Panel</a>
                            <a href="{{ route('admin.support.index') }}" style="color:var(--warning)"><i class="fas fa-inbox"></i> Support Messages</a>
                        @endif
                        <div class="divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="danger"><i class="fas fa-right-from-bracket"></i> Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a>
            @endauth
            <button class="nav-toggle" onclick="toggleMobileNav()"><i class="fas fa-bars"></i></button>
        </div>
    </nav>

    <div class="mobile-nav" id="mobileNav" style="display:none;">
        <a href="{{ route('home') }}"><i class="fas fa-house"></i> Home</a>
        <a href="{{ route('venues.index') }}"><i class="fas fa-building"></i> Venues</a>
        <a href="{{ route('events.index') }}"><i class="fas fa-calendar-days"></i> Events</a>
        @auth
            <a href="{{ route('dashboard') }}"><i class="fas fa-gauge"></i> Dashboard</a>
            <a href="{{ route('bookings.index') }}"><i class="fas fa-ticket"></i> My Bookings</a>
            <a href="{{ route('support.index') }}"><i class="fas fa-headset"></i> Support</a>
        @else
            <a href="{{ route('login') }}"><i class="fas fa-right-to-bracket"></i> Login</a>
            <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Register</a>
        @endauth
    </div>

    @if(session('success'))
        <div style="position:fixed;top:80px;right:1.5rem;z-index:9999;animation:slideIn .4s ease;">
            <div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="position:fixed;top:80px;right:1.5rem;z-index:9999;animation:slideIn .4s ease;">
            <div class="alert alert-error"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo-text">EventVenue</div>
                    <p>Bangladesh's premier smart event and venue booking platform. Find, book, and manage your perfect event space with Google integration.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div>
                    <h5>Platform</h5>
                    <ul>
                        <li><a href="{{ route('venues.index') }}">Browse Venues</a></li>
                        <li><a href="{{ route('events.index') }}">Upcoming Events</a></li>
                        @auth
                            <li><a href="{{ route('venues.create') }}">List Your Venue</a></li>
                            <li><a href="{{ route('events.create') }}">Create Event</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h5>Account</h5>
                    <ul>
                        @auth
                            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Sign In</a></li>
                            <li><a href="{{ route('register') }}">Create Account</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h5>Contact</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-envelope fa-fw"></i> support@eventvenue.bd</a></li>
                        <li><a href="#"><i class="fas fa-phone fa-fw"></i> +880 1XXX-XXXXXX</a></li>
                        <li><a href="#"><i class="fas fa-location-dot fa-fw"></i> Dhaka, Bangladesh</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} EventVenue. All rights reserved.</span>
                <span>Built with <i class="fas fa-heart" style="color:var(--secondary)"></i> using Laravel & Google APIs</span>
            </div>
        </div>
    </footer>

    <style>
        @keyframes slideIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
    </style>
    <script>
        function toggleMobileNav() {
            const nav = document.getElementById('mobileNav');
            nav.style.display = nav.style.display === 'none' ? 'flex' : 'none';
        }
        // Auto-hide flash messages
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                el.closest('div[style*="fixed"]')?.remove();
            });
        }, 4000);
    </script>
    @stack('scripts')
</body>
</html>
