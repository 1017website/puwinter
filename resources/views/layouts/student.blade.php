<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Puwinter' }} — Belajar UTBK Lebih Cerdas</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --primary-light: #EFF6FF;
            --sidebar-w: 200px;
            --sidebar-bg: #0F172A;
            --topbar-h: 64px;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border: #E2E8F0;
            --bg: #F8FAFC;
            --card: #FFFFFF;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --premium: #F59E0B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        /* ------------------------------------------------------------------ */
        /* SIDEBAR                                                              */
        /* ------------------------------------------------------------------ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 50;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 20px 16px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-logo a {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-text {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
        }

        .logo-sub {
            font-size: 10px;
            font-weight: 600;
            color: var(--primary);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            padding: 12px 8px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #94A3B8;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 2px;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.07);
            color: #fff;
        }

        .nav-item.active {
            background: var(--primary);
            color: #fff;
        }

        .nav-item i {
            width: 18px;
            text-align: center;
            font-size: 14px;
        }

        /* Premium block */
        .sidebar-premium {
            margin: 8px;
            background: linear-gradient(135deg, #1D4ED8 0%, #7C3AED 100%);
            border-radius: 10px;
            padding: 14px;
        }

        .sidebar-premium .badge-crown {
            font-size: 20px;
            margin-bottom: 6px;
        }

        .sidebar-premium .upgrade-title {
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-premium .upgrade-sub {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.7);
            margin: 2px 0 8px;
        }

        .sidebar-premium ul {
            list-style: none;
            margin-bottom: 10px;
        }

        .sidebar-premium ul li {
            font-size: 10.5px;
            color: rgba(255, 255, 255, 0.85);
            padding: 2px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sidebar-premium ul li::before {
            content: '✓';
            color: #86EFAC;
            font-weight: 700;
        }

        .btn-upgrade {
            display: block;
            width: 100%;
            padding: 7px;
            background: #fff;
            color: #1D4ED8;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: opacity 0.15s;
        }

        .btn-upgrade:hover {
            opacity: 0.9;
        }

        /* ------------------------------------------------------------------ */
        /* MAIN CONTENT                                                         */
        /* ------------------------------------------------------------------ */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            height: var(--topbar-h);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-greeting {
            flex: 1;
        }

        .topbar-greeting h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
        }

        .topbar-greeting p {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .topbar-search {
            position: relative;
        }

        .topbar-search input {
            width: 280px;
            padding: 8px 16px 8px 36px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            background: var(--bg);
            color: var(--text-main);
            outline: none;
            transition: border-color 0.15s;
        }

        .topbar-search input:focus {
            border-color: var(--primary);
        }

        .topbar-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-icon-btn {
            position: relative;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.15s;
        }

        .topbar-icon-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .badge-notif {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px 4px 4px;
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: border-color 0.15s;
        }

        .topbar-user:hover {
            border-color: var(--primary);
        }

        .topbar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            overflow: hidden;
        }

        .topbar-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .topbar-username {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
        }

        /* PAGE CONTENT */
        .page-content {
            padding: 28px;
            flex: 1;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
        }

        .page-header p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ------------------------------------------------------------------ */
        /* UTILITY CLASSES                                                      */
        /* ------------------------------------------------------------------ */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text-main);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-premium {
            background: linear-gradient(135deg, #F59E0B, #EF4444);
            color: #fff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-primary {
            background: var(--primary-light);
            color: var(--primary);
        }

        .badge-success {
            background: #ECFDF5;
            color: var(--success);
        }

        .badge-warning {
            background: #FFFBEB;
            color: var(--warning);
        }

        .badge-danger {
            background: #FEF2F2;
            color: var(--danger);
        }

        .badge-premium {
            background: #FEF3C7;
            color: #D97706;
        }

        .progress-bar {
            height: 6px;
            background: #E2E8F0;
            border-radius: 99px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 99px;
            transition: width 0.3s ease;
        }

        /* Alert flash messages */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #6EE7B7;
        }

        .alert-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .alert-warning {
            background: #FFFBEB;
            color: #92400E;
            border: 1px solid #FCD34D;
        }

        .alert-info {
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #93C5FD;
        }

        /* Premium lock overlay */
        .lock-overlay {
            position: relative;
        }

        .lock-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(2px);
            border-radius: inherit;
            z-index: 10;
        }

        .lock-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 11;
            background: var(--sidebar-bg);
            color: #fff;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        /* Stats row */
        .stats-row {
            display: grid;
            gap: 16px;
        }

        .stats-row.cols-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        .stats-row.cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .stats-row.cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .stat-icon.blue {
            background: #EFF6FF;
            color: var(--primary);
        }

        .stat-icon.green {
            background: #ECFDF5;
            color: var(--success);
        }

        .stat-icon.yellow {
            background: #FFFBEB;
            color: var(--warning);
        }

        .stat-icon.purple {
            background: #F5F3FF;
            color: #7C3AED;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-row.cols-4 {
                grid-template-columns: repeat(2, 1fr);
            }

            .topbar-search input {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .stats-row.cols-4,
            .stats-row.cols-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- ======================================================================== --}}
    {{-- SIDEBAR --}}
    {{-- ======================================================================== --}}
    <aside class="sidebar">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <a href="{{ route('dashboard') }}">
                <div class="logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" fill="#fff" />
                    </svg>
                </div>
                <div>
                    <div class="logo-text">Puwinter</div>
                    <div class="logo-sub">UTBK</div>
                </div>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="{{ route('student.course.index') }}"
                class="nav-item {{ request()->routeIs('student.course.*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i> Kelas Saya
            </a>
            <a href="{{ route('student.tryout.index') }}"
                class="nav-item {{ request()->routeIs('student.tryout.*') ? 'active' : '' }}">
                <i class="fas fa-bullseye"></i> Tryout
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('student.bank.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i> Bank Soal
            </a>
            <a href="{{ route('student.live.index') }}"
                class="nav-item {{ request()->routeIs('student.live.*') ? 'active' : '' }}">
                <i class="fas fa-video"></i> Live Class
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('student.pdf.*') ? 'active' : '' }}">
                <i class="fas fa-file-pdf"></i> Materi PDF
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('student.pembahasan.*') ? 'active' : '' }}">
                <i class="fas fa-lightbulb"></i> Pembahasan
            </a>
            <a href="{{ route('student.leaderboard.index') }}"
                class="nav-item {{ request()->routeIs('student.leaderboard.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Leaderboard
            </a>
            <a href="{{ route('student.history.index') }}"
                class="nav-item {{ request()->routeIs('student.history.*') ? 'active' : '' }}">
                <i class="fas fa-clock-rotate-left"></i> Riwayat
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('student.settings.*') ? 'active' : '' }}">
                <i class="fas fa-gear"></i> Pengaturan
            </a>
        </nav>

        {{-- Premium block (hanya untuk non-premium) --}}
        @auth
            @if(!auth()->user()->isPremium())
                <div class="sidebar-premium">
                    <div class="badge-crown">👑</div>
                    <div class="upgrade-title">Upgrade ke Premium</div>
                    <div class="upgrade-sub">Buka semua fitur belajar</div>
                    <ul>
                        <li>Akses semua live class</li>
                        <li>Rekaman tanpa batas</li>
                        <li>Materi premium</li>
                        <li>Tanya tutor prioritas</li>
                        <li>Sertifikat kehadiran</li>
                    </ul>
                    <a href="{{ route('upgrade.index') }}" class="btn-upgrade">Upgrade Sekarang</a>
                </div>
            @endif
        @endauth

        <div style="height: 16px;"></div>
    </aside>

    {{-- ======================================================================== --}}
    {{-- MAIN WRAPPER --}}
    {{-- ======================================================================== --}}
    <div class="main-wrapper">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="topbar-greeting">
                <h1>Halo, {{ auth()->user()->name ?? 'Pelajar' }}! 👋</h1>
                <p>{{ $subtitle ?? 'Semangat belajar hari ini!' }}</p>
            </div>

            <div class="topbar-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="{{ $searchPlaceholder ?? 'Cari kelas, materi, atau mentor...' }}">
            </div>

            <div class="topbar-actions">
                {{-- Notifikasi --}}
                <a href="#" class="topbar-icon-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge-notif">3</span>
                </a>

                {{-- Chat --}}
                <a href="#" class="topbar-icon-btn">
                    <i class="fas fa-comment-dots"></i>
                </a>

                {{-- User --}}
                <div style="position:relative;" x-data="{ open: false }">
                    <div class="topbar-user" @click="open = !open" style="cursor:pointer;">
                        <div class="topbar-avatar">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <span class="topbar-username">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px; color: var(--text-muted);"></i>
                    </div>

                    {{-- Dropdown --}}
                    <div x-show="open" @click.outside="open = false"
                        style="position:absolute; right:0; top:calc(100% + 8px); background:#fff; border:1px solid var(--border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.1); min-width:180px; z-index:999; overflow:hidden;">
                        <a href="#"
                            style="display:flex; align-items:center; gap:10px; padding:11px 16px; font-size:13px; color:var(--text-main); text-decoration:none; transition:background 0.1s;"
                            onmouseover="this.style.background='#F8FAFC'"
                            onmouseout="this.style.background='transparent'">
                            <i class="fas fa-user" style="width:16px; color:var(--text-muted);"></i> Profil Saya
                        </a>
                        <a href="#"
                            style="display:flex; align-items:center; gap:10px; padding:11px 16px; font-size:13px; color:var(--text-main); text-decoration:none; transition:background 0.1s;"
                            onmouseover="this.style.background='#F8FAFC'"
                            onmouseout="this.style.background='transparent'">
                            <i class="fas fa-gear" style="width:16px; color:var(--text-muted);"></i> Pengaturan
                        </a>
                        <div style="height:1px; background:var(--border); margin:4px 0;"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                style="display:flex; align-items:center; gap:10px; padding:11px 16px; font-size:13px; color:#EF4444; background:transparent; border:none; width:100%; cursor:pointer; font-family:inherit; transition:background 0.1s;"
                                onmouseover="this.style.background='#FEF2F2'"
                                onmouseout="this.style.background='transparent'">
                                <i class="fas fa-arrow-right-from-bracket" style="width:16px;"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- FLASH MESSAGES --}}
        <div style="padding: 0 28px; margin-top: 16px;">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-circle-xmark"></i> {{ session('error') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fas fa-triangle-exclamation"></i> {{ session('warning') }}
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">
                    <i class="fas fa-circle-info"></i> {{ session('info') }}
                </div>
            @endif
        </div>

        {{-- PAGE CONTENT --}}
        <main class="page-content">
            @yield('content')
        </main>

    </div>

    @stack('scripts')
</body>

</html>