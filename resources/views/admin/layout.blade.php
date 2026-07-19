<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CMS — @yield('title', __('Dashboard'))</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --cyan: #00d4d4;
            --cyan-dim: rgba(0,212,212,0.12);
            --bg: #080c14;
            --bg2: #0d1425;
            --bg3: #111c30;
            --card: #0f1828;
            --border: #1a2540;
            --text: #e0e6f0;
            --muted: #5a6a84;
            --danger: #ef4444;
            --success: #22c55e;
            --sidebar-w: 240px;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: {{ $isRtl ? "'Cairo'" : "'Inter'" }}, sans-serif;
            background:var(--bg); color:var(--text);
            display:flex; min-height:100vh;
        }

        /* ─── Sidebar ─── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg2);
            border-{{ $isRtl ? 'left' : 'right' }}: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            {{ $isRtl ? 'right' : 'left' }}: 0;
            bottom: 0;
            z-index: 100;
        }
        .sidebar-logo {
            padding: 1.5rem 1.25rem;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--cyan);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .sidebar-logo span { {{ $isRtl ? 'margin-right' : 'margin-left' }}:auto; color: var(--text); font-size:0.75rem; font-weight:400; opacity:0.6; }
        .sidebar-nav { flex:1; padding: 1rem 0; overflow-y:auto; }
        .nav-section { padding: 0.5rem 1.25rem 0.25rem; font-size:0.65rem; text-transform:uppercase; letter-spacing:1.5px; color:var(--muted); }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            margin: 0.1rem 0.75rem;
            border-radius: 8px;
        }
        .nav-item:hover, .nav-item.active {
            background: var(--cyan-dim);
            color: var(--cyan);
        }
        .nav-item i { width: 16px; text-align:center; font-size:0.8rem; }
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--border);
        }

        /* ─── Main ─── */
        .main {
            {{ $isRtl ? 'margin-right' : 'margin-left' }}: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .topbar {
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 1rem;
        }
        .topbar-title { font-size: 1rem; font-weight: 600; }
        .topbar-actions { display:flex; gap:0.75rem; align-items:center; }
        .content { padding: 2rem; flex:1; }

        /* ─── Lang switch ─── */
        .lang-switch { display:flex; align-items:center; gap:0.2rem; background:rgba(0,212,212,0.06); border:1px solid rgba(0,212,212,0.2); border-radius:50px; padding:0.2rem; }
        .lang-btn { padding:0.25rem 0.6rem; border-radius:50px; font-size:0.72rem; font-weight:600; text-decoration:none; color:var(--muted); transition:all .2s; font-family:'JetBrains Mono',monospace; white-space:nowrap; }
        .lang-btn.active { background:var(--cyan); color:#080c14; }
        .lang-btn:not(.active):hover { color:var(--cyan); }

        /* ─── Alerts ─── */
        .alert { padding:0.875rem 1rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.875rem; display:flex; align-items:center; gap:0.5rem; }
        .alert-success { background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); color:var(--success); }
        .alert-error   { background:rgba(239,68,68,0.1);  border:1px solid rgba(239,68,68,0.3);  color:var(--danger); }

        /* ─── Cards ─── */
        .card { background:var(--card); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
        .card-header { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
        .card-title { font-size:0.95rem; font-weight:600; }
        .card-body { padding:1.5rem; }

        /* ─── Table ─── */
        table { width:100%; border-collapse:collapse; }
        th { text-align:start; padding:0.75rem 1rem; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.8px; color:var(--muted); border-bottom:1px solid var(--border); }
        td { padding:0.875rem 1rem; font-size:0.875rem; border-bottom:1px solid rgba(26,37,64,0.5); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:rgba(255,255,255,0.02); }

        /* ─── Buttons ─── */
        .btn { display:inline-flex; align-items:center; gap:0.4rem; padding:0.5rem 1rem; border-radius:7px; font-size:0.82rem; font-weight:600; font-family:inherit; cursor:pointer; border:none; text-decoration:none; transition:all 0.2s; white-space:nowrap; }
        .btn-primary   { background:var(--cyan); color:#080c14; }
        .btn-primary:hover { background:#00f5f5; transform:translateY(-1px); }
        .btn-secondary { background:rgba(255,255,255,0.06); color:var(--text); border:1px solid var(--border); }
        .btn-secondary:hover { border-color:var(--cyan); color:var(--cyan); }
        .btn-danger    { background:rgba(239,68,68,0.1); color:var(--danger); border:1px solid rgba(239,68,68,0.2); }
        .btn-danger:hover { background:rgba(239,68,68,0.2); }
        .btn-sm { padding:0.35rem 0.7rem; font-size:0.77rem; }
        .btn-view { background:rgba(0,212,212,0.08); color:var(--cyan); border:1px solid rgba(0,212,212,0.2); }
        .btn-view:hover { background:rgba(0,212,212,0.18); }

        /* ─── Form ─── */
        .form-group { margin-bottom:1.25rem; }
        .form-label { display:block; font-size:0.78rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.5rem; }
        .form-control { width:100%; padding:0.7rem 0.9rem; background:var(--bg); border:1px solid var(--border); border-radius:8px; color:var(--text); font-family:inherit; font-size:0.875rem; outline:none; transition:border-color 0.2s; }
        .form-control:focus { border-color:var(--cyan); box-shadow:0 0 0 3px rgba(0,212,212,0.08); }
        textarea.form-control { min-height:120px; resize:vertical; }
        .form-hint { font-size:0.75rem; color:var(--muted); margin-top:0.35rem; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .form-check { display:flex; align-items:center; gap:0.5rem; }
        .form-check input[type=checkbox] { width:16px; height:16px; accent-color:var(--cyan); }

        /* ─── Badges ─── */
        .badge { display:inline-block; padding:0.2rem 0.55rem; border-radius:50px; font-size:0.7rem; font-weight:600; }
        .badge-cyan    { background:rgba(0,212,212,0.12); color:var(--cyan); }
        .badge-success { background:rgba(34,197,94,0.12); color:var(--success); }
        .badge-muted   { background:rgba(255,255,255,0.06); color:var(--muted); }

        /* ─── Stats ─── */
        .stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:2rem; }
        .stat-card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:1.5rem; }
        .stat-card-number { font-size:2rem; font-weight:800; color:var(--cyan); }
        .stat-card-label  { font-size:0.8rem; color:var(--muted); margin-top:0.25rem; }

        /* ─── Tag preview ─── */
        .tag-preview { display:flex; flex-wrap:wrap; gap:0.4rem; margin-top:0.5rem; }
        .tag-pill { padding:0.2rem 0.6rem; background:var(--cyan-dim); border:1px solid rgba(0,212,212,0.2); border-radius:50px; font-size:0.72rem; color:var(--cyan); font-family:'JetBrains Mono',monospace; }

        /* ─── Section label ─── */
        .section-divider { font-size:0.7rem; text-transform:uppercase; letter-spacing:2px; color:var(--cyan); font-family:'JetBrains Mono',monospace; margin:1.5rem 0 0.75rem; }

        @media(max-width:768px) {
            .sidebar { display:none; }
            .main { margin-left:0 !important; margin-right:0 !important; }
            .form-row { grid-template-columns:1fr; }
            .stats-grid { grid-template-columns:1fr; }
        }
    </style>
    @yield('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <i class="fas fa-code"></i>
        {{ $isRtl ? 'لوحة التحكم' : 'Portfolio CMS' }}
        <span>v1</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">{{ __('Main') }}</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge-high"></i> {{ __('Dashboard') }}
        </a>
        <a href="{{ url('/') }}" target="_blank" class="nav-item">
            <i class="fas fa-eye"></i> {{ __('View Portfolio') }}
        </a>
        <div class="nav-section" style="margin-top:0.75rem">{{ __('Content') }}</div>
        <a href="{{ route('admin.projects') }}" class="nav-item {{ request()->routeIs('admin.projects*') ? 'active' : '' }}">
            <i class="fas fa-rocket"></i> {{ __('Projects') }}
        </a>
        <a href="{{ route('admin.experience') }}" class="nav-item {{ request()->routeIs('admin.experience*') ? 'active' : '' }}">
            <i class="fas fa-briefcase"></i> {{ __('Experience') }}
        </a>
        <a href="{{ route('admin.skills') }}" class="nav-item {{ request()->routeIs('admin.skills*') ? 'active' : '' }}">
            <i class="fas fa-code-branch"></i> {{ __('Skills') }}
        </a>
        <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="fas fa-sliders"></i> {{ __('Settings') }}
        </a>
    </nav>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" style="width:100%;background:none;border:none;cursor:pointer;text-align:{{ $isRtl ? 'right' : 'left' }}">
                <span onclick="this.closest('form').submit()" style="display:flex;align-items:center;gap:0.5rem;padding:0.6rem 0.75rem;border-radius:8px;color:var(--muted);font-size:0.8rem;transition:all 0.2s;cursor:pointer" onmouseover="this.style.background='rgba(239,68,68,0.1)';this.style.color='#ef4444'" onmouseout="this.style.background='';this.style.color='var(--muted)'">
                    <i class="fas fa-right-from-bracket"></i> {{ __('Logout') }}
                </span>
            </button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <div class="topbar">
        <div class="topbar-title">@yield('title', __('Dashboard'))</div>
        <div class="topbar-actions">
            @yield('topbar-actions')
            <div class="lang-switch">
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn {{ $locale === 'ar' ? 'active' : '' }}">عربي</a>
            </div>
        </div>
    </div>
    <div class="content">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>
</body>
</html>
