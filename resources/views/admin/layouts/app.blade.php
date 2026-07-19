<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <title>{{ $title ?? 'Admin' }} — Puwinter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary:    #2563EB;
            --primary-d:  #1D4ED8;
            --sidebar-bg: #1E293B;
            --sidebar-w:  220px;
            --topbar-h:   60px;
            --border:     #E2E8F0;
            --bg:         #F1F5F9;
            --card:       #FFFFFF;
            --text:       #1E293B;
            --muted:      #64748B;
            --success:    #10B981;
            --warning:    #F59E0B;
            --danger:     #EF4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .admin-sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 50;
        }

        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            overscroll-behavior: contain;
            scrollbar-gutter: stable;
            scrollbar-width: thin;
            scrollbar-color: #475569 rgba(15,23,42,0.28);
        }

        .sidebar-scroll::-webkit-scrollbar { width: 7px; }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(15,23,42,0.28);
            border-radius: 999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #475569;
            border: 1px solid rgba(15,23,42,0.45);
            border-radius: 999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #64748B; }
        .sidebar-scroll::-webkit-scrollbar-button { display: none; width: 0; height: 0; }

        .sidebar-logo {
            padding: 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-logo-icon {
            width: 34px; height: 34px;
            background: var(--primary);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .sidebar-logo-text { font-size: 14px; font-weight: 800; color: #fff; }
        .sidebar-logo-badge {
            font-size: 9px;
            background: rgba(239,68,68,0.2);
            color: #FCA5A5;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-section {
            padding: 16px 10px 8px;
        }

        .sidebar-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            padding: 0 8px;
            margin-bottom: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 8px;
            color: #94A3B8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 2px;
        }

        .nav-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .nav-item.active { background: var(--primary); color: #fff; }
        .nav-item i { width: 16px; text-align: center; font-size: 13px; }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 99px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        /* MAIN */
        .admin-main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* TOPBAR */
        .admin-topbar {
            height: var(--topbar-h);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 14px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-title { flex: 1; font-size: 16px; font-weight: 800; color: var(--text); }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .topbar-icon-btn {
            width: 36px; height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--muted);
            text-decoration: none;
            transition: all 0.15s;
            position: relative;
        }

        .topbar-icon-btn:hover { border-color: var(--primary); color: var(--primary); }

        .notif-dot {
            position: absolute;
            top: -3px; right: -3px;
            width: 14px; height: 14px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid var(--card);
            font-size: 8px;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px 4px 4px;
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
        }

        .topbar-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 12px; font-weight: 700;
        }

        .topbar-username { font-size: 13px; font-weight: 600; }

        /* PAGE */
        .admin-content { padding: 24px; flex: 1; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .page-header h2 { font-size: 20px; font-weight: 800; }
        .page-header p  { font-size: 13px; color: var(--muted); margin-top: 2px; }

        /* CARDS & UTILITIES */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 600; font-family: inherit;
            cursor: pointer; border: none; text-decoration: none;
            transition: all 0.15s;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-d); }
        .btn-danger  { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #DC2626; }
        .btn-outline { background: transparent; border: 1.5px solid var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* TABLE */
        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
            background: var(--bg);
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        tbody td {
            padding: 12px 14px;
            font-size: 13.5px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #F8FAFC; }

        /* BADGE */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 8px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }

        .badge-success { background: #ECFDF5; color: #059669; }
        .badge-warning { background: #FFFBEB; color: #D97706; }
        .badge-danger  { background: #FEF2F2; color: var(--danger); }
        .badge-primary { background: #EFF6FF; color: var(--primary); }
        .badge-gray    { background: #F1F5F9; color: var(--muted); }

        /* FORM */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 13.5px;
            font-family: inherit;
            color: var(--text);
            background: #fff;
            outline: none;
            transition: border-color 0.15s;
        }

        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.08); }
        .form-control.is-invalid { border-color: var(--danger); }

        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }

        .invalid-feedback { font-size: 12px; color: var(--danger); margin-top: 4px; }

        /* STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex; align-items: center; gap: 14px;
        }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        .stat-value { font-size: 22px; font-weight: 800; color: var(--text); line-height: 1; }
        .stat-label { font-size: 12px; color: var(--muted); margin-top: 3px; }

        /* PAGINATION */
        .pagination {
            display: flex; align-items: center; gap: 4px;
            padding: 16px 0 0;
            justify-content: flex-end;
        }

        .pagination a, .pagination span {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: var(--muted);
            border: 1px solid var(--border);
        }

        .pagination .active span {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* ALERT */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; }
        .alert-error   { background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; }
        .alert-warning { background: #FFFBEB; color: #92400E; border: 1px solid #FCD34D; }

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2,1fr); }
        }

        /* ------------------------------------------------------------------ */
        /* MOBILE ADMIN                                                         */
        /* ------------------------------------------------------------------ */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                z-index: 200;
            }
            .admin-sidebar.open { transform: translateX(0); }

            .admin-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.55);
                z-index: 199;
            }
            .admin-overlay.open { display: block; }

            .admin-main { margin-left: 0 !important; }

            .admin-hamburger {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 36px; height: 36px;
                border-radius: 7px;
                border: 1px solid var(--border);
                background: var(--bg);
                cursor: pointer;
                color: var(--muted);
                flex-shrink: 0;
            }

            .topbar-title { font-size: 14px !important; }
            .stats-grid   { grid-template-columns: repeat(2,1fr) !important; }
            .page-header  { flex-direction: column; align-items: flex-start !important; gap: 10px; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr !important; }
        }

        .admin-hamburger { display: none; }
    </style>

    @stack('styles')
