<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['hero_name'] ?? 'Portfolio' }} — {{ $settings['hero_tagline'] ?? 'Developer' }}</title>
    <meta name="description" content="{{ strip_tags($settings['hero_subtitle'] ?? '') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
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
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { font-family:{{ $locale === 'ar' ? "'Cairo'" : "'Inter'" }},sans-serif; background:var(--bg-primary); color:var(--text-primary); line-height:1.6; overflow-x:hidden; cursor:none; }
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
            padding:0 2rem;
            background:rgba(10,14,23,0.85);
            backdrop-filter:blur(20px);
            border-bottom:1px solid var(--border);
            transition:all 0.3s;
        }
        nav.scrolled { border-bottom-color:rgba(0,212,212,0.2); box-shadow:0 4px 30px rgba(0,0,0,0.5); }
        .nav-inner { max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; height:70px; gap:1rem; }
        .nav-logo { font-size:1.4rem; font-weight:800; color:var(--cyan); text-decoration:none; letter-spacing:-0.5px; }
        .nav-logo span { color:var(--text-primary); }
        .nav-links { display:flex; gap:2rem; list-style:none; }
        .nav-links a { color:var(--text-secondary); text-decoration:none; font-size:0.875rem; font-weight:500; letter-spacing:0.5px; transition:color 0.2s; position:relative; }
        .nav-links a::after { content:''; position:absolute; bottom:-4px; left:0; width:0; height:1px; background:var(--cyan); transition:width 0.3s; }
        .nav-links a:hover, .nav-links a.active { color:var(--cyan); }
        .nav-links a:hover::after, .nav-links a.active::after { width:100%; }
        .nav-cta { padding:0.5rem 1.25rem; border:1px solid var(--cyan); border-radius:6px; color:var(--cyan) !important; font-weight:600; }
        .nav-cta:hover { background:var(--cyan); color:var(--bg-primary) !important; }
        .hamburger { display:none; flex-direction:column; gap:5px; cursor:none; }
        .hamburger span { display:block; width:24px; height:2px; background:var(--text-primary); border-radius:2px; transition:all 0.3s; }
        .lang-switch { display:flex; align-items:center; gap:0.25rem; background:rgba(0,212,212,0.06); border:1px solid rgba(0,212,212,0.2); border-radius:50px; padding:0.25rem; flex-shrink:0; }
        .lang-btn { padding:0.3rem 0.7rem; border-radius:50px; font-size:0.75rem; font-weight:600; text-decoration:none; color:var(--text-secondary); transition:all .2s; font-family:'JetBrains Mono',monospace; cursor:none; }
        .lang-btn.active { background:var(--gradient); color:#0a0e17; }
        .lang-btn:not(.active):hover { color:var(--cyan); }

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
        .hero-title .name { display:block; background:linear-gradient(135deg,#fff 0%,var(--text-primary) 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .hero-title .role { display:block; background:var(--gradient); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; min-height:1.2em; }
        .hero-subtitle { font-size:1.05rem; color:var(--text-secondary); line-height:1.75; margin-bottom:2.5rem; max-width:500px; }
        .hero-actions { display:flex; gap:1rem; flex-wrap:wrap; }
        .btn-primary { display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:var(--gradient); color:var(--bg-primary); border-radius:8px; font-weight:700; font-size:0.9rem; text-decoration:none; transition:all 0.3s; border:none; cursor:none; box-shadow:0 0 30px rgba(0,212,212,0.3); }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 0 50px rgba(0,212,212,0.5); }
        .btn-secondary { display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:transparent; color:var(--text-primary); border:1px solid var(--border); border-radius:8px; font-weight:600; font-size:0.9rem; text-decoration:none; transition:all 0.3s; cursor:none; }
        .btn-secondary:hover { border-color:var(--cyan); color:var(--cyan); transform:translateY(-2px); }
        .hero-stats { display:flex; gap:2rem; margin-top:3rem; padding-top:2rem; border-top:1px solid var(--border); }
        .stat-number { font-size:1.75rem; font-weight:800; color:var(--cyan); display:block; }
        .stat-label { font-size:0.78rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; }
        .hero-visual { display:flex; justify-content:center; align-items:center; position:relative; }
        .code-block { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:2rem; font-family:'JetBrains Mono',monospace; font-size:0.82rem; line-height:1.8; position:relative; overflow:hidden; max-width:420px; width:100%; box-shadow:0 25px 60px rgba(0,0,0,0.5); }
        .code-block::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--gradient); }
        .code-dots { display:flex; gap:6px; margin-bottom:1.25rem; }
        .code-dots span { width:12px; height:12px; border-radius:50%; }
        .code-dots span:nth-child(1){background:#ff5f57} .code-dots span:nth-child(2){background:#ffbd2e} .code-dots span:nth-child(3){background:#28ca41}
        .code-line { display:flex; gap:0.5rem; }
        .ln{color:#2d3f5a;min-width:20px;user-select:none} .kw{color:#c792ea} .fn{color:#82aaff} .cl{color:#00d4d4} .st{color:#c3e88d} .cm{color:#546e7a} .ar{color:#f78c6c} .op{color:#89ddff}
        .floating-card { position:absolute; background:var(--bg-card); border:1px solid rgba(0,212,212,0.2); border-radius:10px; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.5rem; font-size:0.8rem; font-weight:600; box-shadow:0 10px 30px rgba(0,0,0,0.4); animation:float 4s ease-in-out infinite; }
        .floating-card.card-1{top:-20px;right:-30px;animation-delay:0s} .floating-card.card-2{bottom:20px;left:-30px;animation-delay:2s}
        .floating-card i{color:var(--cyan)}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}

        /* ─── ABOUT ─── */
        #about { padding:8rem 2rem; background:var(--bg-secondary); }
        .about-grid { display:grid; grid-template-columns:1fr 1.5fr; gap:5rem; align-items:center; }
        .avatar-ring { width:280px; height:280px; border-radius:50%; background:var(--gradient); padding:3px; position:relative; animation:spin-slow 10s linear infinite; margin:0 auto; }
        @keyframes spin-slow{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
        .avatar-inner { width:100%; height:100%; border-radius:50%; background:var(--bg-secondary); display:flex; align-items:center; justify-content:center; animation:spin-slow 10s linear infinite reverse; }
        .avatar-initials { font-size:4.5rem; font-weight:900; background:var(--gradient); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .about-avatar-wrap { position:relative; }
        .avatar-badge { position:absolute; bottom:25px; right:calc(50% - 160px); background:var(--bg-card); border:2px solid var(--cyan); border-radius:50px; padding:0.4rem 0.8rem; font-size:0.75rem; font-weight:700; color:var(--cyan); }
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
        .skill-category:hover { border-color:rgba(0,212,212,0.3); transform:translateY(-4px); box-shadow:0 20px 40px rgba(0,0,0,0.3); }
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
        #experience { padding:8rem 2rem; background:var(--bg-secondary); }
        .timeline { position:relative; max-width:800px; margin:0 auto; }
        .timeline::before { content:''; position:absolute; left:20px; top:0; bottom:0; width:1px; background:linear-gradient(to bottom,transparent,var(--cyan),transparent); }
        .timeline-item { padding-left:60px; margin-bottom:3rem; position:relative; }
        .timeline-dot { position:absolute; left:12px; top:8px; width:17px; height:17px; border-radius:50%; background:var(--bg-secondary); border:2px solid var(--cyan); transition:all 0.3s; }
        .timeline-item:hover .timeline-dot { background:var(--cyan); box-shadow:0 0 15px rgba(0,212,212,0.6); }
        .timeline-card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:1.75rem; transition:all 0.3s; }
        .timeline-card:hover { border-color:rgba(0,212,212,0.3); transform:translateX(4px); }
        .timeline-header { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:0.75rem; flex-wrap:wrap; }
        .timeline-title { font-size:1.1rem; font-weight:700; }
        .timeline-company { font-size:0.9rem; color:var(--cyan); margin-bottom:0.25rem; }
        .timeline-date { font-size:0.78rem; color:var(--text-muted); font-family:'JetBrains Mono',monospace; white-space:nowrap; padding:0.25rem 0.75rem; background:rgba(0,212,212,0.06); border-radius:50px; border:1px solid rgba(0,212,212,0.15); }
        .timeline-desc { color:var(--text-secondary); font-size:0.9rem; line-height:1.7; margin-bottom:1rem; }
        .timeline-tags { display:flex; flex-wrap:wrap; gap:0.4rem; }
        .timeline-tag { padding:0.2rem 0.6rem; background:rgba(0,212,212,0.06); border:1px solid rgba(0,212,212,0.15); border-radius:4px; font-size:0.75rem; color:var(--cyan); font-family:'JetBrains Mono',monospace; }

        /* ─── PROJECTS ─── */
        #projects { padding:8rem 2rem; }
        .projects-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(340px,1fr)); gap:1.5rem; }
        .project-card { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; overflow:hidden; transition:all 0.3s; display:flex; flex-direction:column; }
        .project-card:hover { border-color:rgba(0,212,212,0.35); transform:translateY(-6px); box-shadow:0 25px 50px rgba(0,0,0,0.4); }
        .project-header { padding:1.75rem 1.75rem 1rem; display:flex; justify-content:space-between; align-items:flex-start; }
        .project-icon { width:48px; height:48px; border-radius:12px; background:rgba(0,212,212,0.1); border:1px solid rgba(0,212,212,0.2); display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
        .project-links { display:flex; gap:0.5rem; }
        .project-link { width:34px; height:34px; border-radius:6px; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--text-muted); text-decoration:none; font-size:0.85rem; transition:all 0.2s; cursor:none; }
        .project-link:hover { border-color:var(--cyan); color:var(--cyan); }
        .project-body { padding:0 1.75rem 1.75rem; flex:1; display:flex; flex-direction:column; }
        .project-title { font-size:1.15rem; font-weight:700; margin-bottom:0.6rem; }
        .project-desc { color:var(--text-secondary); font-size:0.875rem; line-height:1.7; flex:1; margin-bottom:1.25rem; }
        .project-stack { display:flex; flex-wrap:wrap; gap:0.4rem; }
        .stack-tag { padding:0.25rem 0.6rem; background:rgba(255,255,255,0.04); border:1px solid var(--border); border-radius:4px; font-size:0.75rem; color:var(--text-muted); font-family:'JetBrains Mono',monospace; }
        .featured-badge { display:inline-flex; align-items:center; gap:0.3rem; font-size:0.7rem; color:var(--cyan); margin-bottom:0.5rem; }

        /* ─── CONTACT ─── */
        #contact { padding:8rem 2rem; background:var(--bg-secondary); }
        .contact-wrapper { max-width:700px; margin:0 auto; text-align:center; }
        .contact-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin:3rem 0; }
        .contact-card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:1.5rem; text-decoration:none; transition:all 0.3s; display:flex; flex-direction:column; align-items:center; gap:0.75rem; cursor:none; }
        .contact-card:hover { border-color:var(--cyan); transform:translateY(-4px); box-shadow:0 15px 35px rgba(0,0,0,0.3); }
        .contact-card i { font-size:1.5rem; color:var(--cyan); }
        .contact-card-label { font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); }
        .contact-card-value { font-size:0.875rem; color:var(--text-primary); word-break:break-all; }
        .contact-form { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:2.5rem; margin-top:2rem; text-align:left; }
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
            .nav-links{display:none}
            .hamburger{display:flex}
            .nav-links.open{display:flex;flex-direction:column;position:absolute;top:70px;left:0;right:0;background:rgba(10,14,23,0.98);padding:1.5rem 2rem;border-bottom:1px solid var(--border);gap:1.25rem}
            .hero-inner{grid-template-columns:1fr;text-align:center}
            .hero-visual{display:none}
            .hero-subtitle{max-width:100%}
            .hero-actions{justify-content:center}
            .hero-stats{justify-content:center}
            .about-grid{grid-template-columns:1fr;text-align:center}
            .about-links{justify-content:center}
            .about-tags{justify-content:center}
            .form-row{grid-template-columns:1fr}
            .timeline::before{left:0}
            .timeline-item{padding-left:30px}
            .timeline-dot{left:-8px}
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
        <a class="nav-logo" href="#hero">B<span>.</span></a>
        <ul class="nav-links" id="navLinks">
            <li><a href="#about">{{ __('About') }}</a></li>
            <li><a href="#skills">{{ __('Skills') }}</a></li>
            <li><a href="#experience">{{ __('Experience') }}</a></li>
            <li><a href="#projects">{{ __('Projects') }}</a></li>
            <li><a class="nav-cta" href="#contact">{{ __('Hire Me') }}</a></li>
        </ul>
        <div style="display:flex;align-items:center;gap:0.75rem">
            <div class="lang-switch">
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn {{ $locale === 'ar' ? 'active' : '' }}">عربي</a>
            </div>
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
            <div class="hero-badge">Available for opportunities</div>
            <h1 class="hero-title">
                <span class="name">{{ $settings['hero_name'] ?? 'Your Name' }}</span>
                <span class="role" id="typing-role">{{ $settings['hero_tagline'] ?? 'Developer' }}</span>
            </h1>
            <p class="hero-subtitle">{!! $settings['hero_subtitle'] ?? '' !!}</p>
            <div class="hero-actions">
                <a href="#projects" class="btn-primary"><i class="fas fa-rocket"></i> View My Work</a>
                <a href="#contact" class="btn-secondary"><i class="fas fa-envelope"></i> Get In Touch</a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $settings['hero_stat_years'] ?? '5+' }}</span>
                    <span class="stat-label">Years Exp.</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $settings['hero_stat_projects'] ?? '30+' }}</span>
                    <span class="stat-label">Projects</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $settings['hero_stat_clients'] ?? '15+' }}</span>
                    <span class="stat-label">Happy Clients</span>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="floating-card card-1"><i class="fas fa-check-circle"></i><span>Laravel Expert</span></div>
            <div class="code-block">
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
            <div class="floating-card card-2"><i class="fas fa-star"></i><span>Open to Work</span></div>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-avatar-wrap reveal">
                <div class="avatar-ring">
                    <div class="avatar-inner">
                        @php
                            $name = $settings['hero_name'] ?? 'BA';
                            $parts = explode(' ', $name);
                            $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . substr(end($parts) ?? '', 0, 1));
                        @endphp
                        <span class="avatar-initials">{{ $initials }}</span>
                    </div>
                </div>
                <div class="avatar-badge"><i class="fas fa-code"></i> Backend Dev</div>
            </div>
            <div class="reveal">
                <div class="section-tag">// who am I</div>
                <h2>{!! $settings['about_heading'] ?? 'About Me' !!}</h2>
                @if(!empty($settings['about_p1']))<p>{{ $settings['about_p1'] }}</p>@endif
                @if(!empty($settings['about_p2']))<p>{{ $settings['about_p2'] }}</p>@endif
                @if(!empty($settings['about_p3']))<p>{{ $settings['about_p3'] }}</p>@endif
                @if(!empty($settings['about_tags']))
                <div class="about-tags">
                    @foreach(array_map('trim', explode(',', $settings['about_tags'])) as $t)
                        <span class="tag">{{ $t }}</span>
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
            <div class="section-tag">// expertise</div>
            <h2 class="section-title">Technical <span>Skills</span></h2>
            <div class="section-line"></div>
        </div>
        @if(!empty($categories))
        <div class="skills-grid">
            @foreach($categories as $cat)
            <div class="skill-category reveal">
                <div class="skill-cat-icon">{{ $cat->icon }}</div>
                <div class="skill-cat-title">{{ $cat->name }}</div>
                @if($cat->type === 'bars')
                    <div class="skill-items">
                        @foreach($cat->skills as $skill)
                        <div class="skill-item">
                            <div class="skill-info">
                                <span class="skill-name">{{ $skill->name }}</span>
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
                            <span class="tech-tag">{{ $skill->name }}</span>
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
            <div class="section-tag">// career</div>
            <h2 class="section-title">Work <span>Experience</span></h2>
            <div class="section-line"></div>
        </div>
        <div class="timeline">
            @foreach($experiences as $exp)
            <div class="timeline-item reveal">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-header">
                        <div>
                            <div class="timeline-title">{{ $exp->title }}</div>
                            <div class="timeline-company">{{ $exp->company }}</div>
                        </div>
                        <span class="timeline-date">{{ $exp->date_range }}</span>
                    </div>
                    <p class="timeline-desc">{{ $exp->description }}</p>
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
            <div class="section-tag">// portfolio</div>
            <h2 class="section-title">Featured <span>Projects</span></h2>
            <div class="section-line"></div>
        </div>
        <div class="projects-grid">
            @foreach($projects as $project)
            <a href="{{ route('project.show', $project->id) }}" class="project-card-link reveal">
            <div class="project-card">
                <div class="project-header">
                    <div class="project-icon">{{ $project->icon }}</div>
                    <div class="project-links">
                        @if($project->github_url)
                            <span onclick="event.preventDefault();window.open('{{ $project->github_url }}','_blank')" class="project-link" title="GitHub"><i class="fab fa-github"></i></span>
                        @else
                            <span class="project-link" style="opacity:0.3"><i class="fab fa-github"></i></span>
                        @endif
                        @if($project->live_url)
                            <span onclick="event.preventDefault();window.open('{{ $project->live_url }}','_blank')" class="project-link" title="Live"><i class="fas fa-arrow-up-right-from-square"></i></span>
                        @else
                            <span class="project-link" style="opacity:0.3"><i class="fas fa-arrow-up-right-from-square"></i></span>
                        @endif
                    </div>
                </div>
                <div class="project-body">
                    @if($project->featured)
                        <div class="featured-badge"><i class="fas fa-star"></i> {{ __('Featured') }}</div>
                    @endif
                    <div class="project-title">{{ $project->title }}</div>
                    <p class="project-desc">{{ $project->description }}</p>
                    @if(!empty($project->stack))
                    <div class="project-stack">
                        @foreach($project->stack as $tech)
                            <span class="stack-tag">{{ $tech }}</span>
                        @endforeach
                    </div>
                    @endif
                    <div class="project-view-more">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}"></i>
                    </div>
                </div>
            </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- CONTACT -->
