<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['hero_name'] ?? 'Portfolio' }} — {{ $settings['hero_tagline'] ?? 'Developer' }}</title>
    <meta name="description" content="{{ strip_tags($settings['hero_subtitle'] ?? '') }}">
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('theme'); } catch (e) {}
            var theme = ['light', 'dark', 'ocean', 'sunset'].includes(saved) ? saved : 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ─── THEMES ─── */
        :root,
        [data-theme="light"] {
            --cyan: #0089b3;
            --cyan-light: #00a8d6;
            --cyan-dark: #006d8f;
            --bg-primary: #f5f7fb;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #16202e;
            --text-secondary: #566072;
            --text-muted: #94a0b4;
            --border: #e4e9f1;
            --gradient: linear-gradient(135deg, #0ea5b5, #2563eb);
            --nav-bg: rgba(255,255,255,0.82);
            --mobile-nav-bg: rgba(255,255,255,0.98);
            --shadow-sm: 0 10px 30px rgba(15,23,42,0.08);
            --shadow-md: 0 20px 40px rgba(15,23,42,0.10);
            --shadow-lg: 0 25px 60px rgba(15,23,42,0.12);
            --heading-gradient: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            --scheme: light;
        }
        [data-theme="dark"] {
            --cyan: #00d4d4;
            --cyan-light: #00f5f5;
            --cyan-dark: #009999;
            --bg-primary: #0a0e17;
            --bg-secondary: #0f1623;
            --bg-card: #131c2e;
            --text-primary: #e8eaf0;
            --text-secondary: #8892a4;
            --text-muted: #4a5568;
            --border: #1e2d45;
            --gradient: linear-gradient(135deg, #00d4d4, #0088cc);
            --nav-bg: rgba(10,14,23,0.85);
            --mobile-nav-bg: rgba(10,14,23,0.98);
            --shadow-sm: 0 10px 30px rgba(0,0,0,0.4);
            --shadow-md: 0 20px 40px rgba(0,0,0,0.3);
            --shadow-lg: 0 25px 60px rgba(0,0,0,0.5);
            --heading-gradient: linear-gradient(135deg, #fff 0%, var(--text-primary) 100%);
            --scheme: dark;
        }
        [data-theme="ocean"] {
            --cyan: #20e3c2;
            --cyan-light: #4dfcdb;
            --cyan-dark: #0fae95;
            --bg-primary: #031824;
            --bg-secondary: #04222f;
            --bg-card: #062c3c;
            --text-primary: #e6f7fa;
            --text-secondary: #7fb2c2;
            --text-muted: #43727f;
            --border: #0d3a4b;
            --gradient: linear-gradient(135deg, #20e3c2, #0072ff);
            --nav-bg: rgba(3,24,36,0.85);
            --mobile-nav-bg: rgba(3,24,36,0.98);
            --shadow-sm: 0 10px 30px rgba(0,0,0,0.4);
            --shadow-md: 0 20px 40px rgba(0,0,0,0.3);
            --shadow-lg: 0 25px 60px rgba(0,0,0,0.5);
            --heading-gradient: linear-gradient(135deg, #fff 0%, var(--text-primary) 100%);
            --scheme: dark;
        }
        [data-theme="sunset"] {
            --cyan: #ff7a59;
            --cyan-light: #ff9d7a;
            --cyan-dark: #e0552f;
            --bg-primary: #1a0f1a;
            --bg-secondary: #231327;
            --bg-card: #2c1830;
            --text-primary: #f5e6f0;
            --text-secondary: #b498ab;
            --text-muted: #6b5470;
            --border: #3d2440;
            --gradient: linear-gradient(135deg, #ff7a59, #ff4d94);
            --nav-bg: rgba(26,15,26,0.85);
            --mobile-nav-bg: rgba(26,15,26,0.98);
            --shadow-sm: 0 10px 30px rgba(0,0,0,0.4);
            --shadow-md: 0 20px 40px rgba(0,0,0,0.3);
            --shadow-lg: 0 25px 60px rgba(0,0,0,0.5);
            --heading-gradient: linear-gradient(135deg, #fff 0%, var(--text-primary) 100%);
            --scheme: dark;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { font-family:{{ $locale === 'ar' ? "'Cairo'" : "'Inter'" }},sans-serif; background:var(--bg-primary); color:var(--text-primary); line-height:1.6; overflow-x:hidden; cursor:none; transition:background .35s ease, color .35s ease; }
        ::-webkit-scrollbar { width:5px; }
        ::-webkit-scrollbar-track { background:var(--bg-primary); }
        ::-webkit-scrollbar-thumb { background:var(--cyan-dark); border-radius:3px; }

        /* ─── CUSTOM CURSOR ─── */
        #cursor-dot {
            position: fixed;
            width: 8px;
            height: 8px;
            background: var(--cyan);
            border-radius: 50%;
            pointer-events: none;
            z-index: 99999;
            transform: translate(-50%, -50%);
            transition: width 0.2s, height 0.2s, background 0.2s;
            box-shadow: 0 0 10px var(--cyan), 0 0 20px rgba(0,212,212,0.4);
        }
        #cursor-ring {
            position: fixed;
            width: 36px;
            height: 36px;
            border: 1.5px solid rgba(0,212,212,0.6);
            border-radius: 50%;
            pointer-events: none;
            z-index: 99998;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s, border-color 0.3s, opacity 0.3s;
        }
        #cursor-trail-container {
            position: fixed;
            top: 0; left: 0;
            pointer-events: none;
            z-index: 99997;
        }
        .cursor-trail {
            position: fixed;
            width: 4px;
            height: 4px;
            background: var(--cyan);
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%);
            opacity: 0;
        }
        body.cursor-hover #cursor-dot { width:12px; height:12px; background:#00f5f5; }
        body.cursor-hover #cursor-ring { width:50px; height:50px; border-color:rgba(0,212,212,0.9); }
        body.cursor-click #cursor-dot { width:5px; height:5px; }
        body.cursor-click #cursor-ring { width:24px; height:24px; }

        /* ─── NAV ─── */
        nav {
            position:fixed; top:0; left:0; right:0; z-index:1000;
            padding:1.1rem 1.5rem 0;
            transition:padding .35s ease;
        }
        nav.scrolled { padding:0.6rem 1.5rem 0; }
        .nav-inner {
            max-width:1160px; margin:0 auto; display:flex; align-items:center; justify-content:space-between;
            height:64px; gap:1rem; padding:0 0.6rem 0 1.4rem;
            background:var(--nav-bg);
            backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
            border:1px solid var(--border);
            border-radius:100px;
            box-shadow:0 8px 30px rgba(0,0,0,0.06);
            transition:box-shadow .35s ease, border-radius .35s ease, background .35s ease;
            position:relative;
        }
        nav.scrolled .nav-inner { box-shadow:var(--shadow-md), 0 0 0 1px rgba(0,212,212,0.12); }
        [dir="rtl"] .nav-inner { padding:0 1.4rem 0 0.6rem; }
        .nav-logo { display:flex; align-items:center; gap:0.55rem; font-size:1.05rem; font-weight:800; color:var(--text-primary); text-decoration:none; letter-spacing:-0.3px; flex-shrink:0; }
        .nav-logo-badge { width:36px; height:36px; border-radius:11px; background:var(--gradient); display:flex; align-items:center; justify-content:center; font-size:1rem; font-weight:900; color:#fff; box-shadow:0 6px 16px rgba(0,212,212,0.35); position:relative; overflow:hidden; }
        .nav-logo-badge::after { content:''; position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,.35),transparent 60%); }
        .nav-logo-text { display:none; }
        @media(min-width:480px){ .nav-logo-text{ display:inline; } }

        .nav-links { display:flex; align-items:center; gap:0.15rem; list-style:none; background:rgba(127,127,127,0.06); border:1px solid transparent; border-radius:100px; padding:0.3rem; }
        .nav-links li { position:relative; }
        .nav-links a { display:block; color:var(--text-secondary); text-decoration:none; font-size:0.83rem; font-weight:600; letter-spacing:0.2px; transition:color 0.25s; position:relative; padding:0.5rem 1rem; border-radius:100px; }
        .nav-links a:not(.nav-cta):hover { color:var(--text-primary); }
        .nav-links a.active:not(.nav-cta) { color:#fff; }
        .nav-links a.active:not(.nav-cta)::before { content:''; position:absolute; inset:0; background:var(--gradient); border-radius:100px; z-index:-1; box-shadow:0 4px 14px rgba(0,212,212,0.35); }
        .nav-cta { margin-inline-start:0.4rem; background:var(--gradient); color:#fff !important; font-weight:700; box-shadow:0 6px 16px rgba(0,212,212,0.3); display:flex; align-items:center; gap:0.4rem; }
        .nav-cta:hover { transform:translateY(-1px); box-shadow:0 10px 22px rgba(0,212,212,0.42); }
        .nav-cta i { font-size:0.72rem; }

        .hamburger { display:none; flex-direction:column; gap:5px; cursor:none; width:38px; height:38px; align-items:center; justify-content:center; border-radius:50%; transition:background .2s; }
        .hamburger:hover { background:rgba(127,127,127,0.1); }
        .hamburger span { display:block; width:18px; height:2px; background:var(--text-primary); border-radius:2px; transition:all 0.3s; }
        .hamburger.open span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity:0; }
        .hamburger.open span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }

        .nav-controls { display:flex; align-items:center; gap:0.5rem; flex-shrink:0; padding-inline-start:0.5rem; }
        .nav-divider { width:1px; height:22px; background:var(--border); flex-shrink:0; }
        .lang-switch { display:flex; align-items:center; gap:0.15rem; background:rgba(127,127,127,0.08); border:1px solid var(--border); border-radius:50px; padding:0.2rem; flex-shrink:0; }
        .lang-btn { padding:0.32rem 0.65rem; border-radius:50px; font-size:0.7rem; font-weight:700; text-decoration:none; color:var(--text-secondary); transition:all .2s; font-family:'JetBrains Mono',monospace; cursor:none; }
        .lang-btn.active { background:var(--gradient); color:#fff; box-shadow:0 3px 10px rgba(0,212,212,0.3); }
        .lang-btn:not(.active):hover { color:var(--cyan); }

        /* ─── THEME SWITCH ─── */
        .theme-switch { position:relative; }
        .theme-toggle-btn { display:flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:50%; background:rgba(127,127,127,0.08); border:1px solid var(--border); color:var(--cyan); cursor:none; font-size:0.85rem; transition:all .2s; }
        .theme-toggle-btn:hover { background:rgba(0,212,212,0.15); border-color:rgba(0,212,212,0.3); transform:rotate(20deg); }
        .theme-menu { position:absolute; top:calc(100% + 14px); right:0; background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:0.5rem; display:flex; flex-direction:column; gap:0.15rem; min-width:160px; box-shadow:var(--shadow-md); opacity:0; visibility:hidden; transform:translateY(-8px) scale(0.96); transform-origin:top right; transition:all .2s; z-index:1100; }
        [dir="rtl"] .theme-menu { right:auto; left:0; transform-origin:top left; }
        .theme-menu.open { opacity:1; visibility:visible; transform:translateY(0) scale(1); }
        .theme-option { display:flex; align-items:center; gap:0.6rem; padding:0.55rem 0.7rem; border-radius:10px; cursor:none; font-size:0.82rem; color:var(--text-secondary); transition:all .15s; background:transparent; border:none; text-align:{{ $locale === 'ar' ? 'right' : 'left' }}; width:100%; font-family:inherit; }
        .theme-option:hover { background:rgba(0,212,212,0.08); color:var(--text-primary); }
        .theme-option.active { color:var(--cyan); font-weight:700; background:rgba(0,212,212,0.06); }
        .theme-swatch { width:16px; height:16px; border-radius:50%; flex-shrink:0; border:1px solid rgba(255,255,255,0.15); }
        .theme-swatch.sw-light { background:linear-gradient(135deg,#f5f7fb,#0ea5b5); }
        .theme-swatch.sw-dark { background:linear-gradient(135deg,#0a0e17,#00d4d4); }
        .theme-swatch.sw-ocean { background:linear-gradient(135deg,#031824,#20e3c2); }
        .theme-swatch.sw-sunset { background:linear-gradient(135deg,#1a0f1a,#ff7a59); }

        /* ─── PROJECT CARD LINK ─── */
        .project-card-link { text-decoration:none; color:inherit; display:block; }
        .project-card { cursor:none; }
        .project-card:hover .project-title { color:var(--cyan); }
        .project-view-more { display:inline-flex; align-items:center; gap:0.4rem; margin-top:1rem; font-size:0.8rem; color:var(--cyan); font-weight:600; opacity:0; transition:opacity .2s; font-family:'JetBrains Mono',monospace; }
        .project-card:hover .project-view-more { opacity:1; }

        /* ─── RTL ─── */
        @if($locale === 'ar')
        .nav-links a::after { left:auto; right:0; }
        .hero-stats { direction:ltr; }
        @endif

        /* ─── SECTIONS ─── */
        section { position:relative; z-index:1; }
        .container { max-width:1200px; margin:0 auto; padding:0 2rem; }
        .section-header { text-align:center; margin-bottom:4rem; }
        .section-tag { display:inline-block; font-family:'JetBrains Mono',monospace; font-size:0.8rem; color:var(--cyan); letter-spacing:2px; text-transform:uppercase; margin-bottom:1rem; }
        .section-title { font-size:clamp(2rem,5vw,2.75rem); font-weight:800; }
        .section-title span { color:var(--cyan); }
        .section-line { width:60px; height:3px; background:var(--gradient); margin:1.5rem auto 0; border-radius:2px; }

        /* ─── HERO ─── */
        #hero { min-height:100vh; display:flex; align-items:center; padding:100px 2rem 60px; position:relative; overflow:hidden; }
        .hero-bg { position:absolute; inset:0; background:radial-gradient(ellipse 80% 60% at 70% 50%, rgba(0,212,212,0.06) 0%, transparent 60%), radial-gradient(ellipse 50% 80% at 10% 80%, rgba(0,136,204,0.05) 0%, transparent 50%); }
        .hero-grid { position:absolute; inset:0; background-image:linear-gradient(rgba(0,212,212,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0,212,212,0.03) 1px, transparent 1px); background-size:60px 60px; }
        .hero-inner { max-width:1200px; margin:0 auto; display:grid; grid-template-columns:1fr 1fr; gap:4rem; align-items:center; position:relative; z-index:1; width:100%; }
        .hero-badge { display:inline-flex; align-items:center; gap:0.5rem; padding:0.4rem 1rem; background:rgba(0,212,212,0.08); border:1px solid rgba(0,212,212,0.2); border-radius:50px; font-family:'JetBrains Mono',monospace; font-size:0.8rem; color:var(--cyan); margin-bottom:1.5rem; }
        .hero-badge::before { content:''; width:8px; height:8px; border-radius:50%; background:var(--cyan); animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.8)} }
        .hero-title { font-size:clamp(2.5rem,6vw,4rem); font-weight:900; line-height:1.1; letter-spacing:-1px; margin-bottom:1rem; }
        .hero-title .name { display:block; background:var(--heading-gradient); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .hero-title .role { display:block; background:var(--gradient); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; min-height:1.2em; }
        .hero-subtitle { font-size:1.05rem; color:var(--text-secondary); line-height:1.75; margin-bottom:2.5rem; max-width:500px; }
        .hero-actions { display:flex; gap:1rem; flex-wrap:wrap; }
        .btn-primary { display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:var(--gradient); color:var(--bg-primary); border-radius:8px; font-weight:700; font-size:0.9rem; text-decoration:none; transition:all 0.3s; border:none; cursor:none; box-shadow:0 0 30px rgba(0,212,212,0.3); }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 0 50px rgba(0,212,212,0.5); }
        .btn-secondary { display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:transparent; color:var(--text-primary); border:1px solid var(--border); border-radius:8px; font-weight:600; font-size:0.9rem; text-decoration:none; transition:all 0.3s; cursor:none; }
        .btn-secondary:hover { border-color:var(--cyan); color:var(--cyan); transform:translateY(-2px); }
        .hero-stats { display:flex; align-items:stretch; gap:0; margin-top:3rem; padding-top:2rem; border-top:1px solid var(--border); position:relative; }
        .stat-item { display:flex; align-items:center; gap:0.85rem; padding:0.5rem 1.75rem; position:relative; transition:transform .3s ease; }
        .stat-item:first-child { padding-inline-start:0; }
        .stat-item:not(:first-child)::before { content:''; position:absolute; inset-inline-start:0; top:8%; bottom:8%; width:1px; background:linear-gradient(var(--border),var(--border)); }
        .stat-item:hover { transform:translateY(-3px); }
        .stat-icon { flex-shrink:0; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1rem; background:rgba(0,212,212,0.08); border:1px solid rgba(0,212,212,0.18); color:var(--cyan); transition:all .3s ease; }
        .stat-item:hover .stat-icon { background:var(--gradient); color:#fff; box-shadow:0 8px 18px rgba(0,212,212,0.35); transform:scale(1.06) rotate(-6deg); }
        .stat-text { display:flex; flex-direction:column; }
        .stat-number { font-size:1.85rem; font-weight:800; line-height:1.1; background:var(--gradient); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; display:block; font-family:'JetBrains Mono',monospace; letter-spacing:-0.5px; }
        .stat-label { font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; font-weight:600; margin-top:0.15rem; }
        .hero-visual { display:flex; justify-content:center; align-items:center; position:relative; }
        .code-block { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:2rem; font-family:'JetBrains Mono',monospace; font-size:0.82rem; line-height:1.8; position:relative; overflow:hidden; max-width:420px; width:100%; box-shadow:var(--shadow-lg); }
        .code-block::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--gradient); }
        .code-dots { display:flex; gap:6px; margin-bottom:1.25rem; }
        .code-dots span { width:12px; height:12px; border-radius:50%; }
        .code-dots span:nth-child(1){background:#ff5f57} .code-dots span:nth-child(2){background:#ffbd2e} .code-dots span:nth-child(3){background:#28ca41}
        .code-line { display:flex; gap:0.5rem; }
        .ln{color:#2d3f5a;min-width:20px;user-select:none} .kw{color:#c792ea} .fn{color:#82aaff} .cl{color:#00d4d4} .st{color:#c3e88d} .cm{color:#546e7a} .ar{color:#f78c6c} .op{color:#89ddff}
        .floating-card { position:absolute; background:var(--bg-card); border:1px solid rgba(0,212,212,0.2); border-radius:10px; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.5rem; font-size:0.8rem; font-weight:600; box-shadow:var(--shadow-sm); animation:float 4s ease-in-out infinite; }
        .floating-card.card-1{top:-20px;right:-30px;animation-delay:0s} .floating-card.card-2{bottom:20px;left:-30px;animation-delay:2s}
        .floating-card i{color:var(--cyan)}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}

        /* ─── ABOUT ─── */
        #about { padding:8rem 2rem; background:var(--bg-secondary); }
        .about-grid { display:grid; grid-template-columns:1fr 1.5fr; gap:5rem; align-items:center; }
        .about-avatar-wrap { position:relative; max-width:360px; margin:0 auto; }
        .about-photo-frame { position:relative; border-radius:24px; overflow:hidden; aspect-ratio:4/5; background:var(--gradient); padding:4px; box-shadow:var(--shadow-lg); }
        .about-photo { width:100%; height:100%; object-fit:cover; object-position:50% 32%; border-radius:20px; display:block; filter:saturate(1.08) contrast(1.03); transition:transform .6s ease, filter .6s ease; }
        .about-photo-frame:hover .about-photo { transform:scale(1.045); }
        .about-photo-glow { position:absolute; inset:-30%; background:radial-gradient(circle at 50% 30%, rgba(0,212,212,0.35), transparent 65%); opacity:.55; filter:blur(20px); z-index:0; pointer-events:none; }
        .about-photo-frame::after { content:''; position:absolute; inset:4px; border-radius:20px; box-shadow:inset 0 -60px 70px -30px rgba(0,0,0,0.55); pointer-events:none; z-index:1; }
        .about-photo-scan { position:absolute; left:4px; right:4px; top:4px; height:40%; border-radius:20px 20px 0 0; background:radial-gradient(ellipse at 50% 0%, rgba(255,255,255,0.16), transparent 70%); pointer-events:none; z-index:1; }
        .about-photo-corner { position:absolute; width:26px; height:26px; border:2px solid var(--cyan); z-index:2; opacity:.9; }
        .about-photo-corner.tl { top:14px; left:14px; border-right:none; border-bottom:none; border-radius:6px 0 0 0; }
        .about-photo-corner.br { bottom:14px; right:14px; border-left:none; border-top:none; border-radius:0 0 6px 0; }
        [dir="rtl"] .about-photo-corner.tl { left:auto; right:14px; border-right:2px solid var(--cyan); border-left:none; border-radius:0 6px 0 0; }
        [dir="rtl"] .about-photo-corner.br { right:auto; left:14px; border-left:2px solid var(--cyan); border-right:none; border-radius:0 0 0 6px; }
        .avatar-badge { position:absolute; bottom:-16px; right:calc(50% - 130px); background:var(--bg-card); border:2px solid var(--cyan); border-radius:50px; padding:0.4rem 0.8rem; font-size:0.75rem; font-weight:700; color:var(--cyan); box-shadow:var(--shadow-sm); z-index:3; }
        [dir="rtl"] .avatar-badge { right:auto; left:calc(50% - 130px); }
        .about-content h2 { font-size:2rem; font-weight:800; margin-bottom:1.5rem; line-height:1.3; }
        .about-content p { color:var(--text-secondary); line-height:1.8; margin-bottom:1.25rem; font-size:1rem; }
        .about-tags { display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:1.5rem; }
        .tag { padding:0.35rem 0.85rem; background:rgba(0,212,212,0.08); border:1px solid rgba(0,212,212,0.2); border-radius:50px; font-size:0.8rem; color:var(--cyan); font-family:'JetBrains Mono',monospace; }
        .about-links { display:flex; gap:1rem; margin-top:2rem; }
        .about-links a { display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:8px; border:1px solid var(--border); color:var(--text-secondary); text-decoration:none; transition:all 0.2s; cursor:none; }
        .about-links a:hover { border-color:var(--cyan); color:var(--cyan); transform:translateY(-2px); }

        /* ─── SKILLS ─── */
        #skills { padding:8rem 2rem; }
        .skills-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:1.5rem; }
        .skill-category { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:2rem; transition:all 0.3s; position:relative; overflow:hidden; }
        .skill-category::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--gradient); transform:scaleX(0); transition:transform 0.3s; }
        .skill-category:hover { border-color:rgba(0,212,212,0.3); transform:translateY(-4px); box-shadow:var(--shadow-md); }
        .skill-category:hover::before { transform:scaleX(1); }
        .skill-cat-icon { font-size:1.75rem; margin-bottom:1rem; }
        .skill-cat-title { font-size:1.1rem; font-weight:700; margin-bottom:1.25rem; }
        .skill-items { display:flex; flex-direction:column; gap:0.75rem; }
        .skill-info { display:flex; justify-content:space-between; margin-bottom:0.35rem; }
        .skill-name { font-size:0.875rem; color:var(--text-secondary); font-weight:500; }
        .skill-pct { font-size:0.75rem; color:var(--cyan); font-family:'JetBrains Mono',monospace; }
        .skill-bar { height:4px; background:var(--border); border-radius:2px; overflow:hidden; }
        .skill-fill { height:100%; background:var(--gradient); border-radius:2px; width:0; transition:width 1.2s ease; }
        .tech-tags { display:flex; flex-wrap:wrap; gap:0.5rem; }
        .tech-tag { padding:0.3rem 0.7rem; background:rgba(255,255,255,0.04); border:1px solid var(--border); border-radius:6px; font-size:0.8rem; color:var(--text-secondary); font-family:'JetBrains Mono',monospace; transition:all 0.2s; }
        .tech-tag:hover { border-color:var(--cyan); color:var(--cyan); }

        /* ─── EXPERIENCE ─── */
        #experience { padding:8rem 2rem; background:var(--bg-secondary); overflow:hidden; }
        .timeline { position:relative; max-width:920px; margin:0 auto; }
        .timeline::before { content:''; position:absolute; left:28px; top:0; bottom:0; width:2px; background:linear-gradient(to bottom,transparent,var(--cyan) 12%,rgba(0,212,212,0.22) 88%,transparent); }
        [dir="rtl"] .timeline::before { left:auto; right:28px; }
        .timeline-item { padding-left:88px; margin-bottom:2rem; position:relative; }
        [dir="rtl"] .timeline-item { padding-left:0; padding-right:88px; }
        .timeline-dot { position:absolute; left:16px; top:1.7rem; width:25px; height:25px; border-radius:9px; background:var(--bg-card); border:2px solid var(--cyan); transition:all 0.3s; z-index:1; display:flex; align-items:center; justify-content:center; color:var(--cyan); font-size:0.65rem; box-shadow:0 0 0 7px var(--bg-secondary); }
        [dir="rtl"] .timeline-dot { left:auto; right:16px; }
        .timeline-item:hover .timeline-dot { background:var(--gradient); color:#fff; transform:rotate(45deg); box-shadow:0 0 0 7px var(--bg-secondary),0 0 22px rgba(0,212,212,0.5); }
        .timeline-item:hover .timeline-dot i { transform:rotate(-45deg); }
        .timeline-dot i { transition:transform .3s; }
        .timeline-card { background:linear-gradient(135deg,var(--bg-card),rgba(0,212,212,0.025)); border:1px solid var(--border); border-radius:22px; padding:1.8rem 2rem; transition:all 0.3s; position:relative; overflow:hidden; }
        .timeline-card::after { content:attr(data-step); position:absolute; top:-1rem; right:1rem; font:900 5rem/1 'JetBrains Mono',monospace; color:rgba(0,212,212,0.05); pointer-events:none; }
        [dir="rtl"] .timeline-card::after { right:auto; left:1rem; }
        .timeline-card:hover { border-color:rgba(0,212,212,0.38); transform:translateX(6px); box-shadow:var(--shadow-md); }
        [dir="rtl"] .timeline-card:hover { transform:translateX(-6px); }
        .timeline-header { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1rem; flex-wrap:wrap; position:relative; z-index:1; }
        .timeline-kicker { color:var(--cyan); font:600 0.7rem 'JetBrains Mono',monospace; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:0.45rem; }
        .timeline-title { font-size:1.2rem; font-weight:800; }
        .timeline-company { font-size:0.9rem; color:var(--text-secondary); margin-top:0.25rem; }
        .timeline-date { font-size:0.76rem; color:var(--cyan); font-family:'JetBrains Mono',monospace; white-space:nowrap; padding:0.4rem 0.75rem; background:rgba(0,212,212,0.08); border-radius:50px; border:1px solid rgba(0,212,212,0.18); }
        .timeline-desc { color:var(--text-secondary); font-size:0.92rem; line-height:1.85; margin-bottom:1.2rem; max-width:760px; position:relative; z-index:1; }
        .timeline-tags { display:flex; flex-wrap:wrap; gap:0.45rem; position:relative; z-index:1; }
        .timeline-tag { padding:0.3rem 0.7rem; background:rgba(0,212,212,0.06); border:1px solid rgba(0,212,212,0.15); border-radius:50px; font-size:0.72rem; color:var(--cyan); font-family:'JetBrains Mono',monospace; }

        /* ─── PROJECTS ─── */
        #projects { padding:8rem 2rem; }
        .projects-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(340px,1fr)); gap:1.5rem; }
        .project-card { background:var(--bg-card); border:1px solid var(--border); border-radius:22px; overflow:hidden; transition:all 0.3s; display:flex; flex-direction:column; height:100%; }
        .project-card:hover { border-color:rgba(0,212,212,0.42); transform:translateY(-8px); box-shadow:var(--shadow-lg); }
        .project-cover { height:165px; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,rgba(0,212,212,0.18),rgba(0,136,204,0.08)); border-bottom:1px solid var(--border); }
        .project-base-image { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.58; mix-blend-mode:screen; transition:transform .6s ease, opacity .35s ease; }
        .project-card:hover .project-base-image { transform:scale(1.08); opacity:.72; }
        .project-cover::before { content:''; position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.08) 1px,transparent 1px); background-size:24px 24px; mask-image:linear-gradient(to bottom right,black,transparent 75%); }
        .project-cover::after { content:''; position:absolute; width:180px; height:180px; border:1px solid rgba(255,255,255,.18); border-radius:50%; transform:translate(45px,-35px); box-shadow:0 0 0 22px rgba(255,255,255,.04),0 0 0 44px rgba(255,255,255,.025); }
        .project-cover.cover-1 { background:linear-gradient(135deg,#0d7181, #1d4ed8); }
        .project-cover.cover-2 { background:linear-gradient(135deg,#334155, #0e7490); }
        .project-cover.cover-3 { background:linear-gradient(135deg,#075985, #0f766e); }
        .project-cover.cover-4 { background:linear-gradient(135deg,#4338ca, #0891b2); }
        .project-cover.cover-5 { background:linear-gradient(135deg,#334155, #7c3aed); }
        .project-cover-label { position:absolute; top:1rem; left:1rem; color:rgba(255,255,255,.8); font:700 0.65rem 'JetBrains Mono',monospace; letter-spacing:1.5px; z-index:1; }
        [dir="rtl"] .project-cover-label { left:auto; right:1rem; }
        .project-cover-number { position:absolute; bottom:0.8rem; right:1rem; color:rgba(255,255,255,.28); font:900 2rem 'JetBrains Mono',monospace; z-index:1; }
        [dir="rtl"] .project-cover-number { right:auto; left:1rem; }
        .project-icon { width:68px; height:68px; border-radius:22px; background:rgba(4,19,31,.42); border:1px solid rgba(255,255,255,.25); display:flex; align-items:center; justify-content:center; font-size:1.8rem; position:relative; z-index:2; box-shadow:0 14px 35px rgba(0,0,0,.2); transition:transform .35s; }
        .project-card:hover .project-icon { transform:scale(1.08) rotate(-5deg); }
        .project-links { display:flex; gap:0.5rem; }
        .project-link { width:34px; height:34px; border-radius:6px; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--text-muted); text-decoration:none; font-size:0.85rem; transition:all 0.2s; cursor:none; }
        .project-link:hover { border-color:var(--cyan); color:var(--cyan); }
        .project-header { padding:1rem 1.4rem 0; display:flex; justify-content:flex-end; align-items:flex-start; margin-top:-3rem; position:relative; z-index:3; }
        .project-body { padding:1rem 1.5rem 1.5rem; flex:1; display:flex; flex-direction:column; }
        .project-title { font-size:1.15rem; font-weight:700; margin-bottom:0.6rem; }
        .project-desc { color:var(--text-secondary); font-size:0.875rem; line-height:1.7; flex:1; margin-bottom:1.25rem; }
        .project-stack { display:flex; flex-wrap:wrap; gap:0.4rem; }
        .stack-tag { padding:0.25rem 0.6rem; background:rgba(255,255,255,0.04); border:1px solid var(--border); border-radius:4px; font-size:0.75rem; color:var(--text-muted); font-family:'JetBrains Mono',monospace; }
        .featured-badge { display:inline-flex; align-items:center; gap:0.3rem; font-size:0.7rem; color:var(--cyan); margin-bottom:0.5rem; }
        .project-actions { display:flex; align-items:center; gap:0.6rem; margin-top:1.25rem; padding-top:1rem; border-top:1px solid var(--border); }
        .project-action { display:inline-flex; align-items:center; justify-content:center; gap:0.4rem; padding:0.55rem 0.85rem; border-radius:9px; border:1px solid var(--border); color:var(--text-secondary); text-decoration:none; font-size:0.76rem; font-weight:700; transition:all .25s; cursor:none; }
        .project-action.primary { background:var(--gradient); color:#fff; border-color:transparent; box-shadow:0 5px 14px rgba(0,212,212,0.22); }
        .project-action:hover { color:var(--cyan); border-color:var(--cyan); transform:translateY(-2px); }
        .project-action.primary:hover { color:#fff; box-shadow:0 8px 20px rgba(0,212,212,0.35); }
        .project-action i { font-size:0.7rem; }

        /* ─── CONTACT ─── */
        #contact { padding:8rem 2rem; background:var(--bg-secondary); }
        .contact-wrapper { max-width:1060px; margin:0 auto; }
        .contact-header { max-width:660px; margin:0 auto 3rem; text-align:center; }
        .contact-header p { color:var(--text-secondary); margin-top:1rem; font-size:0.95rem; line-height:1.8; }
        .contact-layout { display:grid; grid-template-columns:0.82fr 1.18fr; gap:1.5rem; align-items:stretch; }
        [dir="rtl"] .contact-layout { direction:rtl; }
        .contact-intro { background:linear-gradient(145deg,var(--bg-card),rgba(0,212,212,0.06)); border:1px solid var(--border); border-radius:24px; padding:2rem; position:relative; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between; }
        .contact-intro::before { content:'✦'; position:absolute; right:-1rem; top:-2rem; font-size:10rem; line-height:1; color:rgba(0,212,212,0.07); transform:rotate(18deg); }
        [dir="rtl"] .contact-intro::before { right:auto; left:-1rem; }
        .contact-intro h3 { font-size:1.5rem; line-height:1.35; position:relative; z-index:1; }
        .contact-intro p { color:var(--text-secondary); line-height:1.8; font-size:0.9rem; margin-top:0.8rem; position:relative; z-index:1; }
        .contact-availability { display:flex; align-items:center; gap:0.65rem; color:var(--cyan); font-size:0.8rem; font-weight:700; margin-top:2rem; position:relative; z-index:1; }
        .contact-availability::before { content:''; width:9px; height:9px; border-radius:50%; background:#22c55e; box-shadow:0 0 0 5px rgba(34,197,94,.12); }
        .contact-cards { display:grid; gap:0.75rem; margin:0; position:relative; z-index:1; }
        .contact-card { background:rgba(127,127,127,0.05); border:1px solid var(--border); border-radius:14px; padding:0.9rem 1rem; text-decoration:none; transition:all 0.3s; display:grid; grid-template-columns:38px 1fr; text-align:{{ $locale === 'ar' ? 'right' : 'left' }}; align-items:center; gap:0.75rem; cursor:none; }
        .contact-card:hover { border-color:var(--cyan); transform:translateX(4px); box-shadow:var(--shadow-md); }
        [dir="rtl"] .contact-card:hover { transform:translateX(-4px); }
        .contact-card i { width:38px; height:38px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1rem; color:var(--cyan); background:rgba(0,212,212,0.09); }
        .contact-card-label { font-size:0.68rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); display:block; }
        .contact-card-value { font-size:0.82rem; color:var(--text-primary); word-break:break-all; display:block; margin-top:0.15rem; }
        .contact-form { background:var(--bg-card); border:1px solid var(--border); border-radius:24px; padding:2rem; margin-top:0; text-align:left; box-shadow:var(--shadow-sm); }
        [dir="rtl"] .contact-form { text-align:right; }
        .contact-form-head { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1.5rem; }
        .contact-form-head h3 { font-size:1.15rem; }
        .contact-form-head p { color:var(--text-muted); font-size:0.78rem; margin-top:0.25rem; }
        .contact-form-mark { width:40px; height:40px; border-radius:13px; background:var(--gradient); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .form-group { margin-bottom:1.25rem; }
        .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.5rem; }
        .form-input, .form-textarea { width:100%; padding:0.75rem 1rem; background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-family:'Inter',sans-serif; font-size:0.9rem; transition:border-color 0.2s; outline:none; cursor:none; }
        .form-input:focus, .form-textarea:focus { border-color:var(--cyan); box-shadow:0 0 0 3px rgba(0,212,212,0.08); }
        .form-textarea { min-height:140px; resize:vertical; }
        .form-submit { width:100%; padding:1rem; background:var(--gradient); border:none; border-radius:8px; color:var(--bg-primary); font-weight:700; font-size:0.95rem; cursor:none; font-family:'Inter',sans-serif; transition:all 0.3s; box-shadow:0 0 25px rgba(0,212,212,0.25); }
        .form-submit:hover { transform:translateY(-2px); box-shadow:0 0 40px rgba(0,212,212,0.45); }

        /* ─── ADMIN LINK ─── */
        .admin-link { position:fixed; bottom:1.5rem; right:1.5rem; z-index:500; background:var(--bg-card); border:1px solid var(--border); border-radius:50px; padding:0.5rem 1rem; font-size:0.78rem; color:var(--text-muted); text-decoration:none; display:flex; align-items:center; gap:0.4rem; transition:all 0.2s; cursor:none; }
        .admin-link:hover { border-color:var(--cyan); color:var(--cyan); }

        /* ─── FOOTER ─── */
        footer { background:var(--bg-primary); border-top:1px solid var(--border); padding:2rem; text-align:center; color:var(--text-muted); font-size:0.85rem; }
        footer span { color:var(--cyan); }

        /* ─── REVEAL ─── */
        .reveal { opacity:0; transform:translateY(30px); transition:opacity 0.7s ease, transform 0.7s ease; }
        .reveal.visible { opacity:1; transform:translateY(0); }

        /* ─── MOBILE ─── */
        @media(max-width:768px){
            nav{padding:0.85rem 1rem 0}
            nav.scrolled{padding:0.5rem 1rem 0}
            .nav-inner{padding:0 0.5rem 0 1rem; height:58px}
            [dir="rtl"] .nav-inner{padding:0 1rem 0 0.5rem}
            .nav-links{display:none}
            .hamburger{display:flex}
            .nav-controls{gap:0.35rem}
            .lang-switch{padding:0.2rem}
            .lang-btn{padding:0.25rem 0.5rem}
            .theme-toggle-btn{width:32px;height:32px}
            .nav-links.open{
                display:flex;flex-direction:column;position:absolute;top:calc(100% + 10px);left:0;right:0;
                background:var(--mobile-nav-bg);backdrop-filter:blur(20px);
                padding:1rem;border:1px solid var(--border);border-radius:20px;
                box-shadow:var(--shadow-lg);gap:0.25rem;background-clip:padding-box;
            }
            .nav-links.open li{width:100%}
            .nav-links.open a{display:block;padding:0.8rem 1rem;border-radius:12px;font-size:0.95rem}
            .nav-links.open a.active:not(.nav-cta)::before{border-radius:12px}
            .nav-links.open a:not(.nav-cta):hover{background:rgba(127,127,127,0.08)}
            .nav-links.open .nav-cta{justify-content:center;margin-inline-start:0;margin-top:0.4rem}
            .hero-inner{grid-template-columns:1fr;text-align:center}
            .hero-visual{display:none}
            .hero-subtitle{max-width:100%}
            .hero-actions{justify-content:center}
            .hero-stats{justify-content:center}
            .about-grid{grid-template-columns:1fr;text-align:center}
            .about-links{justify-content:center}
            .about-tags{justify-content:center}
            .form-row{grid-template-columns:1fr}
            .timeline::before{left:12px}
            [dir="rtl"] .timeline::before{left:auto;right:12px}
            .timeline-item{padding-left:42px}
            [dir="rtl"] .timeline-item{padding-left:0;padding-right:42px}
            .timeline-dot{left:0}
            [dir="rtl"] .timeline-dot{left:auto;right:0}
            .timeline-card{padding:1.35rem}
            .timeline-card::after{font-size:3.5rem}
            .contact-layout{grid-template-columns:1fr}
            .contact-intro{gap:2rem}
            .project-cover{height:145px}
            .project-actions{flex-wrap:wrap}
            body{cursor:auto}
            #cursor-dot,#cursor-ring,#cursor-trail-container{display:none}
        }
    </style>
</head>
<body>

<!-- CURSOR -->
<div id="cursor-dot"></div>
<div id="cursor-ring"></div>
<div id="cursor-trail-container"></div>

<!-- NAV -->
<nav id="navbar">
    <div class="nav-inner">
        <a class="nav-logo" href="#hero">
            <span class="nav-logo-badge">B</span>
            <span class="nav-logo-text">{{ explode(' ', $settings['hero_name'] ?? 'Baraa')[0] }}<span style="color:var(--cyan)">.</span></span>
        </a>
        <ul class="nav-links" id="navLinks">
            <li><a href="#about">{{ __('About') }}</a></li>
            <li><a href="#skills">{{ __('Skills') }}</a></li>
            <li><a href="#experience">{{ __('Experience') }}</a></li>
            <li><a href="#projects">{{ __('Projects') }}</a></li>
            <li><a class="nav-cta" href="#contact">{{ __('Hire Me') }} <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}"></i></a></li>
        </ul>
        <div class="nav-controls">
            <div class="lang-switch">
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn {{ $locale === 'ar' ? 'active' : '' }}">عربي</a>
            </div>
            <div class="theme-switch" id="themeSwitch">
                <button type="button" class="theme-toggle-btn" id="themeToggleBtn" title="{{ __('Theme') }}">
                    <i class="fas fa-palette"></i>
                </button>
                <div class="theme-menu" id="themeMenu">
                    <button type="button" class="theme-option" data-theme-choice="light"><span class="theme-swatch sw-light"></span>{{ __('Light') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="dark"><span class="theme-swatch sw-dark"></span>{{ __('Dark') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="ocean"><span class="theme-swatch sw-ocean"></span>{{ __('Ocean') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="sunset"><span class="theme-swatch sw-sunset"></span>{{ __('Sunset') }}</button>
                </div>
            </div>
            <div class="nav-divider"></div>
            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section id="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-inner">
        <div>
            <div class="hero-badge">{{ __('Available for opportunities') }}</div>
            <h1 class="hero-title">
                <span class="name">{{ ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? 'Your Name') }}</span>
                <span class="role" id="typing-role">{{ ts($settings, 'hero_tagline') ?: ($settings['hero_tagline'] ?? 'Developer') }}</span>
            </h1>
            <p class="hero-subtitle">{!! ts($settings, 'hero_subtitle') ?: ($settings['hero_subtitle'] ?? '') !!}</p>
            <div class="hero-actions">
                <a href="#projects" class="btn-primary"><i class="fas fa-rocket"></i> {{ __('View My Work') }}</a>
                <a href="#contact" class="btn-secondary"><i class="fas fa-envelope"></i> {{ __('Get In Touch') }}</a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-text">
                        <span class="stat-number">{{ $settings['hero_stat_years'] ?? '5+' }}</span>
                        <span class="stat-label">{{ __('Years Exp.') }}</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><i class="fas fa-diagram-project"></i></div>
                    <div class="stat-text">
                        <span class="stat-number">{{ $settings['hero_stat_projects'] ?? '30+' }}</span>
                        <span class="stat-label">{{ __('Projects') }}</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><i class="fas fa-face-smile"></i></div>
                    <div class="stat-text">
                        <span class="stat-number">{{ $settings['hero_stat_clients'] ?? '15+' }}</span>
                        <span class="stat-label">{{ __('Happy Clients') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="floating-card card-1"><i class="fas fa-check-circle"></i><span>{{ __('Laravel Expert') }}</span></div>
            <div class="code-block" dir="ltr">
                <div class="code-dots"><span></span><span></span><span></span></div>
                <div class="code-line"><span class="ln">1</span><span class="cm">// {{ $settings['hero_name'] ?? 'Portfolio' }}</span></div>
                <div class="code-line"><span class="ln">2</span></div>
                <div class="code-line"><span class="ln">3</span><span class="kw">class </span><span class="cl">BackendEngineer</span></div>
                <div class="code-line"><span class="ln">4</span><span class="op">{</span></div>
                <div class="code-line"><span class="ln">5</span>&nbsp;&nbsp;<span class="kw">public</span> <span class="ar">$name</span><span class="op">=</span><span class="st">'Baraa'</span><span class="op">;</span></div>
                <div class="code-line"><span class="ln">6</span>&nbsp;&nbsp;<span class="kw">public</span> <span class="ar">$exp</span>&nbsp;<span class="op">=</span><span class="st">'5+ years'</span><span class="op">;</span></div>
                <div class="code-line"><span class="ln">7</span></div>
                <div class="code-line"><span class="ln">8</span>&nbsp;&nbsp;<span class="kw">public function </span><span class="fn">stack</span><span class="op">(): array</span></div>
                <div class="code-line"><span class="ln">9</span>&nbsp;&nbsp;<span class="op">{</span></div>
                <div class="code-line"><span class="ln">10</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">return</span> <span class="op">[</span></div>
                <div class="code-line"><span class="ln">11</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="st">'Laravel'</span><span class="op">,</span><span class="st">'PHP'</span><span class="op">,</span></div>
                <div class="code-line"><span class="ln">12</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="st">'MySQL'</span><span class="op">,</span><span class="st">'Redis'</span><span class="op">,</span></div>
                <div class="code-line"><span class="ln">13</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="st">'REST APIs'</span><span class="op">,</span></div>
                <div class="code-line"><span class="ln">14</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="op">];</span></div>
                <div class="code-line"><span class="ln">15</span>&nbsp;&nbsp;<span class="op">}</span></div>
                <div class="code-line"><span class="ln">16</span><span class="op">}</span></div>
            </div>
            <div class="floating-card card-2"><i class="fas fa-star"></i><span>{{ __('Open to Work') }}</span></div>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-avatar-wrap reveal">
                <div class="about-photo-frame">
                    <div class="about-photo-glow"></div>
                    <img src="{{ asset('images/about-team.jpg') }}" alt="{{ $settings['hero_name'] ?? 'Profile' }}" class="about-photo" loading="lazy">
                    <div class="about-photo-scan"></div>
                    <span class="about-photo-corner tl"></span>
                    <span class="about-photo-corner br"></span>
                </div>
                <div class="avatar-badge"><i class="fas fa-code"></i> {{ __('Backend Dev') }}</div>
            </div>
            <div class="reveal">
                <div class="section-tag">{{ __('// who am I') }}</div>
                <h2>{!! ts($settings, 'about_heading') ?: ($settings['about_heading'] ?? 'About Me') !!}</h2>
                @php
                    $p1 = ts($settings, 'about_p1') ?: ($settings['about_p1'] ?? '');
                    $p2 = ts($settings, 'about_p2') ?: ($settings['about_p2'] ?? '');
                    $p3 = ts($settings, 'about_p3') ?: ($settings['about_p3'] ?? '');
                    $tagsRaw = ts($settings, 'about_tags') ?: ($settings['about_tags'] ?? '');
                @endphp
                @if($p1)<p>{{ $p1 }}</p>@endif
                @if($p2)<p>{{ $p2 }}</p>@endif
                @if($p3)<p>{{ $p3 }}</p>@endif
                @if($tagsRaw)
                <div class="about-tags">
                    @foreach(array_map('trim', explode(',', $tagsRaw)) as $tag)
                        <span class="tag">{{ $tag }}</span>
                    @endforeach
                </div>
                @endif
                <div class="about-links">
                    <a href="mailto:{{ $settings['email'] ?? '#' }}" title="Email"><i class="fas fa-envelope"></i></a>
                    <a href="{{ $settings['github_url'] ?? '#' }}" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="{{ $settings['linkedin_url'] ?? '#' }}" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="#" title="Download CV"><i class="fas fa-file-arrow-down"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SKILLS -->
<section id="skills">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-tag">{{ __('// expertise') }}</div>
            <h2 class="section-title">{{ __('Technical') }} <span>{{ __('Skills') }}</span></h2>
            <div class="section-line"></div>
        </div>
        @if(!empty($categories))
        <div class="skills-grid">
            @foreach($categories as $cat)
            <div class="skill-category reveal">
                <div class="skill-cat-icon">{{ $cat->icon }}</div>
                <div class="skill-cat-title">{{ t($cat, 'name') }}</div>
                @if($cat->type === 'bars')
                    <div class="skill-items">
                        @foreach($cat->skills as $skill)
                        <div class="skill-item">
                            <div class="skill-info">
                                <span class="skill-name">{{ t($skill, 'name') }}</span>
                                <span class="skill-pct">{{ $skill->percentage }}%</span>
                            </div>
                            <div class="skill-bar">
                                <div class="skill-fill" data-width="{{ $skill->percentage }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="tech-tags">
                        @foreach($cat->skills as $skill)
                            <span class="tech-tag">{{ t($skill, 'name') }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- EXPERIENCE -->
<section id="experience">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-tag">{{ __('// career') }}</div>
            <h2 class="section-title">{{ __('Work') }} <span>{{ __('Experience') }}</span></h2>
            <div class="section-line"></div>
        </div>
        <div class="timeline">
            @foreach($experiences as $exp)
            <div class="timeline-item reveal">
                <div class="timeline-dot"><i class="fas fa-arrow-trend-up"></i></div>
                <div class="timeline-card" data-step="{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}">
                    <div class="timeline-header">
                        <div>
                            <div class="timeline-kicker">{{ __('A chapter in my journey') }}</div>
                            <div class="timeline-title">{{ t($exp, 'title') }}</div>
                            <div class="timeline-company">{{ t($exp, 'company') }}</div>
                        </div>
                        <span class="timeline-date">{{ $exp->date_range }}</span>
                    </div>
                    <p class="timeline-desc">{{ t($exp, 'description') }}</p>
                    @if(!empty($exp->tags))
                    <div class="timeline-tags">
                        @foreach($exp->tags as $tag)
                            <span class="timeline-tag">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- PROJECTS -->
<section id="projects">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-tag">{{ __('// portfolio') }}</div>
            <h2 class="section-title">{{ __('Featured') }} <span>{{ __('Projects') }}</span></h2>
            <div class="section-line"></div>
        </div>
        <div class="projects-grid">
            @foreach($projects as $project)
            <div class="project-card-link reveal">
            <div class="project-card">
                <div class="project-cover cover-{{ (($loop->index) % 5) + 1 }}">
                    @php
                        $projectImage = $project->image ?? null;
                        $projectImageSrc = $projectImage ? project_image_url($projectImage) : asset('images/project-base.svg');
                    @endphp
                    <img src="{{ $projectImageSrc }}" alt="" class="project-base-image" loading="lazy">
                    <span class="project-cover-label">{{ __('PROJECT PREVIEW') }}</span>
                    <span class="project-cover-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="project-icon">{{ $project->icon }}</div>
                </div>
                <div class="project-header">
                    <div class="project-links">
                        @if($project->github_url)
                            <a href="{{ $project->github_url }}" target="_blank" class="project-link" title="GitHub" aria-label="GitHub"><i class="fab fa-github"></i></a>
                        @else
                            <span class="project-link" style="opacity:0.3"><i class="fab fa-github"></i></span>
                        @endif
                        @if($project->live_url)
                            <a href="{{ $project->live_url }}" target="_blank" class="project-link" title="{{ __('Visit it') }}" aria-label="{{ __('Visit it') }}"><i class="fas fa-arrow-up-right-from-square"></i></a>
                        @else
                            <span class="project-link" style="opacity:0.3"><i class="fas fa-arrow-up-right-from-square"></i></span>
                        @endif
                    </div>
                </div>
                <div class="project-body">
                    @if($project->featured)
                        <div class="featured-badge"><i class="fas fa-star"></i> {{ __('Featured') }}</div>
                    @endif
                    <div class="project-title">{{ t($project, 'title') }}</div>
                    <p class="project-desc">{{ t($project, 'description') }}</p>
                    @if(!empty($project->stack))
                    <div class="project-stack">
                        @foreach($project->stack as $tech)
                            <span class="stack-tag">{{ $tech }}</span>
                        @endforeach
                    </div>
                    @endif
                    <div class="project-actions">
                        <a href="{{ route('project.show', $project->id) }}" class="project-action primary">
                            {{ __('More details') }}
                            <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}"></i>
                        </a>
                        @if($project->live_url)
                            <a href="{{ $project->live_url }}" target="_blank" class="project-action">
                                {{ __('Visit it') }}
                                <i class="fas fa-arrow-up-right-from-square"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CONTACT -->
<section id="contact">
    <div class="container">
        <div class="contact-wrapper">
            <div class="contact-header reveal">
                <div class="section-tag">{{ __("// let's connect") }}</div>
                <h2 class="section-title">{{ __('Get In') }} <span>{{ __('Touch') }}</span></h2>
                <div class="section-line"></div>
                <p>
                    {{ __("I'm currently open to new opportunities. Whether you have a project in mind, want to collaborate, or just say hi — my inbox is always open.") }}
                </p>
            </div>
            <div class="contact-layout">
                <div class="contact-intro reveal">
                    <div>
                        <div class="section-tag">{{ __('Let’s make something useful') }}</div>
                        <h3>{{ __('Have an idea? Let’s talk about it.') }}</h3>
                        <p>{{ __('Tell me what you are building, what is getting in the way, or what you want to improve. I’ll get back to you with a clear next step.') }}</p>
                        <div class="contact-availability">{{ __('Usually replies within 1–2 days') }}</div>
                    </div>
                    <div class="contact-cards">
                        <a href="mailto:{{ $settings['email'] ?? '#' }}" class="contact-card">
                            <i class="fas fa-envelope"></i>
                            <span><span class="contact-card-label">{{ __('Email') }}</span><span class="contact-card-value">{{ $settings['email'] ?? __('Email me') }}</span></span>
                        </a>
                        <a href="{{ $settings['linkedin_url'] ?? '#' }}" target="_blank" class="contact-card">
                            <i class="fab fa-linkedin"></i>
                            <span><span class="contact-card-label">LinkedIn</span><span class="contact-card-value">{{ __('Connect with me') }}</span></span>
                        </a>
                        <a href="{{ $settings['github_url'] ?? '#' }}" target="_blank" class="contact-card">
                            <i class="fab fa-github"></i>
                            <span><span class="contact-card-label">GitHub</span><span class="contact-card-value">{{ __('View my code') }}</span></span>
                        </a>
                    </div>
                </div>
                <div class="contact-form reveal">
                <div class="contact-form-head">
                    <div>
                        <h3>{{ __('Send me a message') }}</h3>
                        <p>{{ __('No complicated forms — just the basics.') }}</p>
                    </div>
                    <div class="contact-form-mark"><i class="fas fa-paper-plane"></i></div>
                </div>
                <form id="contactForm" onsubmit="handleSubmit(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-input" placeholder="{{ __('Your name') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-input" placeholder="your@email.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Subject') }}</label>
                        <input type="text" class="form-input" placeholder="{{ __("What's this about?") }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Message') }}</label>
                        <textarea class="form-textarea" placeholder="{{ __('Tell me about your project...') }}" required></textarea>
                    </div>
                    <button type="submit" class="form-submit"><i class="fas fa-paper-plane"></i>&nbsp; {{ __('Send Message') }}</button>
                </form>
                </div>
            </div>
        </div>
    </div>
</section>

<footer>
    <p>{{ $settings['footer_text'] ?? 'Portfolio' }}</p>
</footer>

<a href="/admin" class="admin-link"><i class="fas fa-lock"></i> {{ __('CMS') }}</a>

<script>
// ─── UNIQUE CURSOR ───
const dot   = document.getElementById('cursor-dot');
const ring  = document.getElementById('cursor-ring');
const trailContainer = document.getElementById('cursor-trail-container');
const TRAIL_COUNT = 12;
const trails = [];

for (let i = 0; i < TRAIL_COUNT; i++) {
    const t = document.createElement('div');
    t.className = 'cursor-trail';
    t.style.opacity = (1 - i / TRAIL_COUNT) * 0.5;
    t.style.width  = (4 - i * 0.2) + 'px';
    t.style.height = (4 - i * 0.2) + 'px';
    trailContainer.appendChild(t);
    trails.push({ el: t, x: 0, y: 0 });
}

let mx = 0, my = 0;
let ringX = 0, ringY = 0;

document.addEventListener('mousemove', e => {
    mx = e.clientX; my = e.clientY;
    dot.style.left = mx + 'px';
    dot.style.top  = my + 'px';
});

document.addEventListener('mousedown', () => document.body.classList.add('cursor-click'));
document.addEventListener('mouseup',   () => document.body.classList.remove('cursor-click'));

document.querySelectorAll('a, button, .project-card, .skill-category, .contact-card, .nav-item, input, textarea').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
    el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
});

let prevTrailPositions = Array(TRAIL_COUNT).fill(null).map(() => ({ x: 0, y: 0 }));

function animateCursor() {
    // Smooth ring follow
    ringX += (mx - ringX) * 0.12;
    ringY += (my - ringY) * 0.12;
    ring.style.left = ringX + 'px';
    ring.style.top  = ringY + 'px';

    // Trail effect
    let px = mx, py = my;
    trails.forEach((t, i) => {
        const delay = 0.08 + i * 0.04;
        t.x += (px - t.x) * delay;
        t.y += (py - t.y) * delay;
        t.el.style.left    = t.x + 'px';
        t.el.style.top     = t.y + 'px';
        t.el.style.opacity = (1 - i / TRAIL_COUNT) * 0.35;
        const s = (4 - i * 0.25);
        t.el.style.width  = Math.max(s, 1) + 'px';
        t.el.style.height = Math.max(s, 1) + 'px';
        px = t.x; py = t.y;
    });

    requestAnimationFrame(animateCursor);
}
animateCursor();

// ─── THEME SWITCH ───
(function () {
    const root = document.documentElement;
    const btn = document.getElementById('themeToggleBtn');
    const menu = document.getElementById('themeMenu');
    const options = document.querySelectorAll('.theme-option');

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        try { localStorage.setItem('theme', theme); } catch (e) {}
        options.forEach(o => o.classList.toggle('active', o.dataset.themeChoice === theme));
    }

    applyTheme(root.getAttribute('data-theme') || 'light');

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('open');
    });
    options.forEach(o => o.addEventListener('click', () => {
        applyTheme(o.dataset.themeChoice);
        menu.classList.remove('open');
    }));
    document.addEventListener('click', (e) => {
        if (!document.getElementById('themeSwitch').contains(e.target)) {
            menu.classList.remove('open');
        }
    });
})();

// ─── NAV SCROLL ───
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
    // Active nav
    let current = '';
    document.querySelectorAll('section[id]').forEach(s => {
        if (window.scrollY >= s.offsetTop - 120) current = s.id;
    });
    document.querySelectorAll('.nav-links a[href^="#"]').forEach(a => {
        a.classList.toggle('active', a.getAttribute('href') === '#' + current);
    });
});

// ─── MOBILE NAV ───
const hamburger = document.getElementById('hamburger');
const navLinks  = document.getElementById('navLinks');
hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    hamburger.classList.toggle('open');
});
navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
    navLinks.classList.remove('open');
    hamburger.classList.remove('open');
}));

// ─── SCROLL REVEAL ───
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            entry.target.querySelectorAll('.skill-fill').forEach(bar => {
                bar.style.width = bar.dataset.width + '%';
            });
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ─── TYPING ROLE ───
const roleEl = document.getElementById('typing-role');
const roles  = [
    '{{ ts($settings, "hero_tagline") ?: ($settings["hero_tagline"] ?? "Backend Engineer") }}',
    '{{ __('Laravel Expert') }}',
    '{{ __('API Architect') }}',
    '{{ __('PHP Developer') }}'
];
let ri = 0, ci = roles[0].length, deleting = false;
roleEl.textContent = roles[0];
function typeRole() {
    const cur = roles[ri];
    if (!deleting) {
        roleEl.textContent = cur.slice(0, ++ci);
        if (ci === cur.length) { deleting = true; setTimeout(typeRole, 2200); return; }
    } else {
        roleEl.textContent = cur.slice(0, --ci);
        if (ci === 0) { deleting = false; ri = (ri + 1) % roles.length; }
    }
    setTimeout(typeRole, deleting ? 55 : 95);
}
setTimeout(typeRole, 2200);

// ─── FORM ───
function handleSubmit(e) {
    e.preventDefault();
    const btn = e.target.querySelector('.form-submit');
    btn.innerHTML = '<i class="fas fa-check"></i>&nbsp; {{ __('Message Sent!') }}';
    btn.style.background = 'linear-gradient(135deg,#28ca41,#00a832)';
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-paper-plane"></i>&nbsp; {{ __('Send Message') }}';
        btn.style.background = '';
        e.target.reset();
    }, 3000);
}
</script>
</body>
</html>