</head>
<body>

{{-- Mobile overlay --}}
<div class="admin-overlay" id="admin-overlay" onclick="closeAdminSidebar()"></div>

{{-- SIDEBAR --}}
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-logo" style="flex-direction:column; align-items:center; padding:12px 16px; gap:6px;">
        <a href="{{ route('admin.dashboard') }}" style="display:flex; align-items:center; justify-content:center; text-decoration:none; width:100%;">
            <img src="{{ asset('images/logo.png') }}" alt="Puwinter"
                 style="width:130px; height:auto; object-fit:contain; filter:brightness(0) invert(1);">
        </a>
        <span class="sidebar-logo-badge">ADMIN</span>
    </div>

    <div class="sidebar-scroll" style="padding-bottom:8px;">

        <div class="sidebar-section">
            <div class="sidebar-section-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Manajemen</div>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> User
            </a>
            <a href="{{ route('admin.registration-codes.index') }}" class="nav-item {{ request()->routeIs('admin.registration-codes.*') ? 'active' : '' }}">
                <i class="fas fa-ticket"></i> Kode Pendaftar
            </a>
            <a href="{{ route('admin.courses.index') }}" class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i> Kelas & Materi
            </a>
            <a href="{{ route('admin.tryouts.index') }}" class="nav-item {{ request()->routeIs('admin.tryouts.*') ? 'active' : '' }}">
                <i class="fas fa-bullseye"></i> Tryout
            </a>
            <a href="{{ route('admin.tryout-results.index') }}" class="nav-item {{ request()->routeIs('admin.tryout-results.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> Hasil Tryout
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" class="nav-item {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i> Langganan
                @php $pendingCount = \App\Models\Subscription::where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="nav-badge">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.grade-requests.index') }}" class="nav-item {{ request()->routeIs('admin.grade-requests.*') ? 'active' : '' }}">
                <i class="fas fa-people-arrows"></i> Pindah Kelas
                @php $gradeReqCount = \App\Models\GradeChangeRequest::where('status','pending')->count(); @endphp
                @if($gradeReqCount > 0)
                    <span class="nav-badge">{{ $gradeReqCount }}</span>
                @endif
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Konten</div>
            <a href="{{ route('admin.subjects.index') }}" class="nav-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                <i class="fas fa-tag"></i> Mata Pelajaran
            </a>
            <a href="{{ route('admin.grades.index') }}" class="nav-item {{ request()->routeIs('admin.grades.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> Master Kelas
            </a>
            <a href="{{ route('admin.live-classes.index') }}" class="nav-item {{ request()->routeIs('admin.live-classes.*') ? 'active' : '' }}">
                <i class="fas fa-video"></i> Kelas Online
            </a>
            <a href="{{ route('admin.plans.index') }}" class="nav-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Program
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Sistem</div>
            <a href="{{ route('admin.email-logs.index') }}" class="nav-item {{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}">
                <i class="fas fa-envelope-circle-check"></i> Log Email
                @php
                    $failedEmailCount = 0;
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('email_logs')) {
                            $failedEmailCount = \App\Models\EmailLog::where('status', 'failed')->count();
                        }
                    } catch (\Throwable $e) {
                        $failedEmailCount = 0;
                    }
                @endphp
                @if($failedEmailCount > 0)
                    <span class="nav-badge">{{ $failedEmailCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.visitors.index') }}" class="nav-item {{ request()->routeIs('admin.visitors.*') ? 'active' : '' }}">
                <i class="fas fa-chart-column"></i> Visitor
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-gear"></i> Pengaturan
            </a>
        </div>

    </div>

    <div class="sidebar-footer">
        <div style="display:flex; align-items:center; gap:8px; padding:8px; border-radius:8px; background:rgba(255,255,255,0.04);">
            <div style="width:32px; height:32px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:700; flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:12px; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div style="font-size:10px; color:#475569; text-transform:capitalize;">{{ auth()->user()->role }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:transparent; border:none; cursor:pointer; color:#475569; font-size:13px;" title="Logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="admin-main">

    {{-- TOPBAR --}}
    <header class="admin-topbar">
        <button class="admin-hamburger" onclick="openAdminSidebar()" aria-label="Menu">
            <i class="fas fa-bars" style="font-size:15px;"></i>
        </button>
        <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>

        <div class="topbar-actions">
            <a href="{{ route('dashboard') }}" class="topbar-icon-btn" title="Lihat sebagai student">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('notifications.index') }}" class="topbar-icon-btn" title="Notifikasi">
                <i class="fas fa-bell"></i>
                @if(isset($notifCount) && $notifCount > 0)
                    <span class="notif-dot">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                @endif
            </a>
            <div class="topbar-user">
                <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="topbar-username">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </header>

    {{-- FLASH --}}
    <div style="padding: 0 24px; margin-top:16px;">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning"><i class="fas fa-triangle-exclamation"></i> {{ session('warning') }}</div>
        @endif
    </div>

    {{-- CONTENT --}}
    <main class="admin-content">
        @yield('content')
    </main>

</div>

@stack('scripts')
<script>
function openAdminSidebar() {
    document.getElementById('admin-sidebar').classList.add('open');
    document.getElementById('admin-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeAdminSidebar() {
    document.getElementById('admin-sidebar').classList.remove('open');
    document.getElementById('admin-overlay').classList.remove('open');
    document.body.style.overflow = '';
}
document.querySelectorAll('.admin-sidebar .nav-item').forEach(el => {
    el.addEventListener('click', () => {
        if (window.innerWidth <= 768) closeAdminSidebar();
    });
});
</script>
</body>
</html>