<section id="contact">
    <div class="container">
        <div class="contact-wrapper">
            <div class="section-header reveal">
                <div class="section-tag">// let's connect</div>
                <h2 class="section-title">Get In <span>Touch</span></h2>
                <div class="section-line"></div>
                <p style="color:var(--text-secondary);margin-top:1rem;font-size:0.95rem">
                    I'm currently open to new opportunities. Whether you have a project in mind, want to collaborate, or just say hi — my inbox is always open.
                </p>
            </div>
            <div class="contact-cards reveal">
                <a href="mailto:{{ $settings['email'] ?? '#' }}" class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <span class="contact-card-label">Email</span>
                    <span class="contact-card-value">{{ $settings['email'] ?? 'Email me' }}</span>
                </a>
                <a href="{{ $settings['linkedin_url'] ?? '#' }}" target="_blank" class="contact-card">
                    <i class="fab fa-linkedin"></i>
                    <span class="contact-card-label">LinkedIn</span>
                    <span class="contact-card-value">Connect with me</span>
                </a>
                <a href="{{ $settings['github_url'] ?? '#' }}" target="_blank" class="contact-card">
                    <i class="fab fa-github"></i>
                    <span class="contact-card-label">GitHub</span>
                    <span class="contact-card-value">View my code</span>
                </a>
            </div>
            <div class="contact-form reveal">
                <form id="contactForm" onsubmit="handleSubmit(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-input" placeholder="Your name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" placeholder="your@email.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-input" placeholder="What's this about?" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea class="form-textarea" placeholder="Tell me about your project..." required></textarea>
                    </div>
                    <button type="submit" class="form-submit"><i class="fas fa-paper-plane"></i>&nbsp; Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<footer>
    <p>{{ $settings['footer_text'] ?? 'Portfolio' }}</p>
</footer>

<a href="/admin" class="admin-link"><i class="fas fa-lock"></i> CMS</a>

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
hamburger.addEventListener('click', () => navLinks.classList.toggle('open'));
navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => navLinks.classList.remove('open')));

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
    '{{ $settings["hero_tagline"] ?? "Backend Engineer" }}',
    'Laravel Expert',
    'API Architect',
    'PHP Developer'
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
    btn.innerHTML = '<i class="fas fa-check"></i>&nbsp; Message Sent!';
    btn.style.background = 'linear-gradient(135deg,#28ca41,#00a832)';
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-paper-plane"></i>&nbsp; Send Message';
        btn.style.background = '';
        e.target.reset();
    }, 3000);
}
</script>
</body>
</html>
