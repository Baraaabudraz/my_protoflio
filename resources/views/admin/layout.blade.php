<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', __('Dashboard')) — CMS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('admin-theme'); } catch (e) {}
            var theme = saved === 'light' || saved === 'dark' ? saved : (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ═════════ TOKENS ═════════ */
        :root, [data-theme="dark"] {
            --cyan: #22d3ee;
            --cyan-strong: #06b6d4;
            --cyan-dim: rgba(34,211,238,0.12);
            --cyan-line: rgba(34,211,238,0.28);
            --on-accent: #04121a;
            --bg: #0b1020;
            --bg2: #0f1629;
            --bg3: #141d33;
            --card: #111a2e;
            --card-hover: #15203a;
            --border: #1f2b45;
            --text: #e6ebf5;
            --text-2: #aab4c8;
            --muted: #7c89a3;
            --danger: #f87171;
            --danger-dim: rgba(248,113,113,0.12);
            --success: #34d399;
            --success-dim: rgba(52,211,153,0.12);
            --warning: #fbbf24;
            --warning-dim: rgba(251,191,36,0.14);
            --shadow: 0 1px 2px rgba(0,0,0,.3), 0 8px 24px rgba(0,0,0,.25);
            --shadow-lg: 0 24px 60px rgba(0,0,0,.45);
            --gradient: linear-gradient(135deg, #22d3ee, #3b82f6);
        }
        [data-theme="light"] {
            --cyan: #0e7490;
            --cyan-strong: #0891b2;
            --cyan-dim: rgba(8,145,178,0.09);
            --cyan-line: rgba(8,145,178,0.25);
            --on-accent: #ffffff;
            --bg: #f4f6fb;
            --bg2: #ffffff;
            --bg3: #eef2f8;
            --card: #ffffff;
            --card-hover: #f8fafd;
            --border: #e3e8f0;
            --text: #111827;
            --text-2: #374151;
            --muted: #5b6678;
            --danger: #dc2626;
            --danger-dim: rgba(220,38,38,0.08);
            --success: #059669;
            --success-dim: rgba(5,150,105,0.1);
            --warning: #b45309;
            --warning-dim: rgba(217,119,6,0.12);
            --shadow: 0 1px 2px rgba(15,23,42,.04), 0 6px 18px rgba(15,23,42,.06);
            --shadow-lg: 0 24px 60px rgba(15,23,42,.14);
            --gradient: linear-gradient(135deg, #0891b2, #2563eb);
        }
        :root {
            --font-body: {!! $isRtl ? "'IBM Plex Sans Arabic', 'Inter', sans-serif" : "'Inter', sans-serif" !!};
            --font-head: {!! $isRtl ? "'IBM Plex Sans Arabic', sans-serif" : "'Plus Jakarta Sans', 'Inter', sans-serif" !!};
            --font-ar: 'IBM Plex Sans Arabic', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --sidebar-w: 264px;
            --radius: 16px;
            --radius-sm: 10px;
        }

        /* ═════════ BASE ═════════ */
        * { margin:0; padding:0; box-sizing:border-box; }
        html { -webkit-text-size-adjust:100%; }
        body { font-family:var(--font-body); font-size:15px; line-height:1.55; background:var(--bg); color:var(--text); min-height:100vh; -webkit-font-smoothing:antialiased; transition:background .25s, color .25s; }
        a { color:inherit; }
        ::selection { background:var(--cyan); color:var(--on-accent); }
        :where(a, button, input, select, textarea, summary):focus-visible { outline:2px solid var(--cyan); outline-offset:2px; }
        input[dir="rtl"], textarea[dir="rtl"], .ar-input, .bi-panel[data-lang="ar"] .form-control { font-family:var(--font-ar) !important; }

        /* ═════════ SIDEBAR ═════════ */
        .sidebar { width:var(--sidebar-w); background:var(--bg2); border-inline-end:1px solid var(--border); display:flex; flex-direction:column; position:fixed; top:0; bottom:0; inset-inline-start:0; z-index:200; transition:transform .3s cubic-bezier(.2,.7,.2,1); }
        .sidebar-logo { display:flex; align-items:center; gap:.75rem; padding:1.25rem 1.25rem 1rem; text-decoration:none; }
        .sidebar-logo img { width:42px; height:42px; border-radius:12px; object-fit:cover; object-position:50% 22%; border:2px solid var(--cyan-line); }
        .sidebar-logo strong { display:block; font:700 .98rem var(--font-head); color:var(--text); line-height:1.25; }
        .sidebar-logo small { display:block; font-size:.75rem; color:var(--muted); }
        .sidebar-nav { flex:1; padding:.5rem .75rem 1rem; overflow-y:auto; }
        .nav-section { padding:1rem .75rem .45rem; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; color:var(--muted); }
        [dir="rtl"] .nav-section { letter-spacing:0; }
        .nav-item { display:flex; align-items:center; gap:.75rem; padding:.62rem .75rem; margin-bottom:.15rem; color:var(--text-2); text-decoration:none; font-size:.9rem; font-weight:500; border-radius:var(--radius-sm); transition:background .2s, color .2s; position:relative; }
        .nav-item i { width:18px; text-align:center; font-size:.9rem; color:var(--muted); transition:color .2s; }
        .nav-item:hover { background:var(--bg3); color:var(--text); }
        .nav-item:hover i { color:var(--text); }
        .nav-item.active { background:var(--cyan-dim); color:var(--cyan); font-weight:600; }
        .nav-item.active i { color:var(--cyan); }
        .nav-item.active::before { content:''; position:absolute; inset-inline-start:-.75rem; top:20%; bottom:20%; width:3px; border-radius:0 3px 3px 0; background:var(--cyan); }
        [dir="rtl"] .nav-item.active::before { border-radius:3px 0 0 3px; }
        .nav-item .ext { margin-inline-start:auto; font-size:.7rem; opacity:.6; }
        .nav-count { margin-inline-start:auto; min-width:22px; height:22px; padding:0 .45rem; border-radius:999px; background:var(--gradient); color:var(--on-accent); font-size:.72rem; font-weight:700; display:inline-flex; align-items:center; justify-content:center; }
        .sidebar-footer { padding:.9rem; border-top:1px solid var(--border); display:flex; gap:.5rem; }
        .logout-btn { flex:1; display:flex; align-items:center; gap:.6rem; padding:.6rem .75rem; border-radius:var(--radius-sm); background:none; border:1px solid transparent; color:var(--text-2); font:500 .88rem var(--font-body); cursor:pointer; transition:all .2s; }
        .logout-btn:hover { background:var(--danger-dim); color:var(--danger); }
        .sidebar-backdrop { display:none; }

        /* ═════════ MAIN / TOPBAR ═════════ */
        .main { margin-inline-start:var(--sidebar-w); min-height:100vh; display:flex; flex-direction:column; min-width:0; }
        .topbar { position:sticky; top:0; z-index:100; display:flex; align-items:center; gap:1rem; height:68px; padding:0 2rem; background:color-mix(in srgb, var(--bg) 82%, transparent); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border-bottom:1px solid var(--border); }
        .icon-btn.menu-btn { display:none; }
        .topbar-title { font:700 1.2rem var(--font-head); letter-spacing:-.01em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .topbar-actions { margin-inline-start:auto; display:flex; gap:.6rem; align-items:center; flex-shrink:0; }
        .icon-btn { width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center; border-radius:var(--radius-sm); border:1px solid var(--border); background:var(--card); color:var(--text-2); cursor:pointer; transition:all .2s; text-decoration:none; font-size:.9rem; }
        .icon-btn:hover { color:var(--cyan); border-color:var(--cyan-line); }
        [data-theme="dark"] .theme-light-icon, [data-theme="light"] .theme-dark-icon { display:none; }
        .content { padding:2rem; flex:1; width:100%; max-width:1320px; }

        /* ═════════ LANG SWITCH ═════════ */
        .lang-switch { display:flex; align-items:center; gap:.15rem; background:var(--bg3); border:1px solid var(--border); border-radius:999px; padding:.2rem; }
        .lang-btn { padding:.3rem .65rem; border-radius:999px; font-size:.75rem; font-weight:700; text-decoration:none; color:var(--muted); transition:all .2s; white-space:nowrap; }
        .lang-btn.active { background:var(--gradient); color:var(--on-accent); }
        .lang-btn:not(.active):hover { color:var(--cyan); }

        /* ═════════ ALERTS ═════════ */
        .alert { padding:.85rem 1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem; font-size:.9rem; display:flex; align-items:flex-start; gap:.6rem; border:1px solid; }
        .alert i { margin-top:.2rem; }
        .alert-success { background:var(--success-dim); border-color:color-mix(in srgb, var(--success) 35%, transparent); color:var(--success); }
        .alert-error { background:var(--danger-dim); border-color:color-mix(in srgb, var(--danger) 35%, transparent); color:var(--danger); flex-direction:column; gap:.25rem; }
        .toast { position:fixed; bottom:1.5rem; inset-inline-end:1.5rem; z-index:500; margin:0; box-shadow:var(--shadow-lg); background:var(--card); animation:toast-in .35s cubic-bezier(.2,.7,.2,1); max-width:min(420px, calc(100vw - 2rem)); }
        .toast.hide { opacity:0; transform:translateY(10px); transition:all .3s; }
        @keyframes toast-in { from { opacity:0; transform:translateY(14px); } }

        /* ═════════ CARDS ═════════ */
        .card { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
        .card-header { padding:1.1rem 1.4rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
        .card-title { font:700 1rem var(--font-head); display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
        .card-body { padding:1.4rem; }

        /* ═════════ TABLE ═════════ */
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:start; padding:.8rem 1.4rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--muted); background:var(--bg3); border-bottom:1px solid var(--border); white-space:nowrap; }
        [dir="rtl"] th { letter-spacing:0; }
        td { padding:1rem 1.4rem; font-size:.9rem; border-bottom:1px solid var(--border); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tbody tr { transition:background .15s; }
        tbody tr:hover td { background:var(--card-hover); }

        /* ═════════ BUTTONS ═════════ */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; min-height:40px; padding:0 1.05rem; border-radius:var(--radius-sm); font:600 .88rem var(--font-body); cursor:pointer; border:1px solid transparent; text-decoration:none; transition:all .2s; white-space:nowrap; }
        .btn-primary { background:var(--gradient); color:var(--on-accent); box-shadow:0 6px 16px -6px color-mix(in srgb, var(--cyan-strong) 70%, transparent); }
        .btn-primary:hover { filter:brightness(1.07); transform:translateY(-1px); }
        .btn-secondary { background:var(--card); color:var(--text); border-color:var(--border); }
        .btn-secondary:hover { border-color:var(--cyan-line); color:var(--cyan); }
        .btn-danger { background:var(--danger-dim); color:var(--danger); border-color:color-mix(in srgb, var(--danger) 25%, transparent); }
        .btn-danger:hover { background:var(--danger); color:#fff; }
        .btn-view { background:var(--cyan-dim); color:var(--cyan); border-color:var(--cyan-line); }
        .btn-view:hover { background:var(--cyan); color:var(--on-accent); }
        .btn-sm { min-height:34px; padding:0 .75rem; font-size:.82rem; }
        td .btn-sm { min-width:34px; }

        /* ═════════ FORMS ═════════ */
        .form-group { margin-bottom:1.2rem; }
        .form-label { display:block; font-size:.82rem; font-weight:600; color:var(--text-2); margin-bottom:.45rem; }
        .form-control { width:100%; min-height:44px; padding:.65rem .9rem; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius-sm); color:var(--text); font:inherit; font-size:.92rem; outline:none; transition:border-color .2s, box-shadow .2s, background .2s; }
        .form-control::placeholder { color:var(--muted); opacity:.8; }
        .form-control:hover { border-color:color-mix(in srgb, var(--cyan) 30%, var(--border)); }
        .form-control:focus { border-color:var(--cyan); box-shadow:0 0 0 4px var(--cyan-dim); background:var(--card); }
        textarea.form-control { min-height:120px; resize:vertical; line-height:1.65; }
        select.form-control { cursor:pointer; }
        input[type="file"].form-control { padding:.55rem .6rem; cursor:pointer; }
        input[type="file"].form-control::file-selector-button { margin-inline-end:.75rem; padding:.4rem .8rem; border-radius:8px; border:1px solid var(--border); background:var(--bg3); color:var(--text); font:600 .8rem var(--font-body); cursor:pointer; }
        .form-hint { font-size:.8rem; color:var(--muted); margin-top:.4rem; line-height:1.5; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .form-check { display:inline-flex; align-items:center; gap:.55rem; cursor:pointer; font-size:.9rem; font-weight:500; user-select:none; }
        .form-check input[type=checkbox] { width:18px; height:18px; accent-color:var(--cyan-strong); cursor:pointer; }

        /* ═════════ BADGES & TAGS ═════════ */
        .badge { display:inline-flex; align-items:center; gap:.3rem; padding:.22rem .6rem; border-radius:999px; font-size:.74rem; font-weight:600; line-height:1.4; white-space:nowrap; }
        .badge-cyan { background:var(--cyan-dim); color:var(--cyan); }
        .badge-success { background:var(--success-dim); color:var(--success); }
        .badge-muted { background:var(--bg3); color:var(--text-2); }
        .badge-warning { background:var(--warning-dim); color:var(--warning); }
        .tag-preview { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.5rem; }
        .tag-pill { padding:.22rem .65rem; background:var(--cyan-dim); border:1px solid var(--cyan-line); border-radius:999px; font:500 .74rem var(--font-mono); color:var(--cyan); }
        .section-divider { display:flex; align-items:center; gap:.75rem; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--cyan); margin:1.75rem 0 1rem; }
        .section-divider::after { content:''; flex:1; height:1px; background:var(--border); }
        [dir="rtl"] .section-divider { letter-spacing:0; }

        /* ═════════ BILINGUAL TABS (shared by forms) ═════════ */
        .bi-tabs { display:inline-flex !important; gap:.25rem !important; padding:.25rem; background:var(--bg3); border:1px solid var(--border) !important; border-radius:12px !important; margin-bottom:1.5rem; }
        .bi-tab { flex:none !important; padding:.5rem 1rem !important; border-radius:9px; background:transparent; border:none; font:600 .85rem var(--font-body) !important; color:var(--muted); cursor:pointer; transition:all .2s; display:flex; align-items:center; gap:.4rem; }
        .bi-tab.active { background:var(--card) !important; color:var(--text) !important; box-shadow:var(--shadow); }
        .bi-tab:not(.active):hover { color:var(--text) !important; background:transparent !important; }
        .lang-badge { display:inline-block; padding:.12rem .42rem; border-radius:5px; font-size:.68rem; font-weight:700; margin-inline-start:.25rem; vertical-align:middle; }
        .lang-badge.en { background:rgba(59,130,246,0.14); color:#3b82f6; }
        .lang-badge.ar { background:var(--cyan-dim); color:var(--cyan); }

        /* ═════════ STATS ═════════ */
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem; }
        .stat-card { position:relative; display:flex; align-items:center; gap:1rem; padding:1.25rem 1.35rem; background:var(--card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); text-decoration:none; transition:transform .2s, border-color .2s; overflow:hidden; }
        a.stat-card:hover { transform:translateY(-2px); border-color:var(--cyan-line); }
        .stat-card-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; background:var(--cyan-dim); color:var(--cyan); flex-shrink:0; }
        .stat-card-number { font:800 1.8rem/1.1 var(--font-head); color:var(--text); }
        .stat-card-label { font-size:.84rem; color:var(--muted); margin-top:.15rem; }

        /* ═════════ EMPTY STATE ═════════ */
        .empty-state { text-align:center; padding:3.5rem 1.5rem; color:var(--muted); }
        .empty-state i { font-size:2rem; margin-bottom:1rem; display:block; color:var(--cyan); opacity:.7; }

        /* ═════════ RESPONSIVE ═════════ */
        @media (max-width:1024px) {
            .sidebar { transform:translateX(-100%); box-shadow:var(--shadow-lg); }
            [dir="rtl"] .sidebar { transform:translateX(100%); }
            body.nav-open .sidebar { transform:none; }
            .sidebar-backdrop { display:block; position:fixed; inset:0; z-index:150; background:rgba(2,6,23,.5); backdrop-filter:blur(2px); opacity:0; visibility:hidden; transition:opacity .3s; }
            body.nav-open .sidebar-backdrop { opacity:1; visibility:visible; }
            .main { margin-inline-start:0; }
            .icon-btn.menu-btn { display:inline-flex; }
            .topbar { padding:0 1rem; }
            .content { padding:1.25rem 1rem; }
        }
        @media (max-width:768px) {
            .form-row { grid-template-columns:1fr; }
            .stats-grid { grid-template-columns:1fr 1fr; gap:.75rem; }
            .stat-card { flex-direction:column; align-items:flex-start; gap:.6rem; padding:1rem; }
            .stat-card-icon { width:38px; height:38px; font-size:.95rem; border-radius:11px; }
            .stat-card-number { font-size:1.5rem; }
            .topbar-title { font-size:1.05rem; }
            .topbar .btn-sm .btn-label { display:none; }
            .lang-switch { display:none; }
            .content [style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr !important; }
            th, td { padding:.8rem 1rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition-duration:.01ms !important; animation-duration:.01ms !important; }
        }
    </style>
    @yield('styles')
</head>
<body>

<!-- ═════════ SIDEBAR ═════════ -->
<aside class="sidebar" id="sidebar" aria-label="{{ __('Admin navigation') }}">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
        <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="42" height="42">
        <span>
            <strong>{{ $isRtl ? 'لوحة التحكم' : 'Portfolio CMS' }}</strong>
            <small>{{ __('Manage your website') }}</small>
        </span>
    </a>
    <nav class="sidebar-nav">
        <div class="nav-section">{{ __('Main') }}</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
            <i class="fas fa-gauge-high"></i> {{ __('Dashboard') }}
        </a>
        <a href="{{ route('admin.messages') }}" class="nav-item {{ request()->routeIs('admin.messages*') ? 'active' : '' }}" @if(request()->routeIs('admin.messages*')) aria-current="page" @endif>
            <i class="fas fa-inbox"></i> {{ __('Messages') }}
            @if(($unreadMessages ?? 0) > 0)<span class="nav-count" aria-label="{{ $unreadMessages }} {{ __('unread') }}">{{ $unreadMessages }}</span>@endif
        </a>
        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="nav-item">
            <i class="fas fa-globe"></i> {{ __('View Portfolio') }} <i class="fas fa-arrow-up-right-from-square ext"></i>
        </a>
        <div class="nav-section">{{ __('Content') }}</div>
        @foreach([
            ['admin.services', 'admin.services*', 'fa-handshake', __('Services')],
            ['admin.projects', 'admin.projects*', 'fa-rocket', __('Projects')],
            ['admin.testimonials', 'admin.testimonials*', 'fa-quote-left', __('Testimonials')],
            ['admin.faqs', 'admin.faqs*', 'fa-circle-question', __('FAQ')],
            ['admin.experience', 'admin.experience*', 'fa-briefcase', __('Experience')],
            ['admin.skills', 'admin.skills*', 'fa-code-branch', __('Skills')],
        ] as [$navRoute, $navPattern, $navIcon, $navLabel])
            <a href="{{ route($navRoute) }}" class="nav-item {{ request()->routeIs($navPattern) ? 'active' : '' }}" @if(request()->routeIs($navPattern)) aria-current="page" @endif>
                <i class="fas {{ $navIcon }}"></i> {{ $navLabel }}
            </a>
        @endforeach
        <div class="nav-section">{{ __('Configuration') }}</div>
        <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" @if(request()->routeIs('admin.settings*')) aria-current="page" @endif>
            <i class="fas fa-sliders"></i> {{ __('Settings') }}
        </a>
    </nav>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('admin.logout') }}" style="flex:1">
            @csrf
            <button type="submit" class="logout-btn"><i class="fas fa-right-from-bracket"></i> {{ __('Logout') }}</button>
        </form>
    </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- ═════════ MAIN ═════════ -->
<div class="main">
    <header class="topbar">
        <button type="button" class="icon-btn menu-btn" id="menuBtn" aria-label="{{ __('Menu') }}" aria-controls="sidebar" aria-expanded="false"><i class="fas fa-bars"></i></button>
        <h1 class="topbar-title">@yield('title', __('Dashboard'))</h1>
        <div class="topbar-actions">
            @yield('topbar-actions')
            <div class="lang-switch">
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}" lang="en">EN</a>
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn {{ $locale === 'ar' ? 'active' : '' }}" lang="ar">عربي</a>
            </div>
            <button type="button" class="icon-btn" id="themeBtn" aria-label="{{ __('Toggle light/dark mode') }}" title="{{ __('Toggle light/dark mode') }}">
                <i class="fas fa-sun theme-light-icon"></i><i class="fas fa-moon theme-dark-icon"></i>
            </button>
        </div>
    </header>
    <main class="content">
        @if(session('success'))
            <div class="alert alert-success toast" role="status" id="toast"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" role="alert"><div><i class="fas fa-circle-exclamation"></i> {{ session('error') }}</div></div>
        @endif
        @yield('content')
    </main>
</div>

<script>
(function () {
    // Mobile sidebar drawer
    const body = document.body;
    const menuBtn = document.getElementById('menuBtn');
    function setNav(open) {
        body.classList.toggle('nav-open', open);
        menuBtn.setAttribute('aria-expanded', open);
    }
    menuBtn.addEventListener('click', () => setNav(!body.classList.contains('nav-open')));
    document.getElementById('sidebarBackdrop').addEventListener('click', () => setNav(false));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') setNav(false); });

    // Light / dark theme
    document.getElementById('themeBtn').addEventListener('click', () => {
        const next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', next);
        try { localStorage.setItem('admin-theme', next); } catch (e) {}
    });

    // Auto-dismiss success toast
    const toast = document.getElementById('toast');
    if (toast) {
        setTimeout(() => { toast.classList.add('hide'); setTimeout(() => toast.remove(), 350); }, 3500);
    }

    // Wrap tables so they scroll horizontally on small screens
    document.querySelectorAll('.card > table').forEach(table => {
        const wrap = document.createElement('div');
        wrap.className = 'table-wrap';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    });
})();
</script>
</body>
</html>
