@php
    $heroName = ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? 'Your Name');
    $heroTagline = ts($settings, 'hero_tagline') ?: ($settings['hero_tagline'] ?? 'Developer');
    $heroSubtitle = ts($settings, 'hero_subtitle') ?: ($settings['hero_subtitle'] ?? '');
    $firstName = explode(' ', $heroName)[0];
    $whatsappNumber = preg_replace('/\D+/', '', $settings['whatsapp_number'] ?? '');
    $contactEmail = $settings['email'] ?? '';
    $githubUrl = $settings['github_url'] ?? '';
    $linkedinUrl = $settings['linkedin_url'] ?? '';
    $techTags = array_values(array_filter(array_map('trim', explode(',', ts($settings, 'about_tags') ?: ($settings['about_tags'] ?? '')))));
    $techIcons = [
        'laravel' => 'fab fa-laravel', 'php' => 'fab fa-php', 'docker' => 'fab fa-docker',
        'git' => 'fab fa-git-alt', 'linux' => 'fab fa-linux', 'mysql' => 'fas fa-database',
        'postgres' => 'fas fa-database', 'redis' => 'fas fa-bolt', 'api' => 'fas fa-plug',
        'vue' => 'fab fa-vuejs', 'javascript' => 'fab fa-js', 'aws' => 'fab fa-aws', 'nginx' => 'fas fa-server',
    ];
    $techIcon = function (string $name) use ($techIcons): string {
        foreach ($techIcons as $needle => $icon) {
            if (str_contains(strtolower($name), $needle)) {
                return $icon;
            }
        }

        return 'fas fa-code';
    };
    $arrowIcon = $locale === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right';
    $cvPath = $settings['cv_path'] ?? '';
    $cvUrl = ($cvPath !== '' && file_exists(public_path($cvPath))) ? asset($cvPath) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo')
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('theme'); } catch (e) {}
            var theme = ['light', 'dark', 'ocean', 'sunset'].includes(saved) ? saved : 'light';
            document.documentElement.setAttribute('data-theme', theme);
            var fancyCursor = window.matchMedia('(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)').matches;
            if (fancyCursor) { document.documentElement.classList.add('custom-cursor'); }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@500;700&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preload" as="image" href="{{ asset('images/me.webp') }}" type="image/webp">
    <style>
        @include('partials.theme-tokens')

        /* ═════════ BASE ═════════ */
        * { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; scroll-padding-top:90px; }
        body { font-family:var(--font-body); background:var(--bg-primary); color:var(--text-primary); line-height:1.65; overflow-x:hidden; cursor:none; transition:background .35s ease, color .35s ease; -webkit-font-smoothing:antialiased; }
        img { max-width:100%; display:block; }
        ::selection { background:var(--cyan); color:var(--on-accent); }
        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:var(--bg-primary); }
        ::-webkit-scrollbar-thumb { background:var(--cyan-dark); border-radius:3px; }
        .container { max-width:1200px; margin:0 auto; padding-inline:1.5rem; }
        section { position:relative; z-index:1; padding:7.5rem 0; }
        .section-alt { background:var(--bg-secondary); }

        /* ═════════ CUSTOM CURSOR ═════════ */
        #cursor-dot { position:fixed; width:8px; height:8px; background:var(--cyan); border-radius:50%; pointer-events:none; z-index:99999; transform:translate(-50%,-50%); transition:width .2s, height .2s; box-shadow:0 0 10px var(--cyan); }
        #cursor-ring { position:fixed; width:36px; height:36px; border:1.5px solid var(--accent-line); border-radius:50%; pointer-events:none; z-index:99998; transform:translate(-50%,-50%); transition:width .3s, height .3s, border-color .3s; }
        #cursor-trail-container { position:fixed; top:0; left:0; pointer-events:none; z-index:99997; }
        .cursor-trail { position:fixed; width:4px; height:4px; background:var(--cyan); border-radius:50%; pointer-events:none; transform:translate(-50%,-50%); opacity:0; }
        body.cursor-hover #cursor-dot { width:12px; height:12px; }
        body.cursor-hover #cursor-ring { width:52px; height:52px; border-color:var(--cyan); }
        body.cursor-click #cursor-dot { width:5px; height:5px; }
        body.cursor-click #cursor-ring { width:24px; height:24px; }

        /* ═════════ SCROLL PROGRESS ═════════ */
        .scroll-progress { position:fixed; top:0; left:0; right:0; height:3px; background:var(--gradient); transform-origin:0 50%; transform:scaleX(0); z-index:1200; }
        [dir="rtl"] .scroll-progress { transform-origin:100% 50%; }

        /* ═════════ NAV ═════════ */
        nav { position:fixed; top:0; left:0; right:0; z-index:1000; padding:1rem 1.25rem 0; transition:padding .35s ease; }
        nav.scrolled { padding-top:.6rem; }
        .nav-inner { max-width:1180px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; height:64px; gap:1rem; padding-inline:1.25rem .6rem; background:var(--nav-bg); backdrop-filter:blur(18px) saturate(1.4); -webkit-backdrop-filter:blur(18px) saturate(1.4); border:1px solid var(--border); border-radius:999px; box-shadow:var(--shadow-sm); position:relative; transition:box-shadow .35s ease; }
        nav.scrolled .nav-inner { box-shadow:var(--shadow-md); }
        .nav-logo { display:flex; align-items:center; gap:.6rem; font:800 1.05rem var(--font-head); color:var(--text-primary); text-decoration:none; flex-shrink:0; }
        .nav-logo img { width:38px; height:38px; border-radius:50%; object-fit:cover; object-position:50% 25%; border:2px solid var(--cyan); }
        .nav-logo-text { display:none; }
        .nav-logo-text span { color:var(--cyan); }
        @media(min-width:480px){ .nav-logo-text { display:inline; } }
        .nav-links { display:flex; align-items:center; gap:.1rem; list-style:none; padding:.3rem; border-radius:999px; background:color-mix(in srgb, var(--text-primary) 4%, transparent); }
        .nav-links a { display:block; color:var(--text-secondary); text-decoration:none; font-size:.85rem; font-weight:600; padding:.5rem .95rem; border-radius:999px; position:relative; transition:color .25s, background .25s; }
        .nav-links a:not(.nav-cta):hover { color:var(--text-primary); }
        .nav-links a.active:not(.nav-cta) { color:var(--text-primary); background:var(--bg-card); box-shadow:var(--shadow-sm); }
        .nav-cta { margin-inline-start:.35rem; background:var(--gradient); color:var(--on-accent) !important; font-weight:700; display:flex !important; align-items:center; gap:.45rem; }
        .nav-cta:hover { filter:brightness(1.08); }
        .nav-cta i { font-size:.72rem; }
        .nav-controls { display:flex; align-items:center; gap:.45rem; flex-shrink:0; }
        .hamburger { display:none; background:none; border:none; padding:0; flex-direction:column; gap:5px; cursor:none; width:40px; height:40px; align-items:center; justify-content:center; border-radius:50%; transition:background .2s; }
        .hamburger:hover { background:var(--accent-soft); }
        .hamburger span { display:block; width:18px; height:2px; background:var(--text-primary); border-radius:2px; transition:all .3s; }
        .hamburger.open span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity:0; }
        .hamburger.open span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }
        .lang-switch { display:flex; align-items:center; gap:.15rem; background:color-mix(in srgb, var(--text-primary) 5%, transparent); border:1px solid var(--border); border-radius:999px; padding:.2rem; }
        .lang-btn { padding:.32rem .65rem; border-radius:999px; font-size:.72rem; font-weight:700; text-decoration:none; color:var(--text-secondary); transition:all .2s; cursor:none; }
        .lang-btn.active { background:var(--gradient); color:var(--on-accent); }
        .lang-btn:not(.active):hover { color:var(--cyan); }
        .theme-switch { position:relative; }
        .theme-toggle-btn { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; background:color-mix(in srgb, var(--text-primary) 5%, transparent); border:1px solid var(--border); color:var(--cyan); cursor:none; font-size:.85rem; transition:all .2s; }
        .theme-toggle-btn:hover { background:var(--accent-soft); border-color:var(--accent-line); }
        .theme-menu { position:absolute; top:calc(100% + 14px); inset-inline-end:0; background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:.45rem; display:flex; flex-direction:column; gap:.15rem; min-width:165px; box-shadow:var(--shadow-lg); opacity:0; visibility:hidden; transform:translateY(-8px) scale(.97); transition:all .2s; z-index:1100; }
        .theme-menu.open { opacity:1; visibility:visible; transform:none; }
        .theme-option { display:flex; align-items:center; gap:.6rem; padding:.6rem .7rem; border-radius:10px; cursor:none; font-size:.85rem; color:var(--text-secondary); background:transparent; border:none; text-align:start; width:100%; font-family:inherit; transition:all .15s; }
        .theme-option:hover { background:var(--accent-soft); color:var(--text-primary); }
        .theme-option.active { color:var(--cyan); font-weight:700; }
        .theme-swatch { width:16px; height:16px; border-radius:50%; flex-shrink:0; border:1px solid rgba(127,127,127,.3); }
        .sw-light { background:linear-gradient(135deg,#f5f7fb,#00759c); }
        .sw-dark { background:linear-gradient(135deg,#0a0e17,#00d4d4); }
        .sw-ocean { background:linear-gradient(135deg,#031824,#20e3c2); }
        .sw-sunset { background:linear-gradient(135deg,#1a0f1a,#ff7a59); }

        /* ═════════ SHARED COMPONENTS ═════════ */
        .section-head { text-align:center; max-width:680px; margin:0 auto 4rem; }
        .eyebrow { display:inline-flex; align-items:center; gap:.5rem; padding:.4rem .95rem; border-radius:999px; background:var(--accent-soft); border:1px solid var(--accent-line); color:var(--cyan); font-size:.76rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:1.1rem; }
        .eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
        .section-title { font-family:var(--font-head); font-size:clamp(2rem,4.4vw,3rem); font-weight:800; line-height:1.15; letter-spacing:-.025em; }
        .grad { background:var(--gradient); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
        .section-intro { margin-top:1.1rem; color:var(--text-secondary); font-size:1.05rem; line-height:1.8; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.6rem; min-height:50px; padding:0 1.6rem; border-radius:var(--radius-sm); font:700 .95rem var(--font-body); text-decoration:none; border:1px solid transparent; transition:transform .25s var(--ease), box-shadow .25s, background .25s, color .25s, border-color .25s; cursor:none; white-space:nowrap; }
        .btn-primary { background:var(--gradient); color:var(--on-accent); box-shadow:0 10px 26px -8px color-mix(in srgb, var(--cyan) 70%, transparent); }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 16px 34px -10px color-mix(in srgb, var(--cyan) 80%, transparent); }
        .btn-ghost { background:var(--glass); color:var(--text-primary); border-color:var(--border); backdrop-filter:blur(8px); }
        .btn-ghost:hover { border-color:var(--cyan); color:var(--cyan); transform:translateY(-2px); }
        .btn i { font-size:.85rem; transition:transform .25s; }
        .btn:hover .fa-arrow-right { transform:translateX(3px); }
        .btn:hover .fa-arrow-left { transform:translateX(-3px); }
        .chip { display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .8rem; border-radius:999px; background:var(--accent-soft); border:1px solid var(--accent-line); color:var(--cyan); font:500 .78rem var(--font-mono); }
        .chip-muted { background:color-mix(in srgb, var(--text-primary) 4%, transparent); border-color:var(--border); color:var(--text-secondary); }
        .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); position:relative; transition:transform .35s var(--ease), box-shadow .35s, border-color .35s; }
        .card-hover:hover { transform:translateY(-6px); border-color:var(--accent-line); box-shadow:var(--shadow-lg); }
        .icon-tile { width:54px; height:54px; border-radius:16px; background:var(--gradient); color:var(--on-accent); display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; box-shadow:0 10px 24px -8px color-mix(in srgb, var(--cyan) 70%, transparent); }
        .icon-tile.soft { background:var(--accent-soft); color:var(--cyan); box-shadow:none; border:1px solid var(--accent-line); }
        .pulse-dot { width:9px; height:9px; border-radius:50%; background:#22c55e; box-shadow:0 0 0 0 rgba(34,197,94,.55); animation:pulse 2s infinite; flex-shrink:0; }
        @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(34,197,94,.55)} 70%{box-shadow:0 0 0 9px rgba(34,197,94,0)} 100%{box-shadow:0 0 0 0 rgba(34,197,94,0)} }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .reveal { opacity:0; transform:translateY(26px); transition:opacity .8s var(--ease) var(--d,0s), transform .8s var(--ease) var(--d,0s); }
        .reveal.visible { opacity:1; transform:none; }

        /* ═════════ HERO ═════════ */
        #hero { min-height:100svh; display:flex; flex-direction:column; justify-content:center; padding:130px 0 70px; overflow:hidden; }
        .hero-bg { position:absolute; inset:0; z-index:-1; pointer-events:none; }
        .hero-bg .blob { position:absolute; border-radius:50%; filter:blur(70px); opacity:.5; }
        .hero-bg .b1 { width:520px; height:520px; background:color-mix(in srgb, var(--cyan) 35%, transparent); top:-120px; inset-inline-end:-120px; }
        .hero-bg .b2 { width:420px; height:420px; background:color-mix(in srgb, #2563eb 25%, transparent); bottom:-160px; inset-inline-start:-140px; }
        .hero-grid { position:absolute; inset:0; background-image:linear-gradient(color-mix(in srgb, var(--text-primary) 5%, transparent) 1px, transparent 1px), linear-gradient(90deg, color-mix(in srgb, var(--text-primary) 5%, transparent) 1px, transparent 1px); background-size:64px 64px; mask-image:radial-gradient(ellipse 70% 60% at 50% 40%, #000 30%, transparent 75%); -webkit-mask-image:radial-gradient(ellipse 70% 60% at 50% 40%, #000 30%, transparent 75%); }
        .hero-inner { display:grid; grid-template-columns:1.1fr .9fr; gap:4rem; align-items:center; }
        .hero-badge { display:inline-flex; align-items:center; gap:.6rem; padding:.45rem 1rem .45rem .8rem; border-radius:999px; background:var(--glass); border:1px solid var(--border); box-shadow:var(--shadow-sm); font-size:.85rem; font-weight:600; color:var(--text-secondary); margin-bottom:1.6rem; backdrop-filter:blur(8px); }
        .hero-hello { font-size:1.15rem; font-weight:600; color:var(--text-secondary); margin-bottom:.35rem; }
        .hero-title { font-family:var(--font-head); font-size:clamp(2.6rem,6vw,4.4rem); font-weight:800; line-height:1.05; letter-spacing:-.035em; margin-bottom:1.3rem; }
        .hero-title .name { display:block; background:var(--heading-gradient); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; padding-bottom:.08em; }
        .hero-title .role { display:block; font-size:.62em; letter-spacing:-.02em; background:var(--gradient); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; min-height:1.25em; margin-top:.35rem; }
        .hero-title .role::after { content:''; display:inline-block; width:3px; height:.9em; margin-inline-start:4px; background:var(--cyan); vertical-align:-.1em; animation:blink 1s steps(1) infinite; }
        @keyframes blink { 50%{opacity:0} }
        .hero-subtitle { font-size:1.1rem; color:var(--text-secondary); line-height:1.8; max-width:560px; margin-bottom:2.2rem; }
        .hero-subtitle strong { color:var(--text-primary); font-weight:700; }
        .hero-actions { display:flex; gap:.85rem; flex-wrap:wrap; }
        .hero-promises { list-style:none; display:flex; flex-wrap:wrap; gap:.6rem 1.4rem; margin-top:1.6rem; }
        .hero-promises li { display:flex; align-items:center; gap:.45rem; font-size:.9rem; color:var(--text-secondary); font-weight:500; }
        .hero-promises i { color:var(--cyan); }
        .hero-social { display:flex; align-items:center; gap:.6rem; margin-top:2rem; }
        .hero-social-label { font-size:.85rem; color:var(--text-muted); font-weight:600; margin-inline-end:.3rem; }
        .social-btn { width:44px; height:44px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; color:var(--text-secondary); background:var(--glass); border:1px solid var(--border); text-decoration:none; transition:all .25s; cursor:none; }
        .social-btn:hover { color:var(--cyan); border-color:var(--accent-line); transform:translateY(-3px); }

        /* Portrait */
        .hero-visual { display:flex; justify-content:center; }
        .portrait { position:relative; width:min(420px,100%); aspect-ratio:4/5; }
        .portrait-glow { position:absolute; inset:-12%; background:radial-gradient(circle at 50% 45%, color-mix(in srgb, var(--cyan) 40%, transparent), transparent 62%); filter:blur(30px); z-index:-1; }
        .portrait-dots { position:absolute; width:150px; height:150px; top:-28px; inset-inline-end:-34px; background-image:radial-gradient(var(--cyan) 1.6px, transparent 1.6px); background-size:15px 15px; opacity:.4; z-index:-1; }
        .portrait-shape { position:absolute; bottom:-18px; inset-inline-end:-18px; width:62%; height:62%; border-radius:var(--radius-lg); border:2px dashed var(--accent-line); z-index:-1; }
        .portrait-frame { position:relative; height:100%; border-radius:36px; padding:4px; background:var(--gradient); box-shadow:var(--shadow-lg); }
        .portrait-frame picture, .portrait-frame img { width:100%; height:100%; }
        .portrait-frame img { object-fit:cover; object-position:50% 22%; border-radius:32px; }
        .float-card { position:absolute; display:flex; align-items:center; gap:.7rem; padding:.75rem 1rem; border-radius:18px; background:var(--glass); backdrop-filter:blur(14px) saturate(1.3); -webkit-backdrop-filter:blur(14px) saturate(1.3); border:1px solid var(--border); box-shadow:var(--shadow-md); animation:float 5s ease-in-out infinite; white-space:nowrap; }
        .float-card strong { display:block; font:800 1rem var(--font-head); line-height:1.2; }
        .float-card small { display:block; font-size:.75rem; color:var(--text-secondary); }
        .float-card .icon-tile { width:40px; height:40px; border-radius:12px; font-size:1.05rem; }
        .fc-1 { top:9%; inset-inline-start:-54px; }
        .fc-2 { bottom:22%; inset-inline-end:-46px; animation-delay:1.6s; }
        .fc-3 { bottom:-20px; left:50%; translate:-50% 0; animation-delay:.8s; font-weight:700; font-size:.88rem; }

        /* Stats bar */
        .hero-stats { display:grid; grid-template-columns:repeat(3,1fr); margin-top:4.5rem; background:var(--glass); backdrop-filter:blur(12px); border:1px solid var(--border); border-radius:var(--radius-md); box-shadow:var(--shadow-sm); }
        .stat-item { display:flex; align-items:center; justify-content:center; gap:1rem; padding:1.4rem 1rem; }
        .stat-item + .stat-item { border-inline-start:1px solid var(--border); }
        .stat-number { font:800 2rem var(--font-head); line-height:1; background:var(--gradient); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
        .stat-label { font-size:.85rem; color:var(--text-secondary); font-weight:600; line-height:1.35; }

        /* ═════════ TECH MARQUEE ═════════ */
        .tech-strip { padding:2.5rem 0; border-block:1px solid var(--border); background:var(--bg-secondary); overflow:hidden; }
        .tech-strip-label { text-align:center; font-size:.8rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--text-muted); margin-bottom:1.3rem; }
        .marquee { display:flex; overflow:hidden; mask-image:linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent); -webkit-mask-image:linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent); }
        .marquee-track { display:flex; gap:1rem; padding-inline-end:1rem; flex-shrink:0; animation:marquee 32s linear infinite; }
        .marquee:hover .marquee-track { animation-play-state:paused; }
        [dir="rtl"] .marquee-track { animation-name:marquee-rtl; }
        @keyframes marquee { to { transform:translateX(-100%); } }
        @keyframes marquee-rtl { to { transform:translateX(100%); } }
        .tech-item { display:inline-flex; align-items:center; gap:.6rem; padding:.7rem 1.2rem; border-radius:14px; background:var(--bg-card); border:1px solid var(--border); font-weight:600; font-size:.95rem; color:var(--text-primary); white-space:nowrap; }
        .tech-item i { color:var(--cyan); font-size:1.15rem; }

        /* ═════════ SERVICES ═════════ */
        .services-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr)); gap:1.5rem; }
        .service-card { padding:2.3rem; display:flex; flex-direction:column; overflow:hidden; }
        .service-card::before { content:''; position:absolute; inset:0; border-radius:inherit; padding:1px; background:var(--gradient); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; opacity:0; transition:opacity .35s; pointer-events:none; }
        .service-card:hover::before { opacity:1; }
        .service-card::after { content:''; position:absolute; width:260px; height:260px; top:-130px; inset-inline-end:-130px; background:radial-gradient(circle, color-mix(in srgb, var(--cyan) 16%, transparent), transparent 70%); pointer-events:none; }
        .service-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; }
        .service-number { font:800 2.6rem/1 var(--font-mono); color:color-mix(in srgb, var(--text-primary) 8%, transparent); }
        .service-title { font:800 1.35rem/1.35 var(--font-head); margin-bottom:.8rem; }
        .service-summary { color:var(--text-secondary); line-height:1.8; margin-bottom:1.5rem; }
        .service-includes-label { font-size:.75rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--text-muted); margin-bottom:.8rem; }
        .service-includes { list-style:none; display:grid; gap:.65rem; margin-bottom:2rem; flex:1; }
        .service-includes li { display:flex; align-items:flex-start; gap:.65rem; font-size:.95rem; }
        .service-includes i { flex-shrink:0; width:21px; height:21px; border-radius:50%; background:var(--accent-soft); color:var(--cyan); font-size:.6rem; display:flex; align-items:center; justify-content:center; margin-top:.2rem; }
        .service-card .btn { width:100%; }
        .services-help { margin-top:1.75rem; display:flex; align-items:center; gap:1.25rem; padding:1.5rem 1.75rem; border-radius:var(--radius-md); border:1px dashed var(--accent-line); background:var(--accent-soft); }
        .services-help-text { flex:1; display:flex; flex-direction:column; gap:.2rem; }
        .services-help-text strong { font:800 1.05rem var(--font-head); }
        .services-help-text span { color:var(--text-secondary); font-size:.95rem; }

        /* ═════════ WHO AM I (BENTO) ═════════ */
        .bento { display:grid; grid-template-columns:repeat(3,1fr); grid-template-areas:"story story values" "story story status" "tools tools tools"; gap:1.25rem; }
        .bento-card { padding:2rem; overflow:hidden; }
        .bento-story { grid-area:story; padding:2.6rem; }
        .bento-values { grid-area:values; }
        .bento-status { grid-area:status; background:var(--gradient); border-color:transparent; color:var(--on-accent); }
        .bento-tools { grid-area:tools; }
        .story-head { display:flex; align-items:center; gap:1rem; margin-bottom:1.6rem; }
        .story-head img { width:64px; height:64px; border-radius:18px; object-fit:cover; object-position:50% 22%; border:2px solid var(--accent-line); }
        .story-head strong { display:block; font:800 1.1rem var(--font-head); }
        .story-head small { color:var(--cyan); font-weight:600; font-size:.88rem; }
        .bento-story h3 { font:800 clamp(1.5rem,2.6vw,2rem)/1.3 var(--font-head); letter-spacing:-.02em; margin-bottom:1.2rem; }
        .bento-story p { color:var(--text-secondary); line-height:1.85; margin-bottom:1rem; font-size:1.02rem; }
        .bento-story p:first-of-type { color:var(--text-primary); font-size:1.1rem; }
        .story-actions { display:flex; flex-wrap:wrap; align-items:center; gap:.75rem; margin-top:1.8rem; padding-top:1.6rem; border-top:1px solid var(--border); }
        .bento-title { font:800 1.1rem var(--font-head); margin-bottom:1.2rem; display:flex; align-items:center; gap:.6rem; }
        .bento-title i { color:var(--cyan); }
        .values-list { list-style:none; display:grid; gap:1.1rem; }
        .values-list li { display:flex; gap:.85rem; }
        .values-list .icon-tile { width:40px; height:40px; border-radius:12px; font-size:.95rem; }
        .values-list strong { display:block; font-size:.98rem; margin-bottom:.1rem; }
        .values-list span { font-size:.9rem; color:var(--text-secondary); line-height:1.6; }
        .bento-status { display:flex; flex-direction:column; justify-content:space-between; gap:1.4rem; }
        .status-top { display:flex; align-items:center; gap:.6rem; font-weight:700; font-size:.92rem; }
        .bento-status h4 { font:800 1.35rem/1.3 var(--font-head); }
        .bento-status p { opacity:.9; font-size:.95rem; }
        .bento-status .btn { background:var(--bg-card); color:var(--text-primary); align-self:flex-start; min-height:46px; }
        .bento-status .btn:hover { transform:translateY(-2px); }
        .tools-wrap { display:flex; flex-wrap:wrap; gap:.6rem; }
        .tools-wrap .tech-item { padding:.55rem 1rem; font-size:.9rem; background:var(--bg-primary); }

        /* ═════════ PROCESS ═════════ */
        .process-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.25rem; }
        .process-step { padding:1.8rem; }
        .process-step-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.4rem; }
        .process-num { font:800 2.2rem/1 var(--font-mono); background:var(--gradient); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
        .process-step h3 { font:800 1.1rem var(--font-head); margin-bottom:.5rem; }
        .process-step p { color:var(--text-secondary); font-size:.95rem; line-height:1.75; }
        .process-step .icon-tile { width:46px; height:46px; font-size:1rem; }
        .process-step:not(:last-child)::after { content:''; position:absolute; top:3.1rem; inset-inline-end:-1.25rem; width:1.25rem; height:2px; background:var(--accent-line); }
        .guarantees { margin-top:2rem; display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
        .guarantee { display:flex; align-items:center; gap:.75rem; padding:1rem 1.15rem; border-radius:var(--radius-sm); background:var(--bg-card); border:1px solid var(--border); font-size:.9rem; font-weight:600; }
        .guarantee i { color:var(--cyan); font-size:1.05rem; flex-shrink:0; }

        /* ═════════ PROJECTS ═════════ */
        .projects-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(min(100%,340px),1fr)); gap:1.5rem; }
        .project-card { display:flex; flex-direction:column; overflow:hidden; height:100%; }
        .project-cover { position:relative; aspect-ratio:16/10; overflow:hidden; background:var(--gradient); }
        .project-cover img { width:100%; height:100%; object-fit:cover; transition:transform .7s var(--ease); }
        .project-cover.no-image img { opacity:.35; mix-blend-mode:screen; }
        .project-card:hover .project-cover img { transform:scale(1.06); }
        .project-cover::after { content:''; position:absolute; inset:0; background:linear-gradient(to top, rgba(2,6,23,.55), transparent 55%); }
        .project-emoji { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:2.6rem; z-index:1; }
        .project-badges { position:absolute; top:1rem; inset-inline:1rem; display:flex; justify-content:space-between; gap:.5rem; z-index:2; }
        .project-badge { padding:.35rem .75rem; border-radius:999px; background:rgba(2,6,23,.55); backdrop-filter:blur(8px); color:#fff; font-size:.75rem; font-weight:700; display:inline-flex; align-items:center; gap:.35rem; }
        .project-badge.featured { background:rgba(250,204,21,.92); color:#1f1600; }
        .project-body { padding:1.6rem; display:flex; flex-direction:column; flex:1; }
        .project-title { font:800 1.2rem/1.35 var(--font-head); margin-bottom:.6rem; transition:color .25s; }
        .project-card:hover .project-title { color:var(--cyan); }
        .project-desc { color:var(--text-secondary); font-size:.95rem; line-height:1.75; margin-bottom:1.2rem; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
        .project-stack { display:flex; flex-wrap:wrap; gap:.4rem; margin-bottom:1.4rem; }
        .project-stack .chip { font-size:.72rem; padding:.28rem .65rem; }
        .project-actions { display:flex; align-items:center; gap:.6rem; margin-top:auto; padding-top:1.2rem; border-top:1px solid var(--border); }
        .project-actions .btn { min-height:44px; padding:0 1.1rem; font-size:.85rem; border-radius:12px; }
        .project-actions .btn-primary { flex:1; }
        .icon-btn { width:44px; height:44px; flex-shrink:0; border-radius:12px; border:1px solid var(--border); display:inline-flex; align-items:center; justify-content:center; color:var(--text-secondary); text-decoration:none; transition:all .25s; cursor:none; }
        .icon-btn:hover { color:var(--cyan); border-color:var(--accent-line); }

        /* ═════════ SKILLS ═════════ */
        .skills-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,270px),1fr)); gap:1.25rem; }
        .skill-category { padding:1.9rem; }
        .skill-cat-head { display:flex; align-items:center; gap:.85rem; margin-bottom:1.5rem; }
        .skill-cat-head .icon-tile { width:46px; height:46px; font-size:1.3rem; }
        .skill-cat-title { font:800 1.05rem var(--font-head); }
        .skill-items { display:flex; flex-direction:column; gap:1rem; }
        .skill-info { display:flex; justify-content:space-between; margin-bottom:.45rem; font-size:.92rem; }
        .skill-name { color:var(--text-primary); font-weight:500; }
        .skill-pct { font:700 .78rem var(--font-mono); color:var(--cyan); }
        .skill-bar { height:6px; background:color-mix(in srgb, var(--text-primary) 7%, transparent); border-radius:99px; overflow:hidden; }
        .skill-fill { height:100%; width:0; background:var(--gradient); border-radius:99px; transition:width 1.4s var(--ease); }
        .tech-tags { display:flex; flex-wrap:wrap; gap:.5rem; }

        /* ═════════ EXPERIENCE ═════════ */
        .timeline { position:relative; max-width:900px; margin:0 auto; }
        .timeline::before { content:''; position:absolute; inset-inline-start:23px; top:10px; bottom:10px; width:2px; background:linear-gradient(to bottom, var(--cyan), var(--accent-line) 70%, transparent); }
        .timeline-item { position:relative; padding-inline-start:76px; margin-bottom:1.5rem; }
        .timeline-dot { position:absolute; inset-inline-start:0; top:1.75rem; width:48px; height:48px; border-radius:16px; background:var(--bg-card); border:1px solid var(--accent-line); color:var(--cyan); display:flex; align-items:center; justify-content:center; box-shadow:0 0 0 6px var(--bg-secondary); transition:all .3s; }
        .timeline-item.current .timeline-dot { background:var(--gradient); color:var(--on-accent); border-color:transparent; }
        .timeline-card { padding:1.8rem 2rem; }
        .timeline-header { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap; margin-bottom:1rem; }
        .timeline-title { font:800 1.2rem var(--font-head); }
        .timeline-company { color:var(--cyan); font-weight:600; margin-top:.2rem; }
        .timeline-date { font:600 .78rem var(--font-mono); color:var(--text-secondary); padding:.4rem .8rem; border-radius:999px; background:color-mix(in srgb, var(--text-primary) 5%, transparent); border:1px solid var(--border); white-space:nowrap; }
        .timeline-desc { color:var(--text-secondary); line-height:1.85; margin-bottom:1.1rem; }
        .timeline-tags { display:flex; flex-wrap:wrap; gap:.4rem; }

        /* ═════════ CONTACT ═════════ */
        .contact-shell { display:grid; grid-template-columns:.9fr 1.1fr; border-radius:var(--radius-lg); overflow:hidden; border:1px solid var(--border); background:var(--bg-card); box-shadow:var(--shadow-lg); }
        .contact-aside { position:relative; padding:2.6rem; background:var(--gradient); color:var(--on-accent); display:flex; flex-direction:column; gap:1.8rem; overflow:hidden; }
        .contact-aside::before { content:''; position:absolute; width:340px; height:340px; border-radius:50%; border:50px solid rgba(255,255,255,.08); bottom:-150px; inset-inline-end:-120px; }
        .contact-person { display:flex; align-items:center; gap:.9rem; position:relative; }
        .contact-person img { width:58px; height:58px; border-radius:50%; object-fit:cover; object-position:50% 22%; border:3px solid rgba(255,255,255,.4); }
        .contact-person strong { display:block; font:800 1.05rem var(--font-head); }
        .contact-person span { font-size:.85rem; opacity:.9; display:flex; align-items:center; gap:.45rem; }
        .contact-aside h3 { font:800 1.5rem/1.3 var(--font-head); position:relative; }
        .next-steps { list-style:none; display:grid; gap:1rem; position:relative; }
        .next-steps li { display:flex; align-items:flex-start; gap:.8rem; font-size:.97rem; line-height:1.6; }
        .next-steps li > span { flex-shrink:0; width:28px; height:28px; border-radius:50%; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font:800 .8rem var(--font-mono); }
        .contact-links { display:grid; gap:.6rem; position:relative; margin-top:auto; }
        .contact-link { display:flex; align-items:center; gap:.8rem; padding:.8rem 1rem; border-radius:14px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); color:inherit; text-decoration:none; transition:background .25s; cursor:none; min-height:48px; }
        .contact-link:hover { background:rgba(255,255,255,.22); }
        .contact-link i { width:20px; text-align:center; }
        .contact-link small { display:block; font-size:.72rem; opacity:.85; }
        .contact-link b { font-size:.9rem; word-break:break-all; }
        .contact-form { padding:2.6rem; }
        .contact-form-head h3 { font:800 1.35rem var(--font-head); }
        .contact-form-head p { color:var(--text-muted); font-size:.9rem; margin-top:.3rem; margin-bottom:1.8rem; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .form-group { margin-bottom:1.15rem; }
        .form-label { display:block; font-size:.85rem; font-weight:700; color:var(--text-primary); margin-bottom:.5rem; }
        .form-label small { color:var(--text-muted); font-weight:500; }
        .form-input, .form-textarea { width:100%; min-height:50px; padding:.8rem 1rem; background:var(--bg-primary); border:1px solid var(--border); border-radius:12px; color:var(--text-primary); font:1rem var(--font-body); transition:border-color .2s, box-shadow .2s; outline:none; cursor:none; }
        .form-input:focus, .form-textarea:focus { border-color:var(--cyan); box-shadow:0 0 0 4px var(--accent-soft); }
        .form-textarea { min-height:140px; resize:vertical; }
        select.form-input { appearance:none; -webkit-appearance:none; background-image:linear-gradient(45deg,transparent 50%,var(--text-muted) 50%),linear-gradient(135deg,var(--text-muted) 50%,transparent 50%); background-position:calc(100% - 20px) 52%,calc(100% - 15px) 52%; background-size:5px 5px; background-repeat:no-repeat; padding-inline-end:2.4rem; }
        [dir="rtl"] select.form-input { background-position:20px 52%,15px 52%; }
        .form-actions { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:.75rem; margin-top:.4rem; }
        .form-actions .btn { width:100%; }
        .form-actions .btn[disabled] { opacity:.7; cursor:progress; transform:none; }
        .form-input.is-invalid, .form-textarea.is-invalid { border-color:#dc2626; box-shadow:0 0 0 4px rgba(220,38,38,.12); }
        .field-error { min-height:0; margin-top:.35rem; font-size:.82rem; font-weight:600; color:#dc2626; }
        .field-error:empty { display:none; }
        [data-theme]:not([data-theme="light"]) .field-error { color:#f87171; }
        .form-status { display:flex; align-items:flex-start; gap:.6rem; padding:.9rem 1rem; margin-bottom:1.2rem; border-radius:12px; font-size:.92rem; font-weight:600; line-height:1.5; }
        .form-status i { margin-top:.2rem; }
        .form-status.is-success { background:rgba(34,197,94,.12); color:#15803d; border:1px solid rgba(34,197,94,.35); }
        .form-status.is-error { background:rgba(220,38,38,.1); color:#b91c1c; border:1px solid rgba(220,38,38,.3); }
        [data-theme]:not([data-theme="light"]) .form-status.is-success { color:#4ade80; }
        [data-theme]:not([data-theme="light"]) .form-status.is-error { color:#f87171; }
        .form-status[hidden] { display:none; }
        .hp-field { position:absolute !important; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; }

        /* ═════════ FOOTER ═════════ */
        footer { background:var(--bg-secondary); border-top:1px solid var(--border); padding:3.5rem 0 2rem; }
        .footer-top { display:flex; justify-content:space-between; align-items:flex-start; gap:2rem; flex-wrap:wrap; padding-bottom:2rem; border-bottom:1px solid var(--border); }
        .footer-brand { max-width:340px; }
        .footer-brand .nav-logo { margin-bottom:.8rem; }
        .footer-brand p { color:var(--text-secondary); font-size:.95rem; }
        .footer-links { display:flex; flex-wrap:wrap; gap:.4rem 1.6rem; list-style:none; }
        .footer-links a { color:var(--text-secondary); text-decoration:none; font-weight:600; font-size:.92rem; transition:color .2s; display:inline-block; padding:.5rem 0; }
        .footer-links a:hover { color:var(--cyan); }
        .footer-social { display:flex; gap:.5rem; }
        .footer-bottom { padding-top:1.6rem; text-align:center; color:var(--text-muted); font-size:.88rem; }

        /* ═════════ CV ═════════ */
        .cv-chip { display:inline-flex; align-items:center; gap:.5rem; min-height:44px; padding:0 1rem; border-radius:12px; background:var(--accent-soft); border:1px solid var(--accent-line); color:var(--cyan); font-weight:700; font-size:.88rem; text-decoration:none; transition:all .25s; cursor:none; }
        .cv-chip:hover { background:var(--gradient); color:var(--on-accent); border-color:transparent; transform:translateY(-2px); }
        .hero-social-divider { width:1px; height:26px; background:var(--border); margin-inline:.35rem; }
        .cv-modal { width:min(1000px, 94vw); height:min(92vh, 1200px); max-width:none; max-height:none; margin:auto; padding:0; border:1px solid var(--border); border-radius:var(--radius-lg); background:var(--bg-card); color:var(--text-primary); box-shadow:var(--shadow-lg); overflow:hidden; }
        .cv-modal[open] { display:flex; flex-direction:column; animation:cv-in .35s var(--ease); }
        .cv-modal::backdrop { background:rgba(2,6,23,.6); backdrop-filter:blur(6px); }
        .cv-modal, .cv-modal * { cursor:auto; }
        .cv-modal :is(a, button) { cursor:pointer; }
        .cv-modal-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 1.25rem; border-bottom:1px solid var(--border); }
        .cv-modal-title { display:flex; align-items:center; gap:.8rem; min-width:0; }
        .cv-modal-title .icon-tile { width:44px; height:44px; font-size:1.05rem; }
        .cv-modal-title h2 { font:800 1.1rem var(--font-head); }
        .cv-modal-title small { display:block; color:var(--text-muted); font-size:.82rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .cv-modal-actions { display:flex; align-items:center; gap:.5rem; flex-shrink:0; }
        .cv-modal-actions .btn { min-height:44px; padding:0 1.1rem; font-size:.9rem; }
        .cv-modal-body { position:relative; flex:1; background:#525659; }
        .cv-modal-body iframe { position:relative; z-index:1; width:100%; height:100%; border:0; display:block; }
        .cv-loading { position:absolute; inset:0; display:grid; place-items:center; color:#fff; font-size:1.8rem; opacity:.7; }
        @keyframes cv-in { from { opacity:0; transform:translateY(16px) scale(.98); } }

        /* ═════════ FLOATING UTILITIES ═════════ */
        .to-top { position:fixed; bottom:1.5rem; inset-inline-end:1.5rem; z-index:500; width:48px; height:48px; border-radius:14px; border:1px solid var(--border); background:var(--glass); backdrop-filter:blur(10px); color:var(--cyan); display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-md); opacity:0; visibility:hidden; transform:translateY(12px); transition:all .3s; cursor:none; }
        .to-top.show { opacity:1; visibility:visible; transform:none; }

        /* ═════════ ACCESSIBILITY ═════════ */
        .skip-link { position:absolute; top:-100px; inset-inline-start:1rem; z-index:2000; padding:.75rem 1.25rem; border-radius:10px; background:var(--gradient); color:var(--on-accent); font-weight:700; text-decoration:none; transition:top .2s; }
        .skip-link:focus { top:1rem; }
        :where(a, button, input, select, textarea, [tabindex]):focus-visible { outline:2px solid var(--cyan); outline-offset:3px; }
        .form-input:focus-visible, .form-textarea:focus-visible { outline:none; }
        html:not(.custom-cursor) body { cursor:auto; }
        html:not(.custom-cursor) :is(a, button, select, label, .hamburger, .theme-option, .project-card, .service-card) { cursor:pointer; }
        html:not(.custom-cursor) :is(input, textarea) { cursor:text; }
        html:not(.custom-cursor) :is(#cursor-dot, #cursor-ring, #cursor-trail-container) { display:none; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior:auto; }
            *, *::before, *::after { animation-duration:.01ms !important; animation-iteration-count:1 !important; transition-duration:.01ms !important; }
            .reveal { opacity:1; transform:none; }
            .marquee { flex-wrap:wrap; justify-content:center; mask-image:none; -webkit-mask-image:none; }
            .marquee-track { animation:none; flex-wrap:wrap; justify-content:center; }
            .marquee-track[aria-hidden="true"] { display:none; }
        }
        [dir="rtl"] :is(.eyebrow, .tech-strip-label, .service-includes-label, .hero-title, .section-title, .service-title, .bento-story h3) { letter-spacing:0; }
        [dir="rtl"] .chip { font-family:var(--font-body); }

        /* ═════════ RESPONSIVE ═════════ */
        @media(max-width:1024px){
            .hero-inner { grid-template-columns:1fr; gap:4.5rem; }
            .hero-copy { text-align:center; }
            .hero-subtitle { margin-inline:auto; }
            .hero-actions, .hero-promises, .hero-social { justify-content:center; }
            .bento { grid-template-columns:1fr 1fr; grid-template-areas:"story story" "values status" "tools tools"; }
            .process-grid, .guarantees { grid-template-columns:repeat(2,1fr); }
            .process-step::after { display:none; }
            .contact-shell { grid-template-columns:1fr; }
        }
        @media(max-width:768px){
            section { padding:5rem 0; }
            .container { padding-inline:1rem; }
            nav { padding:.75rem .75rem 0; }
            .nav-inner { padding-inline:.9rem .45rem; height:62px; }
            .nav-links { display:none; }
            .hamburger { display:flex; width:44px; height:44px; }
            .theme-toggle-btn { width:44px; height:44px; }
            .lang-btn { min-height:40px; min-width:40px; display:inline-flex; align-items:center; justify-content:center; }
            .nav-links.open { display:flex; flex-direction:column; position:absolute; top:calc(100% + 10px); left:0; right:0; background:var(--mobile-nav-bg); backdrop-filter:blur(20px); padding:.75rem; border:1px solid var(--border); border-radius:22px; box-shadow:var(--shadow-lg); gap:.2rem; }
            .nav-links.open li { width:100%; }
            .nav-links.open a { padding:.85rem 1rem; border-radius:14px; font-size:1rem; }
            .nav-links.open .nav-cta { justify-content:center; margin:.35rem 0 0; }
            #hero { padding:110px 0 50px; min-height:auto; }
            .portrait { width:min(320px,82%); }
            .float-card { padding:.55rem .75rem; gap:.5rem; }
            .float-card .icon-tile { width:32px; height:32px; font-size:.85rem; }
            .float-card strong { font-size:.88rem; }
            .fc-1 { inset-inline-start:-26px; }
            .fc-2 { inset-inline-end:-24px; }
            .hero-stats { grid-template-columns:1fr; margin-top:3.5rem; }
            .stat-item { justify-content:flex-start; padding:1.1rem 1.4rem; }
            .stat-item + .stat-item { border-inline-start:none; border-top:1px solid var(--border); }
            .section-head { margin-bottom:2.8rem; }
            .bento { grid-template-columns:1fr; grid-template-areas:"story" "values" "status" "tools"; }
            .bento-story, .bento-card { padding:1.6rem; }
            .service-card { padding:1.6rem; }
            .services-help { flex-direction:column; text-align:center; }
            .process-grid, .guarantees { grid-template-columns:1fr; }
            .form-row { grid-template-columns:1fr; }
            .contact-aside, .contact-form { padding:1.8rem 1.4rem; }
            .timeline::before { inset-inline-start:19px; }
            .timeline-item { padding-inline-start:56px; }
            .timeline-dot { width:40px; height:40px; border-radius:13px; }
            .timeline-card { padding:1.4rem; }
            .footer-top { flex-direction:column; align-items:center; text-align:center; }
            .footer-links { justify-content:center; }
            .to-top { bottom:1rem; inset-inline-end:1rem; }
            body { cursor:auto; }
            #cursor-dot, #cursor-ring, #cursor-trail-container { display:none; }
        }
    </style>
</head>
<body>

<a href="#main" class="skip-link">{{ __('Skip to content') }}</a>
<div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>

<div id="cursor-dot" aria-hidden="true"></div>
<div id="cursor-ring" aria-hidden="true"></div>
<div id="cursor-trail-container" aria-hidden="true"></div>

<!-- ═════════ NAV ═════════ -->
<nav id="navbar" aria-label="{{ __('Main navigation') }}">
    <div class="nav-inner">
        <a class="nav-logo" href="#hero" aria-label="{{ $heroName }}">
            <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="38" height="38">
            <span class="nav-logo-text">{{ $firstName }}<span>.</span></span>
        </a>
        <ul class="nav-links" id="navLinks">
            <li><a href="#services">{{ __('Services') }}</a></li>
            <li><a href="#about">{{ __('About') }}</a></li>
            <li><a href="#process">{{ __('Process') }}</a></li>
            <li><a href="#projects">{{ __('My Work') }}</a></li>
            <li><a href="#experience">{{ __('Experience') }}</a></li>
            <li><a class="nav-cta" href="#contact">{{ __('Start a Project') }} <i class="fas {{ $arrowIcon }}"></i></a></li>
        </ul>
        <div class="nav-controls">
            <div class="lang-switch">
                <a href="{{ url()->current() }}?lang=en" hreflang="en" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}" lang="en">EN</a>
                <a href="{{ url()->current() }}?lang=ar" hreflang="ar" class="lang-btn {{ $locale === 'ar' ? 'active' : '' }}" lang="ar">عربي</a>
            </div>
            <div class="theme-switch" id="themeSwitch">
                <button type="button" class="theme-toggle-btn" id="themeToggleBtn" title="{{ __('Theme') }}" aria-label="{{ __('Theme') }}" aria-haspopup="true" aria-expanded="false" aria-controls="themeMenu">
                    <i class="fas fa-palette"></i>
                </button>
                <div class="theme-menu" id="themeMenu">
                    <button type="button" class="theme-option" data-theme-choice="light"><span class="theme-swatch sw-light"></span>{{ __('Light') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="dark"><span class="theme-swatch sw-dark"></span>{{ __('Dark') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="ocean"><span class="theme-swatch sw-ocean"></span>{{ __('Ocean') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="sunset"><span class="theme-swatch sw-sunset"></span>{{ __('Sunset') }}</button>
                </div>
            </div>
            <button type="button" class="hamburger" id="hamburger" aria-label="{{ __('Menu') }}" aria-expanded="false" aria-controls="navLinks">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<main id="main">

<!-- ═════════ HERO: ABOUT ME ═════════ -->
<section id="hero">
    <div class="hero-bg" aria-hidden="true">
        <span class="blob b1"></span>
        <span class="blob b2"></span>
        <div class="hero-grid"></div>
    </div>
    <div class="container">
        <div class="hero-inner">
            <div class="hero-copy">
                <div class="hero-badge"><span class="pulse-dot"></span>{{ __('Available for new projects') }}</div>
                <p class="hero-hello">{{ __('Hi, I’m') }}</p>
                <h1 class="hero-title">
                    <span class="name">{{ $heroName }}</span>
                    <span class="role" id="typing-role">{{ $heroTagline }}</span>
                </h1>
                <p class="hero-subtitle">{!! $heroSubtitle !!}</p>
                <div class="hero-actions">
                    <a href="#contact" class="btn btn-primary"><i class="fas fa-comments"></i> {{ __('Discuss Your Project') }}</a>
                    <a href="#services" class="btn btn-ghost">{{ __('See How I Can Help') }} <i class="fas {{ $arrowIcon }}"></i></a>
                </div>
                <ul class="hero-promises">
                    <li><i class="fas fa-circle-check"></i> {{ __('Free initial consultation') }}</li>
                    <li><i class="fas fa-circle-check"></i> {{ __('Clear scope & price upfront') }}</li>
                    <li><i class="fas fa-circle-check"></i> {{ __('Reply within 1–2 days') }}</li>
                </ul>
                <div class="hero-social">
                    @if($cvUrl)
                    <a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="cv-chip" data-cv-open><i class="fas fa-file-lines"></i> {{ __('View my CV') }}</a>
                    <span class="hero-social-divider" aria-hidden="true"></span>
                    @endif
                    <span class="hero-social-label">{{ __('Find me on') }}</span>
                    @if($githubUrl)<a href="{{ $githubUrl }}" target="_blank" rel="noopener" class="social-btn" aria-label="GitHub"><i class="fab fa-github"></i></a>@endif
                    @if($linkedinUrl)<a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="social-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                    @if($whatsappNumber)<a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="social-btn" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>@endif
                    @if($contactEmail)<a href="mailto:{{ $contactEmail }}" class="social-btn" aria-label="{{ __('Email') }}"><i class="fas fa-envelope"></i></a>@endif
                </div>
            </div>

            <div class="hero-visual">
                <div class="portrait">
                    <div class="portrait-glow" aria-hidden="true"></div>
                    <div class="portrait-dots" aria-hidden="true"></div>
                    <div class="portrait-shape" aria-hidden="true"></div>
                    <div class="portrait-frame">
                        <picture>
                            <source srcset="{{ asset('images/me.webp') }}" type="image/webp">
                            <img src="{{ asset('images/me.jpeg') }}" alt="{{ $heroName }} — {{ $heroTagline }}" width="900" height="900" fetchpriority="high">
                        </picture>
                    </div>
                    <div class="float-card fc-1" aria-hidden="true">
                        <span class="icon-tile"><i class="fab fa-laravel"></i></span>
                        <div><strong>Laravel</strong><small>{{ __('Expert') }}</small></div>
                    </div>
                    <div class="float-card fc-2" aria-hidden="true">
                        <span class="icon-tile soft"><i class="fas fa-gauge-high"></i></span>
                        <div><strong>{{ __('Performance') }}</strong><small>{{ __('First mindset') }}</small></div>
                    </div>
                    <div class="float-card fc-3" aria-hidden="true">
                        <span class="pulse-dot"></span>{{ __('Taking new clients') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-stats reveal">
            <div class="stat-item">
                <span class="stat-number" dir="ltr">{{ $settings['hero_stat_years'] ?? '5+' }}</span>
                <span class="stat-label">{{ __('Years of') }}<br>{{ __('experience') }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" dir="ltr">{{ $settings['hero_stat_projects'] ?? '30+' }}</span>
                <span class="stat-label">{{ __('Projects') }}<br>{{ __('delivered') }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" dir="ltr">{{ $settings['hero_stat_clients'] ?? '15+' }}</span>
                <span class="stat-label">{{ __('Happy') }}<br>{{ __('clients') }}</span>
            </div>
        </div>
    </div>
</section>

<!-- ═════════ TECH MARQUEE ═════════ -->
@if(!empty($techTags))
<div class="tech-strip">
    <p class="tech-strip-label">{{ __('Technologies I work with') }}</p>
    <div class="marquee">
        @foreach([false, true] as $isDuplicate)
        <div class="marquee-track" @if($isDuplicate) aria-hidden="true" @endif>
            @foreach($techTags as $tag)
                <span class="tech-item"><i class="{{ $techIcon($tag) }}"></i>{{ $tag }}</span>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- ═════════ SERVICES ═════════ -->
<section id="services">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">{{ __('Services') }}</span>
            <h2 class="section-title">{{ __('How I Can') }} <span class="grad">{{ __('Help You') }}</span></h2>
            <p class="section-intro">{{ __('Whether you are starting from an idea or already have a system, I make sure the technology behind your business is solid, fast, and ready to grow.') }}</p>
        </div>
        @if(!empty($services))
        <div class="services-grid">
            @foreach($services as $service)
            @php
                $deliverables = ($locale === 'ar' && !empty($service->deliverables_ar)) ? $service->deliverables_ar : ($service->deliverables ?? []);
            @endphp
            <article class="card card-hover service-card reveal" style="--d: {{ $loop->index * 0.1 }}s">
                <div class="service-top">
                    <div class="icon-tile"><i class="{{ $service->icon }}"></i></div>
                    <span class="service-number" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h3 class="service-title">{{ t($service, 'title') }}</h3>
                <p class="service-summary">{{ t($service, 'summary') }}</p>
                @if(!empty($deliverables))
                <div class="service-includes-label">{{ __("What's included") }}</div>
                <ul class="service-includes">
                    @foreach($deliverables as $item)
                        <li><i class="fas fa-check"></i> {{ $item }}</li>
                    @endforeach
                </ul>
                @endif
                <a href="#contact" class="btn btn-ghost service-cta" data-service="{{ t($service, 'title') }}">
                    {{ __('Discuss this service') }} <i class="fas {{ $arrowIcon }}"></i>
                </a>
            </article>
            @endforeach
        </div>
        @endif
        <div class="services-help reveal">
            <span class="icon-tile soft"><i class="fas fa-lightbulb"></i></span>
            <div class="services-help-text">
                <strong>{{ __('Not sure what you need?') }}</strong>
                <span>{{ __('Describe the problem in your own words — I’ll tell you honestly what it needs, even if it’s a small fix.') }}</span>
            </div>
            <a href="#contact" class="btn btn-primary">{{ __('Ask me') }}</a>
        </div>
    </div>
</section>

<!-- ═════════ WHO AM I ═════════ -->
@php
    $aboutHeading = ts($settings, 'about_heading') ?: ($settings['about_heading'] ?? '');
    $aboutParagraphs = array_filter([
        ts($settings, 'about_p1') ?: ($settings['about_p1'] ?? ''),
        ts($settings, 'about_p2') ?: ($settings['about_p2'] ?? ''),
        ts($settings, 'about_p3') ?: ($settings['about_p3'] ?? ''),
    ]);
@endphp
<section id="about" class="section-alt">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">{{ __('Who Am I') }}</span>
            <h2 class="section-title">{{ __('The Person') }} <span class="grad">{{ __('Behind the Code') }}</span></h2>
        </div>
        <div class="bento">
            <article class="card bento-card bento-story reveal">
                <div class="story-head">
                    <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="64" height="64" loading="lazy">
                    <div>
                        <strong>{{ $heroName }}</strong>
                        <small>{{ $heroTagline }}</small>
                    </div>
                </div>
                @if($aboutHeading)<h3>{!! $aboutHeading !!}</h3>@endif
                @foreach($aboutParagraphs as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
                <div class="story-actions">
                    <a href="#contact" class="btn btn-primary">{{ __('Let’s Work Together') }} <i class="fas {{ $arrowIcon }}"></i></a>
                    @if($cvUrl)
                    <a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="btn btn-ghost" data-cv-open><i class="fas fa-file-lines"></i> {{ __('View CV') }}</a>
                    @endif
                    @if($githubUrl)<a href="{{ $githubUrl }}" target="_blank" rel="noopener" class="social-btn" aria-label="GitHub"><i class="fab fa-github"></i></a>@endif
                    @if($linkedinUrl)<a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="social-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                </div>
            </article>

            <article class="card bento-card bento-values reveal" style="--d:.1s">
                <h3 class="bento-title"><i class="fas fa-heart"></i>{{ __('What I care about') }}</h3>
                <ul class="values-list">
                    <li>
                        <span class="icon-tile soft"><i class="fas fa-broom"></i></span>
                        <div><strong>{{ __('Clean, maintainable code') }}</strong><span>{{ __('Code the next developer can read and extend.') }}</span></div>
                    </li>
                    <li>
                        <span class="icon-tile soft"><i class="fas fa-comments"></i></span>
                        <div><strong>{{ __('Honest communication') }}</strong><span>{{ __('Clear updates, realistic estimates, no surprises.') }}</span></div>
                    </li>
                    <li>
                        <span class="icon-tile soft"><i class="fas fa-gauge-high"></i></span>
                        <div><strong>{{ __('Performance first') }}</strong><span>{{ __('Fast systems that stay fast as you grow.') }}</span></div>
                    </li>
                </ul>
            </article>

            <article class="card bento-card bento-status reveal" style="--d:.2s">
                <div class="status-top"><span class="pulse-dot"></span>{{ __('Currently available') }}</div>
                <div>
                    <h4>{{ __('Open to freelance & remote projects') }}</h4>
                    <p>{{ __('Usually replies within 1–2 days') }}</p>
                </div>
                <a href="#contact" class="btn">{{ __('Say hello') }} <i class="fas fa-hand"></i></a>
            </article>

            @if(!empty($techTags))
            <article class="card bento-card bento-tools reveal" style="--d:.1s">
                <h3 class="bento-title"><i class="fas fa-toolbox"></i>{{ __('My toolbox') }}</h3>
                <div class="tools-wrap">
                    @foreach($techTags as $tag)
                        <span class="tech-item"><i class="{{ $techIcon($tag) }}"></i>{{ $tag }}</span>
                    @endforeach
                </div>
            </article>
            @endif
        </div>
    </div>
</section>

<!-- ═════════ PROCESS ═════════ -->
<section id="process">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">{{ __('Process') }}</span>
            <h2 class="section-title">{{ __('How We') }} <span class="grad">{{ __('Work Together') }}</span></h2>
            <p class="section-intro">{{ __('A simple, transparent process — you always know what is happening and what comes next.') }}</p>
        </div>
        @php
            $steps = [
                ['icon' => 'fa-comments',         'title' => __('Discovery'),         'text' => __('We talk about your project, the problem, and the goal. No technical jargon needed.')],
                ['icon' => 'fa-magnifying-glass', 'title' => __('Audit & Plan'),      'text' => __('I review the code or requirements and send a clear plan with scope, timeline, and price.')],
                ['icon' => 'fa-code',             'title' => __('Build & Update'),    'text' => __('I get to work and share regular progress updates, so there are no surprises.')],
                ['icon' => 'fa-rocket',           'title' => __('Deliver & Support'), 'text' => __('You get tested, documented work — plus support after delivery to make sure everything runs smoothly.')],
            ];
        @endphp
        <div class="process-grid">
            @foreach($steps as $step)
            <div class="card card-hover process-step reveal" style="--d: {{ $loop->index * 0.1 }}s">
                <div class="process-step-head">
                    <span class="process-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="icon-tile soft"><i class="fas {{ $step['icon'] }}"></i></span>
                </div>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['text'] }}</p>
            </div>
            @endforeach
        </div>
        <div class="guarantees reveal">
            <div class="guarantee"><i class="fas fa-file-signature"></i><span>{{ __('Clear scope & price before starting') }}</span></div>
            <div class="guarantee"><i class="fas fa-clock-rotate-left"></i><span>{{ __('Regular progress updates') }}</span></div>
            <div class="guarantee"><i class="fas fa-book-open"></i><span>{{ __('Clean, documented code you own') }}</span></div>
            <div class="guarantee"><i class="fas fa-life-ring"></i><span>{{ __('Support after delivery') }}</span></div>
        </div>
    </div>
</section>

<!-- ═════════ PROJECTS ═════════ -->
<section id="projects" class="section-alt">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">{{ __('My Work') }}</span>
            <h2 class="section-title">{{ __('Work I’m') }} <span class="grad">{{ __('Proud Of') }}</span></h2>
            <p class="section-intro">{{ __('Real systems I have built and improved — open any project to see the challenge, the approach, and the result.') }}</p>
        </div>
        <div class="projects-grid">
            @foreach($projects as $project)
            @php
                $projectImage = $project->image ? project_image_url($project->image) : null;
                $projectCategory = t($project, 'category');
            @endphp
            <article class="card card-hover project-card reveal" style="--d: {{ ($loop->index % 3) * 0.1 }}s">
                <div class="project-cover {{ $projectImage ? '' : 'no-image' }}">
                    <img src="{{ $projectImage ?: asset('images/project-base.svg') }}" alt="" loading="lazy">
                    @unless($projectImage)
                        <span class="project-emoji" aria-hidden="true">{{ $project->icon }}</span>
                    @endunless
                    <div class="project-badges">
                        @if($projectCategory)<span class="project-badge">{{ $projectCategory }}</span>@else<span></span>@endif
                        @if($project->featured)<span class="project-badge featured"><i class="fas fa-star"></i> {{ __('Featured') }}</span>@endif
                    </div>
                </div>
                <div class="project-body">
                    <h3 class="project-title">{{ t($project, 'title') }}</h3>
                    <p class="project-desc">{{ t($project, 'description') }}</p>
                    @if(!empty($project->stack))
                    <div class="project-stack">
                        @foreach(array_slice($project->stack, 0, 5) as $tech)
                            <span class="chip chip-muted">{{ $tech }}</span>
                        @endforeach
                        @if(count($project->stack) > 5)<span class="chip chip-muted">+{{ count($project->stack) - 5 }}</span>@endif
                    </div>
                    @endif
                    <div class="project-actions">
                        <a href="{{ route('project.show', $project->id) }}" class="btn btn-primary">{{ __('More details') }} <i class="fas {{ $arrowIcon }}"></i></a>
                        @if($project->live_url)
                            <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="icon-btn" aria-label="{{ __('Visit it') }}" title="{{ __('Visit it') }}"><i class="fas fa-arrow-up-right-from-square"></i></a>
                        @endif
                        @if($project->github_url)
                            <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="icon-btn" aria-label="GitHub" title="GitHub"><i class="fab fa-github"></i></a>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

<!-- ═════════ SKILLS ═════════ -->
@if(!empty($categories))
<section id="skills">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">{{ __('Skills') }}</span>
            <h2 class="section-title">{{ __('Technical') }} <span class="grad">{{ __('Skills') }}</span></h2>
        </div>
        <div class="skills-grid">
            @foreach($categories as $cat)
            <div class="card card-hover skill-category reveal" style="--d: {{ ($loop->index % 4) * 0.08 }}s">
                <div class="skill-cat-head">
                    <span class="icon-tile soft" aria-hidden="true">{{ $cat->icon }}</span>
                    <h3 class="skill-cat-title">{{ t($cat, 'name') }}</h3>
                </div>
                @if($cat->type === 'bars')
                    <div class="skill-items">
                        @foreach($cat->skills as $skill)
                        <div>
                            <div class="skill-info">
                                <span class="skill-name">{{ t($skill, 'name') }}</span>
                                <span class="skill-pct">{{ $skill->percentage }}%</span>
                            </div>
                            <div class="skill-bar" role="progressbar" aria-label="{{ t($skill, 'name') }}" aria-valuenow="{{ $skill->percentage }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="skill-fill" data-width="{{ $skill->percentage }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="tech-tags">
                        @foreach($cat->skills as $skill)
                            <span class="chip chip-muted">{{ t($skill, 'name') }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═════════ EXPERIENCE ═════════ -->
<section id="experience" class="section-alt">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">{{ __('Experience') }}</span>
            <h2 class="section-title">{{ __('My Professional') }} <span class="grad">{{ __('Journey') }}</span></h2>
        </div>
        <div class="timeline">
            @foreach($experiences as $exp)
            <div class="timeline-item reveal {{ $loop->first ? 'current' : '' }}">
                <div class="timeline-dot" aria-hidden="true"><i class="fas fa-briefcase"></i></div>
                <div class="card card-hover timeline-card">
                    <div class="timeline-header">
                        <div>
                            <h3 class="timeline-title">{{ t($exp, 'title') }}</h3>
                            <div class="timeline-company">{{ t($exp, 'company') }}</div>
                        </div>
                        <span class="timeline-date" dir="ltr">{{ $exp->date_range }}</span>
                    </div>
                    <p class="timeline-desc">{{ t($exp, 'description') }}</p>
                    @if(!empty($exp->tags))
                    <div class="timeline-tags">
                        @foreach($exp->tags as $tag)
                            <span class="chip">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ═════════ CONTACT ═════════ -->
<section id="contact">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">{{ __('Contact') }}</span>
            <h2 class="section-title">{{ __('Let’s Talk About') }} <span class="grad">{{ __('Your Project') }}</span></h2>
            <p class="section-intro">{{ __('Tell me briefly what you need. You will get a clear answer on how I can help, how long it takes, and what it costs — no obligation.') }}</p>
        </div>
        <div class="contact-shell reveal">
            <aside class="contact-aside">
                <div class="contact-person">
                    <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="58" height="58" loading="lazy">
                    <div>
                        <strong>{{ $heroName }}</strong>
                        <span><span class="pulse-dot"></span>{{ __('Usually replies within 1–2 days') }}</span>
                    </div>
                </div>
                <h3>{{ __('What happens next?') }}</h3>
                <ol class="next-steps">
                    <li><span>1</span>{{ __('You send a short message about your project.') }}</li>
                    <li><span>2</span>{{ __('I reply with questions or a first recommendation.') }}</li>
                    <li><span>3</span>{{ __('You get a clear plan with scope, timeline, and price.') }}</li>
                </ol>
                <div class="contact-links">
                    @if($cvUrl)
                    <a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="contact-link" data-cv-open>
                        <i class="fas fa-file-lines"></i><span><small>{{ __('Curriculum Vitae') }}</small><b>{{ __('View & download my CV') }}</b></span>
                    </a>
                    @endif
                    @if($whatsappNumber)
                    <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="contact-link">
                        <i class="fab fa-whatsapp"></i><span><small>WhatsApp</small><b dir="ltr">+{{ $whatsappNumber }}</b></span>
                    </a>
                    @endif
                    @if($contactEmail)
                    <a href="mailto:{{ $contactEmail }}" class="contact-link">
                        <i class="fas fa-envelope"></i><span><small>{{ __('Email') }}</small><b>{{ $contactEmail }}</b></span>
                    </a>
                    @endif
                    @if($linkedinUrl)
                    <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="contact-link">
                        <i class="fab fa-linkedin-in"></i><span><small>LinkedIn</small><b>{{ __('Connect with me') }}</b></span>
                    </a>
                    @endif
                </div>
            </aside>
            <div class="contact-form">
                <div class="contact-form-head">
                    <h3>{{ __('Request a free consultation') }}</h3>
                    <p>{{ __('I reply personally, usually within 1–2 days. You will also get a confirmation email.') }}</p>
                </div>

                <div class="form-status is-success" id="contactStatus" role="status" aria-live="polite" @unless(session('contact_success')) hidden @endunless>
                    <i class="fas fa-circle-check"></i><span>{{ session('contact_success') }}</span>
                </div>
                <div class="form-status is-error" id="contactError" role="alert" @unless(session('contact_error') || $errors->any()) hidden @endunless>
                    <i class="fas fa-circle-exclamation"></i><span>{{ session('contact_error') ?: ($errors->any() ? __('Please check the highlighted fields.') : '') }}</span>
                </div>

                <form id="contactForm" method="POST" action="{{ route('contact.store') }}" novalidate data-whatsapp="{{ $whatsappNumber }}">
                    @csrf
                    <div class="hp-field" aria-hidden="true">
                        <label for="cf-website">Website</label>
                        <input type="text" id="cf-website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="cf-name">{{ __('Name') }}</label>
                            <input type="text" id="cf-name" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('Your name') }}" autocomplete="name" required maxlength="100" aria-describedby="cf-name-error">
                            <p class="field-error" id="cf-name-error">@error('name'){{ $message }}@enderror</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cf-email">{{ __('Email') }}</label>
                            <input type="email" id="cf-email" name="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@company.com" autocomplete="email" required maxlength="150" dir="ltr" aria-describedby="cf-email-error">
                            <p class="field-error" id="cf-email-error">@error('email'){{ $message }}@enderror</p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="cf-service">{{ __('What do you need?') }}</label>
                            <select id="cf-service" name="service" class="form-input">
                                @foreach($services as $service)
                                    <option value="{{ t($service, 'title') }}" @selected(old('service') === t($service, 'title'))>{{ t($service, 'title') }}</option>
                                @endforeach
                                <option value="{{ __('Something else') }}" @selected(old('service') === __('Something else'))>{{ __('Something else') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cf-phone">{{ __('Phone / WhatsApp') }} <small>({{ __('optional') }})</small></label>
                            <input type="tel" id="cf-phone" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+970 59 000 0000" autocomplete="tel" maxlength="30" dir="ltr">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cf-budget">{{ __('Estimated budget') }} <small>({{ __('optional') }})</small></label>
                        <select id="cf-budget" name="budget" class="form-input">
                            <option value="">{{ __('Not sure yet') }}</option>
                            @foreach(['< $500', '$500 – $1,500', '$1,500 – $5,000', '$5,000+'] as $budgetOption)
                                <option value="{{ $budgetOption }}" @selected(old('budget') === $budgetOption)>{{ $budgetOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cf-message">{{ __('Message') }}</label>
                        <textarea id="cf-message" name="message" class="form-textarea @error('message') is-invalid @enderror" placeholder="{{ __('What is the problem, and what result do you want?') }}" required maxlength="5000" aria-describedby="cf-message-error">{{ old('message') }}</textarea>
                        <p class="field-error" id="cf-message-error">@error('message'){{ $message }}@enderror</p>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="contactSubmit">
                            <i class="fas fa-paper-plane"></i>
                            <span class="btn-label">{{ __('Send Message') }}</span>
                        </button>
                        @if($whatsappNumber)
                        <button type="button" class="btn btn-ghost" id="contactWhatsapp"><i class="fab fa-whatsapp"></i> {{ __('Send via WhatsApp') }}</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

</main>

<!-- ═════════ FOOTER ═════════ -->
<footer>
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <a class="nav-logo" href="#hero">
                    <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="38" height="38" loading="lazy">
                    <span>{{ $heroName }}</span>
                </a>
                <p>{{ __('Whether you are starting from an idea or already have a system, I make sure the technology behind your business is solid, fast, and ready to grow.') }}</p>
            </div>
            <ul class="footer-links">
                <li><a href="#services">{{ __('Services') }}</a></li>
                <li><a href="#about">{{ __('About') }}</a></li>
                <li><a href="#process">{{ __('Process') }}</a></li>
                <li><a href="#projects">{{ __('My Work') }}</a></li>
                @if($cvUrl)<li><a href="{{ $cvUrl }}" target="_blank" rel="noopener" data-cv-open>{{ __('CV') }}</a></li>@endif
                <li><a href="#contact">{{ __('Contact') }}</a></li>
            </ul>
            <div class="footer-social">
                @if($githubUrl)<a href="{{ $githubUrl }}" target="_blank" rel="noopener" class="social-btn" aria-label="GitHub"><i class="fab fa-github"></i></a>@endif
                @if($linkedinUrl)<a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="social-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                @if($whatsappNumber)<a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="social-btn" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>@endif
                @if($contactEmail)<a href="mailto:{{ $contactEmail }}" class="social-btn" aria-label="{{ __('Email') }}"><i class="fas fa-envelope"></i></a>@endif
            </div>
        </div>
        <p class="footer-bottom">{{ ts($settings, 'footer_text') ?: ($settings['footer_text'] ?? '') }}</p>
    </div>
</footer>

@if($cvUrl)
<dialog class="cv-modal" id="cvModal" aria-labelledby="cvModalTitle">
    <div class="cv-modal-head">
        <div class="cv-modal-title">
            <span class="icon-tile soft"><i class="fas fa-file-lines"></i></span>
            <div>
                <h2 id="cvModalTitle">{{ __('Curriculum Vitae') }}</h2>
                <small>{{ $heroName }} · {{ $heroTagline }}</small>
            </div>
        </div>
        <div class="cv-modal-actions">
            <a href="{{ $cvUrl }}" download="{{ basename($cvPath) }}" class="btn btn-primary"><i class="fas fa-download"></i> <span class="cv-label">{{ __('Download') }}</span></a>
            <a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="icon-btn" aria-label="{{ __('Open in new tab') }}" title="{{ __('Open in new tab') }}"><i class="fas fa-up-right-from-square"></i></a>
            <button type="button" class="icon-btn" data-cv-close autofocus aria-label="{{ __('Close') }}" title="{{ __('Close') }}"><i class="fas fa-xmark"></i></button>
        </div>
    </div>
    <div class="cv-modal-body">
        <div class="cv-loading" aria-hidden="true"><i class="fas fa-circle-notch fa-spin"></i></div>
        <iframe title="{{ __('Curriculum Vitae') }} — {{ $heroName }}" data-src="{{ $cvUrl }}#view=FitH"></iframe>
    </div>
</dialog>
@endif

<button type="button" class="to-top" id="toTop" aria-label="{{ __('Back to top') }}"><i class="fas fa-arrow-up"></i></button>


<script>
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ─── CUSTOM CURSOR (mouse users without reduced motion only) ───
(function () {
    if (!document.documentElement.classList.contains('custom-cursor')) return;
    const dot = document.getElementById('cursor-dot');
    const ring = document.getElementById('cursor-ring');
    const trailContainer = document.getElementById('cursor-trail-container');
    const TRAIL_COUNT = 12;
    const trails = [];
    for (let i = 0; i < TRAIL_COUNT; i++) {
        const t = document.createElement('div');
        t.className = 'cursor-trail';
        trailContainer.appendChild(t);
        trails.push({ el: t, x: 0, y: 0 });
    }
    let mx = 0, my = 0, ringX = 0, ringY = 0;
    document.addEventListener('mousemove', e => {
        mx = e.clientX; my = e.clientY;
        dot.style.left = mx + 'px';
        dot.style.top = my + 'px';
    });
    document.addEventListener('mousedown', () => document.body.classList.add('cursor-click'));
    document.addEventListener('mouseup', () => document.body.classList.remove('cursor-click'));
    document.querySelectorAll('a, button, .card-hover, input, select, textarea').forEach(el => {
        el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
        el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
    });
    (function animateCursor() {
        ringX += (mx - ringX) * 0.12;
        ringY += (my - ringY) * 0.12;
        ring.style.left = ringX + 'px';
        ring.style.top = ringY + 'px';
        let px = mx, py = my;
        trails.forEach((t, i) => {
            const delay = 0.08 + i * 0.04;
            t.x += (px - t.x) * delay;
            t.y += (py - t.y) * delay;
            t.el.style.left = t.x + 'px';
            t.el.style.top = t.y + 'px';
            t.el.style.opacity = (1 - i / TRAIL_COUNT) * 0.35;
            const s = Math.max(4 - i * 0.25, 1);
            t.el.style.width = s + 'px';
            t.el.style.height = s + 'px';
            px = t.x; py = t.y;
        });
        requestAnimationFrame(animateCursor);
    })();
})();

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
    function closeMenu(returnFocus) {
        menu.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        if (returnFocus) btn.focus();
    }
    applyTheme(root.getAttribute('data-theme') || 'light');
    btn.addEventListener('click', e => {
        e.stopPropagation();
        btn.setAttribute('aria-expanded', menu.classList.toggle('open'));
    });
    options.forEach(o => o.addEventListener('click', () => { applyTheme(o.dataset.themeChoice); closeMenu(true); }));
    document.addEventListener('click', e => {
        if (!document.getElementById('themeSwitch').contains(e.target)) closeMenu(false);
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && menu.classList.contains('open')) closeMenu(true);
    });
})();

// ─── NAV: SCROLL STATE, ACTIVE LINK, PROGRESS, BACK TO TOP ───
(function () {
    const navbar = document.getElementById('navbar');
    const progress = document.getElementById('scrollProgress');
    const toTop = document.getElementById('toTop');
    const sections = [...document.querySelectorAll('main section[id]')];
    const links = [...document.querySelectorAll('.nav-links a[href^="#"]')];
    let ticking = false;
    function update() {
        const y = window.scrollY;
        const max = document.documentElement.scrollHeight - window.innerHeight;
        navbar.classList.toggle('scrolled', y > 40);
        progress.style.transform = 'scaleX(' + (max > 0 ? y / max : 0) + ')';
        toTop.classList.toggle('show', y > 700);
        let current = '';
        sections.forEach(s => { if (y >= s.offsetTop - 140) current = s.id; });
        links.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + current));
        ticking = false;
    }
    window.addEventListener('scroll', () => {
        if (!ticking) { requestAnimationFrame(update); ticking = true; }
    }, { passive: true });
    toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: prefersReducedMotion ? 'auto' : 'smooth' }));
    update();
})();

// ─── MOBILE NAV ───
(function () {
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');
    function setMobileNav(open) {
        navLinks.classList.toggle('open', open);
        hamburger.classList.toggle('open', open);
        hamburger.setAttribute('aria-expanded', open);
    }
    hamburger.addEventListener('click', () => setMobileNav(!navLinks.classList.contains('open')));
    navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMobileNav(false)));
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && navLinks.classList.contains('open')) { setMobileNav(false); hamburger.focus(); }
    });
})();

// ─── SCROLL REVEAL ───
(function () {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('visible');
            entry.target.querySelectorAll('.skill-fill').forEach(bar => { bar.style.width = bar.dataset.width + '%'; });
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
})();

// ─── TYPING ROLE ───
(function () {
    const roleEl = document.getElementById('typing-role');
    const roles = {{ \Illuminate\Support\Js::from([$heroTagline, __('Laravel Expert'), __('Performance Optimizer'), __('API Architect')]) }};
    if (prefersReducedMotion) return;
    let ri = 0, ci = roles[0].length, deleting = false;
    function typeRole() {
        const cur = roles[ri];
        if (!deleting) {
            roleEl.textContent = cur.slice(0, ++ci);
            if (ci === cur.length) { deleting = true; setTimeout(typeRole, 2200); return; }
        } else {
            roleEl.textContent = cur.slice(0, --ci);
            if (ci === 0) { deleting = false; ri = (ri + 1) % roles.length; }
        }
        setTimeout(typeRole, deleting ? 45 : 85);
    }
    setTimeout(typeRole, 2200);
})();

// ─── CV VIEWER ───
(function () {
    const modal = document.getElementById('cvModal');
    if (!modal || typeof modal.showModal !== 'function') return;
    const frame = modal.querySelector('iframe');
    // Phones and browsers without a built-in PDF viewer get the PDF in a new tab instead (the link's default).
    const canShowInline = () => navigator.pdfViewerEnabled !== false && window.matchMedia('(min-width: 769px)').matches;
    document.querySelectorAll('[data-cv-open]').forEach(link => link.addEventListener('click', e => {
        if (!canShowInline()) return;
        e.preventDefault();
        if (!frame.getAttribute('src')) frame.setAttribute('src', frame.dataset.src);
        modal.showModal();
        document.documentElement.style.overflow = 'hidden';
    }));
    modal.addEventListener('close', () => { document.documentElement.style.overflow = ''; });
    modal.querySelector('[data-cv-close]').addEventListener('click', () => modal.close());
    modal.addEventListener('click', e => { if (e.target === modal) modal.close(); });
})();

// ─── SERVICE CTA → PRESELECT IN FORM ───
document.querySelectorAll('.service-cta[data-service]').forEach(a => a.addEventListener('click', () => {
    const select = document.getElementById('cf-service');
    if (select) select.value = a.dataset.service;
}));

// ─── CONTACT FORM → SERVER (email) with WhatsApp as an alternative ───
(function () {
    const form = document.getElementById('contactForm');
    if (!form || !window.fetch) return; // without fetch, the form posts normally
    const submit = document.getElementById('contactSubmit');
    const submitLabel = submit.querySelector('.btn-label');
    const statusBox = document.getElementById('contactStatus');
    const errorBox = document.getElementById('contactError');
    const text = {{ \Illuminate\Support\Js::from([
        'sending' => __('Sending…'),
        'send' => __('Send Message'),
        'checkFields' => __('Please check the highlighted fields.'),
        'network' => __('Your message could not be sent. Please check your connection, or contact me on WhatsApp.'),
        'greeting' => __('Hello, I found you through your portfolio.'),
        'name' => __('Name'),
        'email' => __('Email'),
        'service' => __('Service'),
        'budget' => __('Budget'),
        'message' => __('Message'),
    ]) }};

    const show = (box, message) => { box.querySelector('span').textContent = message; box.hidden = false; };
    const clearErrors = () => {
        errorBox.hidden = true;
        form.querySelectorAll('.is-invalid').forEach(el => { el.classList.remove('is-invalid'); el.removeAttribute('aria-invalid'); });
        form.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; });
    };

    form.addEventListener('submit', async e => {
        e.preventDefault();
        clearErrors();
        statusBox.hidden = true;
        submit.disabled = true;
        submit.setAttribute('aria-busy', 'true');
        submitLabel.textContent = text.sending;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                form.reset();
                show(statusBox, data.message);
                statusBox.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'center' });
            } else if (response.status === 422 && data.errors) {
                let firstInvalid = null;
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const input = form.querySelector('[name="' + field + '"]');
                    const holder = document.getElementById('cf-' + field + '-error');
                    if (input) { input.classList.add('is-invalid'); input.setAttribute('aria-invalid', 'true'); firstInvalid = firstInvalid || input; }
                    if (holder) holder.textContent = messages[0];
                });
                show(errorBox, text.checkFields);
                if (firstInvalid) firstInvalid.focus();
            } else {
                show(errorBox, data.message || text.network);
            }
        } catch (error) {
            show(errorBox, text.network);
        } finally {
            submit.disabled = false;
            submit.removeAttribute('aria-busy');
            submitLabel.textContent = text.send;
        }
    });

    // WhatsApp alternative: opens a chat pre-filled with whatever the visitor has typed
    const whatsappButton = document.getElementById('contactWhatsapp');
    if (whatsappButton && form.dataset.whatsapp) {
        whatsappButton.addEventListener('click', () => {
            const data = new FormData(form);
            const lines = [text.greeting, ''];
            if (data.get('name')) lines.push(text.name + ': ' + data.get('name'));
            if (data.get('service')) lines.push(text.service + ': ' + data.get('service'));
            if (data.get('budget')) lines.push(text.budget + ': ' + data.get('budget'));
            if (data.get('message')) lines.push('', text.message + ':', data.get('message'));
            window.open('https://wa.me/' + form.dataset.whatsapp + '?text=' + encodeURIComponent(lines.join('\n')), '_blank', 'noopener');
        });
    }
})();
</script>
@include('partials.whatsapp-sticker')
</body>
</html>
