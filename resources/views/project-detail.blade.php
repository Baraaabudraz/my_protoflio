@php
    $heroName = ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? 'Portfolio');
    $heroTagline = ts($settings, 'hero_tagline') ?: ($settings['hero_tagline'] ?? '');
    $firstName = explode(' ', $heroName)[0];
    $projectTitle = t($project, 'title');
    $projectImage = $project->image ? project_image_url($project->image) : null;
    $client = t($project, 'client');
    $duration = t($project, 'duration');
    $category = t($project, 'category');
    $overview = t($project, 'overview');
    $stack = (array) ($project->stack ?? []);
    $stages = (array) ($project->work_stages ?? []);
    $homeUrl = \App\Http\Middleware\SetLocale::localizedUrl(url('/'), $locale);
    $pageUrl = \App\Http\Middleware\SetLocale::localizedUrl(url()->current(), $locale);
    $arrowIcon = $locale === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right';
    $backIcon = $locale === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left';
    $whatsappNumber = preg_replace('/\D+/', '', $settings['whatsapp_number'] ?? '');
    $similarMessage = __('Hello :name, I saw your project ":project" and I would like something similar.', ['name' => $firstName, 'project' => $projectTitle]);
    $similarUrl = $whatsappNumber ? 'https://wa.me/'.$whatsappNumber.'?text='.rawurlencode($similarMessage) : $homeUrl.'#contact';
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
            document.documentElement.setAttribute('data-theme', ['light', 'dark', 'ocean', 'sunset'].includes(saved) ? saved : 'light');
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@500;700&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @include('partials.theme-tokens')

        /* ═════════ BASE ═════════ */
        * { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; scroll-padding-top:90px; }
        body { font-family:var(--font-body); background:var(--bg-primary); color:var(--text-primary); line-height:1.65; overflow-x:hidden; -webkit-font-smoothing:antialiased; transition:background .35s, color .35s; }
        img { max-width:100%; display:block; }
        a { color:inherit; }
        ::selection { background:var(--cyan); color:var(--on-accent); }
        :where(a, button):focus-visible { outline:2px solid var(--cyan); outline-offset:3px; }
        .container { max-width:1200px; margin:0 auto; padding-inline:1.5rem; }
        .skip-link { position:absolute; top:-100px; inset-inline-start:1rem; z-index:2000; padding:.75rem 1.25rem; border-radius:10px; background:var(--gradient); color:var(--on-accent); font-weight:700; text-decoration:none; }
        .skip-link:focus { top:1rem; }
        .scroll-progress { position:fixed; top:0; left:0; right:0; height:3px; background:var(--gradient); transform-origin:0 50%; transform:scaleX(0); z-index:1200; }
        [dir="rtl"] .scroll-progress { transform-origin:100% 50%; }

        /* ═════════ SHARED COMPONENTS ═════════ */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.6rem; min-height:48px; padding:0 1.4rem; border-radius:var(--radius-sm); font:700 .93rem var(--font-body); text-decoration:none; border:1px solid transparent; cursor:pointer; transition:transform .25s var(--ease), box-shadow .25s, background .25s, color .25s, border-color .25s; white-space:nowrap; }
        .btn-primary { background:var(--gradient); color:var(--on-accent); box-shadow:0 10px 26px -8px color-mix(in srgb, var(--cyan) 70%, transparent); }
        .btn-primary:hover { transform:translateY(-2px); }
        .btn-ghost { background:var(--glass); color:var(--text-primary); border-color:var(--border); }
        .btn-ghost:hover { border-color:var(--cyan); color:var(--cyan); transform:translateY(-2px); }
        .btn-whatsapp { background:#25d366; color:#fff; }
        .btn-whatsapp:hover { background:#1ebe5b; transform:translateY(-2px); }
        .chip { display:inline-flex; align-items:center; gap:.4rem; padding:.38rem .85rem; border-radius:999px; background:var(--accent-soft); border:1px solid var(--accent-line); color:var(--cyan); font-size:.8rem; font-weight:600; }
        .chip-muted { background:color-mix(in srgb, var(--text-primary) 4%, transparent); border-color:var(--border); color:var(--text-secondary); }
        .chip-featured { background:rgba(250,204,21,.18); border-color:rgba(250,204,21,.45); color:#a16207; }
        [data-theme]:not([data-theme="light"]) .chip-featured { color:#facc15; }
        .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); }
        .icon-tile { width:48px; height:48px; border-radius:14px; background:var(--accent-soft); border:1px solid var(--accent-line); color:var(--cyan); display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
        .eyebrow { display:inline-flex; align-items:center; gap:.5rem; font-size:.76rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--cyan); margin-bottom:.6rem; }
        .eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
        [dir="rtl"] .eyebrow { letter-spacing:0; }
        .block-title { font:800 clamp(1.4rem,2.6vw,1.75rem)/1.3 var(--font-head); letter-spacing:-.02em; margin-bottom:1.2rem; }
        [dir="rtl"] .block-title { letter-spacing:0; }
        .reveal { opacity:0; transform:translateY(22px); transition:opacity .7s var(--ease) var(--d,0s), transform .7s var(--ease) var(--d,0s); }
        .reveal.visible { opacity:1; transform:none; }

        /* ═════════ NAV ═════════ */
        nav.site-nav { position:fixed; top:0; left:0; right:0; z-index:1000; padding:1rem 1.25rem 0; }
        .nav-inner { max-width:1180px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; gap:1rem; height:62px; padding-inline:1rem .5rem; background:var(--nav-bg); backdrop-filter:blur(18px) saturate(1.4); -webkit-backdrop-filter:blur(18px) saturate(1.4); border:1px solid var(--border); border-radius:999px; box-shadow:var(--shadow-sm); }
        .nav-logo { display:flex; align-items:center; gap:.6rem; font:800 1.02rem var(--font-head); text-decoration:none; }
        .nav-logo img { width:36px; height:36px; border-radius:50%; object-fit:cover; object-position:50% 25%; border:2px solid var(--cyan); }
        .nav-logo span span { color:var(--cyan); }
        .nav-back { display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1rem; border-radius:999px; color:var(--text-secondary); text-decoration:none; font-size:.88rem; font-weight:600; transition:all .2s; }
        .nav-back:hover { color:var(--text-primary); background:color-mix(in srgb, var(--text-primary) 5%, transparent); }
        .nav-controls { display:flex; align-items:center; gap:.45rem; }
        .lang-switch { display:flex; gap:.15rem; background:color-mix(in srgb, var(--text-primary) 5%, transparent); border:1px solid var(--border); border-radius:999px; padding:.2rem; }
        .lang-btn { min-width:40px; min-height:34px; display:inline-flex; align-items:center; justify-content:center; padding:0 .6rem; border-radius:999px; font-size:.74rem; font-weight:700; text-decoration:none; color:var(--text-secondary); }
        .lang-btn.active { background:var(--gradient); color:var(--on-accent); }
        .theme-switch { position:relative; }
        .round-btn { width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; border:1px solid var(--border); background:color-mix(in srgb, var(--text-primary) 5%, transparent); color:var(--cyan); cursor:pointer; transition:all .2s; text-decoration:none; }
        .round-btn:hover { border-color:var(--accent-line); background:var(--accent-soft); }
        .theme-menu { position:absolute; top:calc(100% + 12px); inset-inline-end:0; min-width:160px; padding:.4rem; display:flex; flex-direction:column; gap:.15rem; background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:var(--shadow-lg); opacity:0; visibility:hidden; transform:translateY(-8px); transition:all .2s; z-index:1100; }
        .theme-menu.open { opacity:1; visibility:visible; transform:none; }
        .theme-option { display:flex; align-items:center; gap:.6rem; width:100%; padding:.6rem .7rem; border:none; border-radius:10px; background:none; color:var(--text-secondary); font:inherit; font-size:.85rem; text-align:start; cursor:pointer; }
        .theme-option:hover { background:var(--accent-soft); color:var(--text-primary); }
        .theme-option.active { color:var(--cyan); font-weight:700; }
        .theme-swatch { width:16px; height:16px; border-radius:50%; border:1px solid rgba(127,127,127,.3); }
        .sw-light { background:linear-gradient(135deg,#f5f7fb,#00759c); } .sw-dark { background:linear-gradient(135deg,#0a0e17,#00d4d4); }
        .sw-ocean { background:linear-gradient(135deg,#031824,#20e3c2); } .sw-sunset { background:linear-gradient(135deg,#1a0f1a,#ff7a59); }
        .nav-cta { min-height:40px; padding:0 1.1rem; border-radius:999px; font-size:.85rem; }

        /* ═════════ HERO ═════════ */
        .project-hero { position:relative; padding:130px 0 3.5rem; overflow:hidden; }
        .project-hero::before { content:''; position:absolute; width:560px; height:560px; top:-200px; inset-inline-end:-160px; border-radius:50%; background:color-mix(in srgb, var(--cyan) 30%, transparent); filter:blur(90px); opacity:.45; z-index:-1; }
        .breadcrumb ol { list-style:none; display:flex; flex-wrap:wrap; align-items:center; gap:.5rem; font-size:.85rem; color:var(--text-muted); margin-bottom:1.6rem; }
        .breadcrumb a { color:var(--text-secondary); text-decoration:none; font-weight:600; }
        .breadcrumb a:hover { color:var(--cyan); }
        .breadcrumb i { font-size:.6rem; opacity:.6; }
        .breadcrumb [aria-current] { color:var(--text-primary); font-weight:600; }
        .hero-grid { display:grid; grid-template-columns:1.05fr .95fr; gap:3.5rem; align-items:center; }
        .hero-badges { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1.2rem; }
        .project-title { font:800 clamp(2.1rem,4.6vw,3.3rem)/1.12 var(--font-head); letter-spacing:-.03em; margin-bottom:1.1rem; }
        [dir="rtl"] .project-title { letter-spacing:0; }
        .project-lead { font-size:1.12rem; line-height:1.8; color:var(--text-secondary); margin-bottom:2rem; max-width:580px; }
        .hero-actions { display:flex; flex-wrap:wrap; gap:.75rem; }
        .cover { position:relative; aspect-ratio:4/3; border-radius:var(--radius-lg); overflow:hidden; background:var(--gradient); box-shadow:var(--shadow-lg); border:1px solid var(--border); }
        .cover img { width:100%; height:100%; object-fit:cover; }
        .cover-placeholder { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:1.4rem; padding:2rem; }
        .cover-placeholder::before { content:''; position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size:32px 32px; mask-image:radial-gradient(circle at 50% 45%, #000 20%, transparent 75%); -webkit-mask-image:radial-gradient(circle at 50% 45%, #000 20%, transparent 75%); }
        .cover-emoji { position:relative; width:112px; height:112px; border-radius:30px; display:grid; place-items:center; font-size:3.3rem; background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.3); backdrop-filter:blur(8px); box-shadow:0 20px 40px rgba(0,0,0,.18); }
        .cover-stack { position:relative; display:flex; flex-wrap:wrap; justify-content:center; gap:.45rem; max-width:360px; }
        .cover-stack span { padding:.35rem .8rem; border-radius:999px; background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.28); color:#fff; font-size:.8rem; font-weight:600; backdrop-filter:blur(6px); }

        /* ═════════ FACTS ═════════ */
        .facts { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-top:3rem; }
        .fact { display:flex; align-items:center; gap:.9rem; padding:1.1rem 1.2rem; border-radius:var(--radius-md); background:var(--glass); border:1px solid var(--border); backdrop-filter:blur(10px); }
        .fact .icon-tile { width:42px; height:42px; font-size:.95rem; border-radius:12px; }
        .fact small { display:block; font-size:.76rem; color:var(--text-muted); font-weight:600; }
        .fact strong { display:block; font-size:.95rem; line-height:1.35; }

        /* ═════════ CONTENT ═════════ */
        .content { padding:3rem 0 5rem; }
        .content-grid { display:grid; grid-template-columns:minmax(0,1fr) 340px; gap:2rem; align-items:start; }
        .main-col { display:flex; flex-direction:column; gap:1.5rem; }
        .block { padding:2.2rem; }
        .overview-text { font-size:1.06rem; line-height:1.9; color:var(--text-secondary); white-space:pre-line; }
        .stages { list-style:none; position:relative; }
        .stages::before { content:''; position:absolute; top:22px; bottom:22px; inset-inline-start:21px; width:2px; background:linear-gradient(to bottom, var(--cyan), var(--accent-line)); }
        .stage { position:relative; display:flex; gap:1.1rem; align-items:flex-start; padding-bottom:1.3rem; }
        .stage:last-child { padding-bottom:0; }
        .stage-num { position:relative; z-index:1; width:44px; height:44px; flex-shrink:0; border-radius:14px; display:grid; place-items:center; background:var(--bg-card); border:2px solid var(--cyan); color:var(--cyan); font:800 .85rem var(--font-mono); box-shadow:0 0 0 5px var(--bg-card); }
        .stage:last-child .stage-num { background:var(--gradient); border-color:transparent; color:var(--on-accent); }
        .stage-text { padding:.55rem 1rem; flex:1; border-radius:14px; background:color-mix(in srgb, var(--text-primary) 3%, transparent); border:1px solid var(--border); font-size:1rem; line-height:1.65; }
        .empty-note { color:var(--text-muted); font-size:.95rem; }

        /* Sidebar */
        .side-col { position:sticky; top:100px; display:flex; flex-direction:column; gap:1.25rem; }
        .side-card { padding:1.6rem; }
        .side-title { display:flex; align-items:center; gap:.55rem; font:800 1rem var(--font-head); margin-bottom:1rem; }
        .side-title i { color:var(--cyan); }
        .cta-card { position:relative; overflow:hidden; padding:1.8rem; border:none; background:var(--gradient); color:var(--on-accent); }
        .cta-card::after { content:''; position:absolute; width:220px; height:220px; border-radius:50%; border:36px solid rgba(255,255,255,.1); bottom:-110px; inset-inline-end:-80px; }
        .cta-person { display:flex; align-items:center; gap:.75rem; margin-bottom:1rem; position:relative; z-index:1; }
        .cta-person img { width:50px; height:50px; border-radius:50%; object-fit:cover; object-position:50% 22%; border:3px solid rgba(255,255,255,.4); }
        .cta-person small { display:flex; align-items:center; gap:.4rem; font-size:.8rem; opacity:.9; }
        .cta-person strong { font:800 .98rem var(--font-head); }
        .pulse-dot { width:8px; height:8px; border-radius:50%; background:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.3); }
        .cta-card h2 { font:800 1.3rem/1.3 var(--font-head); margin-bottom:.5rem; position:relative; z-index:1; }
        .cta-card p { font-size:.92rem; opacity:.9; margin-bottom:1.3rem; position:relative; z-index:1; }
        .cta-actions { display:grid; gap:.6rem; position:relative; z-index:1; }
        .cta-actions .btn { width:100%; }
        .cta-actions .btn-light { background:rgba(255,255,255,.16); color:inherit; border-color:rgba(255,255,255,.3); }
        .cta-actions .btn-light:hover { background:rgba(255,255,255,.26); }
        .stack-list { display:flex; flex-wrap:wrap; gap:.45rem; }
        .link-list { display:grid; gap:.6rem; }
        .link-list .btn { width:100%; min-height:44px; }
        .share-row { display:flex; gap:.5rem; }
        .share-row .round-btn { width:44px; height:44px; border-radius:12px; }
        .copy-feedback { font-size:.8rem; color:var(--cyan); font-weight:600; min-height:1.2em; margin-top:.5rem; }

        /* ═════════ GALLERY ═════════ */
        .gallery-count { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 .55rem; margin-inline-start:.4rem; border-radius:999px; background:var(--accent-soft); color:var(--cyan); font:700 .85rem var(--font-body); vertical-align:middle; }
        .gallery-grid { list-style:none; display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:.75rem; }
        .gallery-grid li.is-wide { grid-column:1 / -1; }
        .gallery-item { position:relative; display:block; aspect-ratio:4/3; overflow:hidden; border-radius:var(--radius-sm); border:1px solid var(--border); background:var(--bg-primary); }
        .gallery-grid li.is-wide .gallery-item { aspect-ratio:16/8; }
        .gallery-item img { width:100%; height:100%; object-fit:cover; transition:transform .5s var(--ease); }
        .gallery-item:hover img, .gallery-item:focus-visible img { transform:scale(1.05); }
        .gallery-zoom { position:absolute; top:.6rem; inset-inline-end:.6rem; width:34px; height:34px; border-radius:10px; display:grid; place-items:center; background:rgba(2,6,23,.55); color:#fff; font-size:.8rem; opacity:0; transition:opacity .25s; backdrop-filter:blur(6px); }
        .gallery-item:hover .gallery-zoom, .gallery-item:focus-visible .gallery-zoom { opacity:1; }
        .gallery-caption { position:absolute; inset-inline:0; bottom:0; padding:1.4rem .8rem .6rem; background:linear-gradient(to top, rgba(2,6,23,.75), transparent); color:#fff; font-size:.82rem; font-weight:600; text-align:start; }

        /* Lightbox */
        .lightbox { width:100vw; height:100vh; max-width:none; max-height:none; margin:0; padding:0; border:none; background:rgba(2,6,23,.94); color:#fff; overflow:hidden; }
        .lightbox[open] { display:flex; flex-direction:column; animation:lb-in .25s ease; }
        .lightbox::backdrop { background:transparent; }
        @keyframes lb-in { from { opacity:0; } }
        .lightbox-bar { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.9rem 1.2rem; }
        .lightbox-counter { font-size:.9rem; font-weight:600; opacity:.85; }
        .lightbox-btn { width:46px; height:46px; display:inline-grid; place-items:center; border-radius:50%; border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.08); color:#fff; cursor:pointer; font-size:1rem; transition:background .2s; text-decoration:none; }
        .lightbox-btn:hover { background:rgba(255,255,255,.2); }
        .lightbox-btn:focus-visible { outline:2px solid #fff; outline-offset:2px; }
        .lightbox-stage { position:relative; flex:1; display:flex; align-items:center; justify-content:center; padding:0 4.5rem; min-height:0; touch-action:pan-y; }
        .lightbox-stage img { max-width:100%; max-height:100%; object-fit:contain; border-radius:10px; box-shadow:0 20px 60px rgba(0,0,0,.5); user-select:none; }
        .lightbox-nav { position:absolute; top:50%; transform:translateY(-50%); }
        .lightbox-prev { inset-inline-start:1rem; }
        .lightbox-next { inset-inline-end:1rem; }
        .lightbox-caption { min-height:3.2rem; padding:.9rem 1.2rem 1.3rem; text-align:center; font-size:.95rem; opacity:.9; }
        @media (max-width:768px) {
            .gallery-grid { grid-template-columns:repeat(2, 1fr); gap:.5rem; }
            .lightbox-stage { padding:0 .5rem; }
            .lightbox-nav { top:auto; bottom:-3.6rem; transform:none; }
            .lightbox-caption { padding-top:1rem; min-height:4.5rem; }
        }

        /* ═════════ PREV / NEXT ═════════ */
        .pager { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:1.5rem; }
        .pager a { display:flex; align-items:center; gap:1rem; padding:1.2rem 1.4rem; border-radius:var(--radius-md); background:var(--bg-card); border:1px solid var(--border); text-decoration:none; transition:all .25s; min-width:0; }
        .pager a:hover { border-color:var(--accent-line); transform:translateY(-3px); box-shadow:var(--shadow-md); }
        .pager .next { justify-content:flex-end; text-align:end; grid-column:2; }
        .pager small { display:block; font-size:.78rem; color:var(--text-muted); font-weight:600; }
        .pager strong { display:block; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .pager span.info { min-width:0; }
        .pager i { color:var(--cyan); }

        /* ═════════ RELATED ═════════ */
        .related { padding:5rem 0; background:var(--bg-secondary); border-top:1px solid var(--border); }
        .related-head { display:flex; align-items:flex-end; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:2rem; }
        .related-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap:1.25rem; }
        .related-card { display:flex; flex-direction:column; overflow:hidden; text-decoration:none; transition:transform .3s var(--ease), box-shadow .3s, border-color .3s; }
        .related-card:hover { transform:translateY(-5px); box-shadow:var(--shadow-lg); border-color:var(--accent-line); }
        .related-cover { position:relative; aspect-ratio:16/9; background:var(--gradient); display:grid; place-items:center; font-size:2.2rem; overflow:hidden; }
        .related-cover img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .related-body { padding:1.3rem 1.4rem 1.5rem; display:flex; flex-direction:column; gap:.5rem; flex:1; }
        .related-body small { color:var(--cyan); font-weight:700; font-size:.78rem; }
        .related-body h3 { font:800 1.08rem/1.35 var(--font-head); }
        .related-body p { color:var(--text-secondary); font-size:.92rem; line-height:1.7; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .related-more { margin-top:auto; padding-top:.6rem; color:var(--cyan); font-weight:700; font-size:.88rem; display:inline-flex; align-items:center; gap:.4rem; }

        /* ═════════ FOOTER ═════════ */
        footer { padding:2rem 0; border-top:1px solid var(--border); background:var(--bg-primary); }
        .footer-inner { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; color:var(--text-muted); font-size:.88rem; }
        .footer-inner a { color:var(--text-secondary); text-decoration:none; font-weight:600; }
        .footer-inner a:hover { color:var(--cyan); }

        /* ═════════ RESPONSIVE ═════════ */
        @media (max-width:1024px) {
            .hero-grid { grid-template-columns:1fr; gap:2.5rem; }
            .facts { grid-template-columns:repeat(2,1fr); }
            .content-grid { grid-template-columns:1fr; }
            .side-col { position:static; }
        }
        @media (max-width:768px) {
            nav.site-nav { padding:.75rem .75rem 0; }
            .nav-back-label, .nav-cta { display:none; }
            .container { padding-inline:1rem; }
            .project-hero { padding-top:110px; }
            .facts { grid-template-columns:1fr 1fr; gap:.75rem; margin-top:2rem; }
            .fact { flex-direction:column; align-items:flex-start; gap:.6rem; padding:1rem; }
            .fact .icon-tile { width:36px; height:36px; font-size:.85rem; border-radius:10px; }
            .fact strong { font-size:.9rem; }
            .block { padding:1.5rem; }
            .pager { grid-template-columns:1fr; }
            .pager .next { grid-column:auto; }
            .hero-actions .btn { flex:1 1 100%; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior:auto; }
            *, *::before, *::after { animation-duration:.01ms !important; transition-duration:.01ms !important; }
            .reveal { opacity:1; transform:none; }
        }
        @media print {
            nav.site-nav, .side-col .cta-card, .related, .scroll-progress, .wa-sticker { display:none !important; }
            .reveal { opacity:1; transform:none; }
        }
    </style>
</head>
<body>

<a href="#main" class="skip-link">{{ __('Skip to content') }}</a>
<div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>

<!-- ═════════ NAV ═════════ -->
<nav class="site-nav" aria-label="{{ __('Main navigation') }}">
    <div class="nav-inner">
        <a class="nav-logo" href="{{ $homeUrl }}" aria-label="{{ $heroName }}">
            <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="36" height="36">
            <span>{{ $firstName }}<span>.</span></span>
        </a>
        <a class="nav-back" href="{{ $homeUrl }}#projects"><i class="fas {{ $backIcon }}"></i> <span class="nav-back-label">{{ __('All projects') }}</span></a>
        <div class="nav-controls">
            <div class="lang-switch">
                <a href="{{ url()->current() }}?lang=en" hreflang="en" lang="en" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ url()->current() }}?lang=ar" hreflang="ar" lang="ar" class="lang-btn {{ $locale === 'ar' ? 'active' : '' }}">عربي</a>
            </div>
            <div class="theme-switch" id="themeSwitch">
                <button type="button" class="round-btn" id="themeToggleBtn" aria-label="{{ __('Theme') }}" aria-haspopup="true" aria-expanded="false" aria-controls="themeMenu"><i class="fas fa-palette"></i></button>
                <div class="theme-menu" id="themeMenu">
                    <button type="button" class="theme-option" data-theme-choice="light"><span class="theme-swatch sw-light"></span>{{ __('Light') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="dark"><span class="theme-swatch sw-dark"></span>{{ __('Dark') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="ocean"><span class="theme-swatch sw-ocean"></span>{{ __('Ocean') }}</button>
                    <button type="button" class="theme-option" data-theme-choice="sunset"><span class="theme-swatch sw-sunset"></span>{{ __('Sunset') }}</button>
                </div>
            </div>
            <a href="{{ $homeUrl }}#contact" class="btn btn-primary nav-cta">{{ __('Start a Project') }}</a>
        </div>
    </div>
</nav>

<main id="main">

<!-- ═════════ HERO ═════════ -->
<header class="project-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
            <ol>
                <li><a href="{{ $homeUrl }}">{{ __('Home') }}</a></li>
                <li aria-hidden="true"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}"></i></li>
                <li><a href="{{ $homeUrl }}#projects">{{ __('Projects') }}</a></li>
                <li aria-hidden="true"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}"></i></li>
                <li aria-current="page">{{ $projectTitle }}</li>
            </ol>
        </nav>

        <div class="hero-grid">
            <div>
                <div class="hero-badges">
                    @if($category)<span class="chip"><i class="fas fa-tag"></i> {{ $category }}</span>@endif
                    @if($project->featured)<span class="chip chip-featured"><i class="fas fa-star"></i> {{ __('Featured') }}</span>@endif
                    @if($duration)<span class="chip chip-muted"><i class="far fa-calendar"></i> {{ $duration }}</span>@endif
                </div>
                <h1 class="project-title">{{ $projectTitle }}</h1>
                <p class="project-lead">{{ t($project, 'description') }}</p>
                <div class="hero-actions">
                    <a href="{{ $similarUrl }}" @if($whatsappNumber) target="_blank" rel="noopener" @endif class="btn btn-primary"><i class="fas fa-comments"></i> {{ __('I want something similar') }}</a>
                    @if($project->live_url)
                        <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn btn-ghost"><i class="fas fa-arrow-up-right-from-square"></i> {{ __('Live Demo') }}</a>
                    @endif
                    @if($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="btn btn-ghost"><i class="fab fa-github"></i> {{ __('Source Code') }}</a>
                    @endif
                </div>
            </div>

            <div class="cover">
                @if($projectImage)
                    <img src="{{ $projectImage }}" alt="{{ $projectTitle }}" fetchpriority="high">
                @else
                    <div class="cover-placeholder" aria-hidden="true">
                        <div class="cover-emoji">{{ $project->icon ?: '🚀' }}</div>
                        @if(!empty($stack))
                            <div class="cover-stack">
                                @foreach(array_slice($stack, 0, 6) as $tech)<span>{{ $tech }}</span>@endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <dl class="facts">
            <div class="fact">
                <span class="icon-tile" aria-hidden="true"><i class="fas fa-user-tie"></i></span>
                <div><dt><small>{{ __('Client') }}</small></dt><dd><strong>{{ $client ?: __('Not specified') }}</strong></dd></div>
            </div>
            <div class="fact">
                <span class="icon-tile" aria-hidden="true"><i class="fas fa-clock"></i></span>
                <div><dt><small>{{ __('Duration') }}</small></dt><dd><strong>{{ $duration ?: __('Not specified') }}</strong></dd></div>
            </div>
            <div class="fact">
                <span class="icon-tile" aria-hidden="true"><i class="fas fa-shapes"></i></span>
                <div><dt><small>{{ __('Category') }}</small></dt><dd><strong>{{ $category ?: __('Not specified') }}</strong></dd></div>
            </div>
            <div class="fact">
                <span class="icon-tile" aria-hidden="true"><i class="fas fa-layer-group"></i></span>
                <div><dt><small>{{ __('Technologies') }}</small></dt><dd><strong>{{ count($stack) }} {{ __('tools') }}</strong></dd></div>
            </div>
        </dl>
    </div>
</header>

<!-- ═════════ CONTENT ═════════ -->
<section class="content">
    <div class="container">
        <div class="content-grid">
            <div class="main-col">
                @if($overview)
                <article class="card block reveal">
                    <div class="eyebrow">{{ __('Overview') }}</div>
                    <h2 class="block-title">{{ __('The challenge & the solution') }}</h2>
                    <p class="overview-text">{{ $overview }}</p>
                </article>
                @endif

                @if(!empty($gallery))
                <article class="card block reveal" id="gallery" aria-labelledby="galleryTitle">
                    <div class="eyebrow">{{ __('Gallery') }}</div>
                    <h2 class="block-title" id="galleryTitle">{{ __('Screenshots') }} <span class="gallery-count">{{ count($gallery) }}</span></h2>
                    <ul class="gallery-grid">
                        @foreach($gallery as $image)
                            @php($caption = t($image, 'caption'))
                            <li class="{{ $loop->first && count($gallery) > 2 ? 'is-wide' : '' }}">
                                <a href="{{ asset('uploads/'.$image->path) }}" class="gallery-item" data-gallery-index="{{ $loop->index }}"
                                   data-full="{{ asset('uploads/'.$image->path) }}" data-caption="{{ $caption }}"
                                   aria-label="{{ __('Open image :number of :total', ['number' => $loop->iteration, 'total' => count($gallery)]) }}{{ $caption ? ' — '.$caption : '' }}">
                                    <img src="{{ asset('uploads/'.($loop->first && count($gallery) > 2 ? $image->path : $image->thumb_path)) }}"
                                         alt="{{ $caption ?: $projectTitle.' — '.__('screenshot :number', ['number' => $loop->iteration]) }}"
                                         loading="lazy" decoding="async"
                                         @if($image->width && $image->height) width="{{ $image->width }}" height="{{ $image->height }}" @endif>
                                    <span class="gallery-zoom" aria-hidden="true"><i class="fas fa-expand"></i></span>
                                    @if($caption)<span class="gallery-caption">{{ $caption }}</span>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </article>
                @endif

                <article class="card block reveal">
                    <div class="eyebrow">{{ __('Work Stages') }}</div>
                    <h2 class="block-title">{{ __('How I built it') }}</h2>
                    @if(!empty($stages))
                        <ol class="stages">
                            @foreach($stages as $stage)
                                <li class="stage">
                                    <span class="stage-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="stage-text">{{ $stage }}</span>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="empty-note">{{ __('No stages added yet.') }}</p>
                    @endif
                </article>

                @if($previousProject || $nextProject)
                <nav class="pager reveal" aria-label="{{ __('More projects') }}">
                    @if($previousProject)
                        <a href="{{ \App\Http\Middleware\SetLocale::localizedUrl(route('project.show', $previousProject->id), $locale) }}" class="prev" rel="prev">
                            <i class="fas {{ $backIcon }}"></i>
                            <span class="info"><small>{{ __('Previous project') }}</small><strong>{{ t($previousProject, 'title') }}</strong></span>
                        </a>
                    @endif
                    @if($nextProject)
                        <a href="{{ \App\Http\Middleware\SetLocale::localizedUrl(route('project.show', $nextProject->id), $locale) }}" class="next" rel="next">
                            <span class="info"><small>{{ __('Next project') }}</small><strong>{{ t($nextProject, 'title') }}</strong></span>
                            <i class="fas {{ $arrowIcon }}"></i>
                        </a>
                    @endif
                </nav>
                @endif
            </div>

            <aside class="side-col" aria-label="{{ __('Project Details') }}">
                <div class="card cta-card">
                    <div class="cta-person">
                        <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="50" height="50" loading="lazy">
                        <div><strong>{{ $heroName }}</strong><small><span class="pulse-dot"></span>{{ __('Available for new projects') }}</small></div>
                    </div>
                    <h2>{{ __('Need something similar?') }}</h2>
                    <p>{{ __('Tell me about your idea or your current system — I will reply with a clear plan, timeline, and price.') }}</p>
                    <div class="cta-actions">
                        @if($whatsappNumber)
                            <a href="{{ $similarUrl }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><i class="fab fa-whatsapp"></i> {{ __('Chat on WhatsApp') }}</a>
                        @endif
                        <a href="{{ $homeUrl }}#contact" class="btn btn-light">{{ __('Discuss Your Project') }} <i class="fas {{ $arrowIcon }}"></i></a>
                    </div>
                </div>

                @if(!empty($stack))
                <div class="card side-card">
                    <h2 class="side-title"><i class="fas fa-layer-group"></i> {{ __('Tech Stack') }}</h2>
                    <div class="stack-list">
                        @foreach($stack as $tech)<span class="chip chip-muted">{{ $tech }}</span>@endforeach
                    </div>
                </div>
                @endif

                @if($project->live_url || $project->github_url)
                <div class="card side-card">
                    <h2 class="side-title"><i class="fas fa-link"></i> {{ __('Project links') }}</h2>
                    <div class="link-list">
                        @if($project->live_url)<a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="fas fa-arrow-up-right-from-square"></i> {{ __('Live Demo') }}</a>@endif
                        @if($project->github_url)<a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="btn btn-ghost"><i class="fab fa-github"></i> {{ __('Source Code') }}</a>@endif
                    </div>
                </div>
                @endif

                <div class="card side-card">
                    <h2 class="side-title"><i class="fas fa-share-nodes"></i> {{ __('Share this project') }}</h2>
                    <div class="share-row">
                        <button type="button" class="round-btn" id="copyLink" data-url="{{ $pageUrl }}" aria-label="{{ __('Copy link') }}" title="{{ __('Copy link') }}"><i class="fas fa-link"></i></button>
                        <a class="round-btn" href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode($pageUrl) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on LinkedIn') }}" title="{{ __('Share on LinkedIn') }}"><i class="fab fa-linkedin-in"></i></a>
                        <a class="round-btn" href="https://wa.me/?text={{ rawurlencode($projectTitle.' — '.$pageUrl) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on WhatsApp') }}" title="{{ __('Share on WhatsApp') }}"><i class="fab fa-whatsapp"></i></a>
                        <a class="round-btn" href="https://x.com/intent/post?url={{ rawurlencode($pageUrl) }}&text={{ rawurlencode($projectTitle) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on X') }}" title="{{ __('Share on X') }}"><i class="fab fa-x-twitter"></i></a>
                    </div>
                    <p class="copy-feedback" id="copyFeedback" role="status"></p>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- ═════════ RELATED ═════════ -->
@if(!empty($related))
<section class="related" aria-labelledby="relatedTitle">
    <div class="container">
        <div class="related-head">
            <div>
                <div class="eyebrow">{{ __('Related Projects') }}</div>
                <h2 class="block-title" id="relatedTitle" style="margin:0">{{ __('More work you might like') }}</h2>
            </div>
            <a href="{{ $homeUrl }}#projects" class="btn btn-ghost">{{ __('All projects') }} <i class="fas {{ $arrowIcon }}"></i></a>
        </div>
        <div class="related-grid">
            @foreach($related as $rel)
            <a href="{{ \App\Http\Middleware\SetLocale::localizedUrl(route('project.show', $rel->id), $locale) }}" class="card related-card reveal" style="--d: {{ $loop->index * 0.08 }}s">
                <div class="related-cover" aria-hidden="true">
                    @if($rel->image)
                        <img src="{{ project_image_url($rel->image) }}" alt="" loading="lazy">
                    @else
                        {{ $rel->icon ?: '🚀' }}
                    @endif
                </div>
                <div class="related-body">
                    @if(t($rel, 'category'))<small>{{ t($rel, 'category') }}</small>@endif
                    <h3>{{ t($rel, 'title') }}</h3>
                    <p>{{ t($rel, 'description') }}</p>
                    <span class="related-more">{{ __('View Details') }} <i class="fas {{ $arrowIcon }}"></i></span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

</main>

<footer>
    <div class="container footer-inner">
        <a href="{{ $homeUrl }}">{{ $heroName }} · {{ $heroTagline }}</a>
        <span>{{ ts($settings, 'footer_text') ?: ($settings['footer_text'] ?? '') }}</span>
    </div>
</footer>

@if(!empty($gallery))
<dialog class="lightbox" id="lightbox" aria-label="{{ __('Gallery') }} — {{ $projectTitle }}">
    <div class="lightbox-bar">
        <span class="lightbox-counter" id="lightboxCounter" aria-live="polite"></span>
        <div style="display:flex;gap:.5rem">
            <a class="lightbox-btn" id="lightboxOpen" href="#" target="_blank" rel="noopener" aria-label="{{ __('Open in new tab') }}"><i class="fas fa-up-right-from-square"></i></a>
            <button type="button" class="lightbox-btn" id="lightboxClose" aria-label="{{ __('Close') }}" autofocus><i class="fas fa-xmark"></i></button>
        </div>
    </div>
    <div class="lightbox-stage" id="lightboxStage">
        <button type="button" class="lightbox-btn lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="{{ __('Previous image') }}"><i class="fas {{ $backIcon }}"></i></button>
        <img id="lightboxImage" src="" alt="">
        <button type="button" class="lightbox-btn lightbox-nav lightbox-next" id="lightboxNext" aria-label="{{ __('Next image') }}"><i class="fas {{ $arrowIcon }}"></i></button>
    </div>
    <p class="lightbox-caption" id="lightboxCaption"></p>
</dialog>
@endif

<script>
// Theme menu (shared with the homepage via localStorage)
(function () {
    const root = document.documentElement;
    const btn = document.getElementById('themeToggleBtn');
    const menu = document.getElementById('themeMenu');
    const options = document.querySelectorAll('.theme-option');
    const apply = theme => {
        root.setAttribute('data-theme', theme);
        try { localStorage.setItem('theme', theme); } catch (e) {}
        options.forEach(o => o.classList.toggle('active', o.dataset.themeChoice === theme));
    };
    const close = focus => { menu.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); if (focus) btn.focus(); };
    apply(root.getAttribute('data-theme') || 'light');
    btn.addEventListener('click', e => { e.stopPropagation(); btn.setAttribute('aria-expanded', menu.classList.toggle('open')); });
    options.forEach(o => o.addEventListener('click', () => { apply(o.dataset.themeChoice); close(true); }));
    document.addEventListener('click', e => { if (!document.getElementById('themeSwitch').contains(e.target)) close(false); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && menu.classList.contains('open')) close(true); });
})();

// Reading progress
(function () {
    const bar = document.getElementById('scrollProgress');
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            const max = document.documentElement.scrollHeight - window.innerHeight;
            bar.style.transform = 'scaleX(' + (max > 0 ? window.scrollY / max : 0) + ')';
            ticking = false;
        });
    }, { passive: true });
})();

// Scroll reveal
(function () {
    const observer = new IntersectionObserver(entries => entries.forEach(entry => {
        if (entry.isIntersecting) { entry.target.classList.add('visible'); observer.unobserve(entry.target); }
    }), { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
})();

// Copy link
(function () {
    const button = document.getElementById('copyLink');
    const feedback = document.getElementById('copyFeedback');
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.url);
            feedback.textContent = @json(__('Link copied!'));
        } catch (e) {
            window.prompt(@json(__('Copy link')), button.dataset.url);
        }
        setTimeout(() => { feedback.textContent = ''; }, 2500);
    });
})();

// Gallery lightbox (links open the full image when JavaScript or <dialog> is unavailable)
(function () {
    const dialog = document.getElementById('lightbox');
    if (!dialog || typeof dialog.showModal !== 'function') return;
    const items = [...document.querySelectorAll('.gallery-item')];
    const image = document.getElementById('lightboxImage');
    const caption = document.getElementById('lightboxCaption');
    const counter = document.getElementById('lightboxCounter');
    const openLink = document.getElementById('lightboxOpen');
    const isRtl = document.documentElement.dir === 'rtl';
    const ofLabel = @json(__('of'));
    const title = @json($projectTitle);
    let index = 0;
    let opener = null;

    function show(i) {
        index = (i + items.length) % items.length;
        const item = items[index];
        image.src = item.dataset.full;
        image.alt = item.querySelector('img').alt;
        caption.textContent = item.dataset.caption || title;
        counter.textContent = (index + 1) + ' ' + ofLabel + ' ' + items.length;
        openLink.href = item.dataset.full;
        // Preload neighbours for instant navigation
        [index + 1, index - 1].forEach(n => { const next = items[(n + items.length) % items.length]; if (next) new Image().src = next.dataset.full; });
    }
    const next = () => show(index + 1);
    const prev = () => show(index - 1);

    items.forEach((item, i) => item.addEventListener('click', e => {
        e.preventDefault();
        opener = item;
        show(i);
        dialog.showModal();
        document.documentElement.style.overflow = 'hidden';
    }));
    dialog.addEventListener('close', () => {
        document.documentElement.style.overflow = '';
        if (opener) opener.focus();
    });
    document.getElementById('lightboxClose').addEventListener('click', () => dialog.close());
    document.getElementById('lightboxNext').addEventListener('click', next);
    document.getElementById('lightboxPrev').addEventListener('click', prev);
    dialog.addEventListener('click', e => { if (e.target === dialog || e.target.id === 'lightboxStage') dialog.close(); });
    dialog.addEventListener('keydown', e => {
        if (e.key === 'ArrowRight') { isRtl ? prev() : next(); }
        if (e.key === 'ArrowLeft') { isRtl ? next() : prev(); }
    });
    if (items.length < 2) {
        document.getElementById('lightboxNext').hidden = true;
        document.getElementById('lightboxPrev').hidden = true;
    }

    // Swipe on touch screens
    let startX = null;
    const stage = document.getElementById('lightboxStage');
    stage.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    stage.addEventListener('touchend', e => {
        if (startX === null) return;
        const delta = e.changedTouches[0].clientX - startX;
        if (Math.abs(delta) > 50) { (delta < 0) !== isRtl ? next() : prev(); }
        startX = null;
    });
})();
</script>
@include('partials.whatsapp-sticker')
</body>
</html>
