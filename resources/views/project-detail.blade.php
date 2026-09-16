<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $project->title }} — {{ $settings['hero_name'] ?? 'Portfolio' }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($project->overview ?? $project->description), 160) }}">
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

        /* ─── CURSOR ─── */
        #cursor-dot { position:fixed; width:8px; height:8px; background:var(--cyan); border-radius:50%; pointer-events:none; z-index:99999; transform:translate(-50%,-50%); transition:width .2s,height .2s; box-shadow:0 0 10px var(--cyan),0 0 20px rgba(0,212,212,0.4); }
        #cursor-ring { position:fixed; width:36px; height:36px; border:1.5px solid rgba(0,212,212,0.6); border-radius:50%; pointer-events:none; z-index:99998; transform:translate(-50%,-50%); transition:width .3s,height .3s,border-color .3s; }
        #cursor-trail-container { position:fixed; top:0; left:0; pointer-events:none; z-index:99997; }
        .cursor-trail { position:fixed; width:4px; height:4px; background:var(--cyan); border-radius:50%; pointer-events:none; transform:translate(-50%,-50%); }
        body.cursor-hover #cursor-dot { width:12px; height:12px; }
        body.cursor-hover #cursor-ring { width:50px; height:50px; border-color:rgba(0,212,212,0.9); }

        /* ─── NAV ─── */
        nav { position:fixed; top:0; left:0; right:0; z-index:1000; padding:0 2rem; background:rgba(10,14,23,0.9); backdrop-filter:blur(20px); border-bottom:1px solid var(--border); }
        .nav-inner { max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; height:70px; gap:1rem; }
        .nav-logo { font-size:1.4rem; font-weight:800; color:var(--cyan); text-decoration:none; }
        .nav-logo span { color:var(--text-primary); }
        .nav-back { display:inline-flex; align-items:center; gap:0.5rem; color:var(--text-secondary); text-decoration:none; font-size:0.875rem; font-weight:500; transition:color .2s; }
        .nav-back:hover { color:var(--cyan); }
        .nav-right { display:flex; align-items:center; gap:1rem; }
        .lang-switch { display:flex; align-items:center; gap:0.25rem; background:rgba(0,212,212,0.06); border:1px solid rgba(0,212,212,0.2); border-radius:50px; padding:0.25rem; }
        .lang-btn { padding:0.3rem 0.7rem; border-radius:50px; font-size:0.75rem; font-weight:600; text-decoration:none; color:var(--text-secondary); transition:all .2s; font-family:'JetBrains Mono',monospace; cursor:none; }
        .lang-btn.active { background:var(--gradient); color:#0a0e17; }
        .lang-btn:not(.active):hover { color:var(--cyan); }

        /* ─── HERO ─── */
        .detail-hero { padding:120px 2rem 60px; background:var(--bg-secondary); border-bottom:1px solid var(--border); position:relative; overflow:hidden; }
        .detail-hero::before { content:''; position:absolute; inset:0; background:radial-gradient(ellipse 80% 60% at 50% 0%, rgba(0,212,212,0.05) 0%, transparent 60%); }
        .detail-hero-inner { max-width:1200px; margin:0 auto; position:relative; z-index:1; }
        .breadcrumb { display:flex; align-items:center; gap:0.5rem; margin-bottom:1.5rem; font-size:0.8rem; color:var(--text-muted); font-family:'JetBrains Mono',monospace; }
        .breadcrumb a { color:var(--cyan); text-decoration:none; }
        .breadcrumb a:hover { text-decoration:underline; }
        .project-hero-title { display:flex; align-items:center; gap:1rem; margin-bottom:1rem; }
        .project-hero-icon { font-size:3rem; line-height:1; }
        .project-hero-title h1 { font-size:clamp(2rem,5vw,3.5rem); font-weight:900; line-height:1.1; }
        .featured-badge { display:inline-flex; align-items:center; gap:0.4rem; padding:0.3rem 0.8rem; background:rgba(0,212,212,0.1); border:1px solid rgba(0,212,212,0.3); border-radius:50px; font-size:0.78rem; font-weight:600; color:var(--cyan); margin-bottom:1rem; }
        .project-hero-desc { font-size:1.1rem; color:var(--text-secondary); max-width:700px; line-height:1.75; margin-bottom:2rem; }
        .project-hero-image { max-width:900px; width:100%; height:260px; object-fit:cover; border:1px solid var(--border); border-radius:20px; margin:1.5rem 0 2rem; box-shadow:0 25px 60px rgba(0,0,0,.28); }
        .hero-actions { display:flex; gap:0.75rem; flex-wrap:wrap; }
        .btn-primary { display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 1.75rem; background:var(--gradient); color:#0a0e17; border-radius:8px; font-weight:700; font-size:0.875rem; text-decoration:none; transition:all .3s; border:none; cursor:none; }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 0 30px rgba(0,212,212,0.4); }
        .btn-outline { display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 1.75rem; background:transparent; color:var(--text-primary); border:1px solid var(--border); border-radius:8px; font-weight:600; font-size:0.875rem; text-decoration:none; transition:all .3s; cursor:none; }
        .btn-outline:hover { border-color:var(--cyan); color:var(--cyan); transform:translateY(-2px); }

        /* ─── META BAR ─── */
        .meta-bar { background:var(--bg-card); border-bottom:1px solid var(--border); padding:0; }
        .meta-bar-inner { max-width:1200px; margin:0 auto; display:grid; grid-template-columns:repeat(3,1fr); }
        .meta-item { padding:1.5rem 2rem; border-{{ $locale === 'ar' ? 'left' : 'right' }}:1px solid var(--border); }
        .meta-item:last-child { border:none; }
        .meta-label { font-size:0.7rem; text-transform:uppercase; letter-spacing:2px; color:var(--text-muted); font-family:'JetBrains Mono',monospace; margin-bottom:0.4rem; }
        .meta-value { font-size:1rem; font-weight:600; color:var(--text-primary); }
        .meta-value span { color:var(--cyan); }

        /* ─── CONTENT ─── */
        .detail-content { max-width:1200px; margin:0 auto; padding:4rem 2rem; display:grid; grid-template-columns:1fr 360px; gap:3rem; align-items:start; }
        @media (max-width:900px) { .detail-content { grid-template-columns:1fr; } }

        /* ─── SECTIONS ─── */
        .section-block { margin-bottom:3rem; }
        .section-label { font-size:0.7rem; text-transform:uppercase; letter-spacing:2px; color:var(--cyan); font-family:'JetBrains Mono',monospace; margin-bottom:1rem; }
        .section-title { font-size:1.5rem; font-weight:800; margin-bottom:1.25rem; }
        .overview-text { color:var(--text-secondary); line-height:1.85; font-size:1rem; white-space:pre-wrap; }

        /* ─── WORK STAGES ─── */
        .stages-list { display:flex; flex-direction:column; gap:0; }
        .stage-item { display:flex; gap:1.25rem; position:relative; }
        .stage-item:not(:last-child)::before { content:''; position:absolute; {{ $locale === 'ar' ? 'right' : 'left' }}:19px; top:44px; bottom:0; width:2px; background:linear-gradient(to bottom,rgba(0,212,212,0.3),transparent); }
        .stage-num { flex-shrink:0; width:40px; height:40px; border-radius:50%; background:rgba(0,212,212,0.1); border:2px solid rgba(0,212,212,0.3); display:flex; align-items:center; justify-content:center; font-family:'JetBrains Mono',monospace; font-size:0.8rem; font-weight:700; color:var(--cyan); margin-top:0.15rem; position:relative; z-index:1; }
        .stage-body { padding-bottom:2rem; }
        .stage-text { font-size:0.95rem; color:var(--text-secondary); line-height:1.6; }

        /* ─── SIDEBAR ─── */
        .sidebar { display:flex; flex-direction:column; gap:1.5rem; }
        .sidebar-card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:1.25rem; }
        .sidebar-card-title { font-size:0.7rem; text-transform:uppercase; letter-spacing:2px; color:var(--cyan); font-family:'JetBrains Mono',monospace; margin-bottom:1rem; padding-bottom:0.75rem; border-bottom:1px solid var(--border); }
        .stack-tags { display:flex; flex-wrap:wrap; gap:0.5rem; }
        .stack-tag { padding:0.35rem 0.75rem; background:rgba(0,212,212,0.08); border:1px solid rgba(0,212,212,0.2); border-radius:6px; font-size:0.78rem; color:var(--cyan); font-family:'JetBrains Mono',monospace; }

        /* ─── RELATED ─── */
        .related-section { background:var(--bg-secondary); border-top:1px solid var(--border); padding:4rem 2rem; }
        .related-inner { max-width:1200px; margin:0 auto; }
        .related-header { margin-bottom:2.5rem; }
        .related-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1.5rem; }
        .related-card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:1.5rem; text-decoration:none; color:inherit; display:block; transition:all .3s; cursor:none; }
        .related-card:hover { border-color:rgba(0,212,212,0.4); transform:translateY(-4px); box-shadow:0 20px 40px rgba(0,0,0,0.3); }
        .related-card-icon { font-size:1.75rem; margin-bottom:0.75rem; }
        .related-card-title { font-size:1rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-primary); }
        .related-card-desc { font-size:0.85rem; color:var(--text-secondary); line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .related-card-stack { display:flex; flex-wrap:wrap; gap:0.35rem; margin-top:0.75rem; }
        .related-tag { padding:0.2rem 0.5rem; background:rgba(0,212,212,0.06); border:1px solid rgba(0,212,212,0.15); border-radius:4px; font-size:0.7rem; color:var(--cyan); font-family:'JetBrains Mono',monospace; }
        .view-details-link { display:inline-flex; align-items:center; gap:0.35rem; margin-top:1rem; font-size:0.8rem; color:var(--cyan); font-weight:600; }

        /* ─── FOOTER ─── */
        footer { text-align:center; padding:2rem; color:var(--text-muted); font-size:0.85rem; border-top:1px solid var(--border); }

        /* ─── RTL TWEAKS ─── */
        @if($locale === 'ar')
        .breadcrumb { direction:rtl; }
        .stage-item:not(:last-child)::before { right:19px; left:auto; }
        .meta-item { border-right:none; border-left:1px solid var(--border); }
        .meta-item:last-child { border:none; }
        @endif

        /* ─── RESPONSIVE ─── */
        @media (max-width:768px) {
            .meta-bar-inner { grid-template-columns:1fr; }
            .meta-item { border:none; border-bottom:1px solid var(--border); }
            .meta-item:last-child { border:none; }
            .detail-hero { padding:100px 1.5rem 40px; }
            body{cursor:auto} #cursor-dot,#cursor-ring,#cursor-trail-container{display:none}
        }
        @media (max-width:600px) {
            .project-hero-title { flex-direction:column; align-items:flex-start; gap:0.5rem; }
        }
    </style>
</head>
<body>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>
<div id="cursor-trail-container"></div>

<!-- NAV -->
<nav>
    <div class="nav-inner">
        <a class="nav-logo" href="/">B<span>.</span></a>
        <a class="nav-back" href="/#projects">
            @if($locale === 'ar')
                {{ __('Back to Portfolio') }} <i class="fas fa-arrow-right"></i>
            @else
                <i class="fas fa-arrow-left"></i> {{ __('Back to Portfolio') }}
            @endif
        </a>
        <div class="nav-right">
            <div class="lang-switch">
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn {{ $locale === 'ar' ? 'active' : '' }}">عربي</a>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<div class="detail-hero">
    <div class="detail-hero-inner">
        <div class="breadcrumb">
            <a href="/">{{ $settings['hero_name'] ?? 'Portfolio' }}</a>
            <i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}" style="font-size:0.65rem"></i>
            <a href="/#projects">{{ __('Projects') }}</a>
            <i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}" style="font-size:0.65rem"></i>
            <span>{{ $project->title }}</span>
        </div>

        @if($project->featured)
            <div class="featured-badge"><i class="fas fa-star"></i> {{ __('Featured') }}</div>
        @endif

        <div class="project-hero-title">
            <div class="project-hero-icon">{{ $project->icon ?? '🚀' }}</div>
            <h1>{{ $project->title }}</h1>
        </div>

        <p class="project-hero-desc">{{ $project->description }}</p>
        @if($project->image)
            @php
                $projectImageSrc = preg_match('/^https?:\/\//i', $project->image)
                    ? $project->image
                    : asset(ltrim($project->image, '/'));
            @endphp
            <img src="{{ $projectImageSrc }}" alt="{{ $project->title }}" class="project-hero-image">
        @endif

        <div class="hero-actions">
            @if($project->live_url)
                <a href="{{ $project->live_url }}" target="_blank" class="btn-primary">
                    <i class="fas fa-arrow-up-right-from-square"></i> {{ __('Live Demo') }}
                </a>
            @endif
            @if($project->github_url)
                <a href="{{ $project->github_url }}" target="_blank" class="btn-outline">
                    <i class="fab fa-github"></i> {{ __('Source Code') }}
                </a>
            @endif
        </div>
    </div>
</div>

<!-- META BAR -->
<div class="meta-bar">
    <div class="meta-bar-inner">
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-user-tie" style="margin-{{ $locale === 'ar' ? 'left' : 'right' }}:0.35rem"></i>{{ __('Client') }}</div>
            <div class="meta-value"><span>{{ $project->client ?: __('Not specified') }}</span></div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-clock" style="margin-{{ $locale === 'ar' ? 'left' : 'right' }}:0.35rem"></i>{{ __('Duration') }}</div>
            <div class="meta-value"><span>{{ $project->duration ?: __('Not specified') }}</span></div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-tag" style="margin-{{ $locale === 'ar' ? 'left' : 'right' }}:0.35rem"></i>{{ __('Category') }}</div>
            <div class="meta-value"><span>{{ $project->category ?: __('Not specified') }}</span></div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="detail-content">
    <!-- Left: Overview + Work Stages -->
    <div>
        @if($project->overview)
        <div class="section-block reveal">
            <div class="section-label">// {{ strtolower(__('Overview')) }}</div>
            <div class="section-title">{{ __('Overview') }}</div>
            <div class="overview-text">{{ $project->overview }}</div>
        </div>
        @endif

        @if(!empty($project->work_stages))
        <div class="section-block reveal">
            <div class="section-label">// {{ strtolower(__('Work Stages')) }}</div>
            <div class="section-title">{{ __('Work Stages') }}</div>
            <div class="stages-list">
                @foreach($project->work_stages as $i => $stage)
                <div class="stage-item">
                    <div class="stage-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="stage-body">
                        <div class="stage-text">{{ $stage }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="section-block">
            <div class="section-label">// {{ strtolower(__('Work Stages')) }}</div>
            <div class="section-title">{{ __('Work Stages') }}</div>
            <p style="color:var(--text-muted);font-size:0.9rem">{{ __('No stages added yet.') }}</p>
        </div>
        @endif
    </div>

    <!-- Right: Sidebar -->
    <div class="sidebar">
        @if(!empty($project->stack))
        <div class="sidebar-card">
            <div class="sidebar-card-title"><i class="fas fa-layer-group" style="margin-{{ $locale === 'ar' ? 'left' : 'right' }}:0.4rem"></i>{{ __('Tech Stack') }}</div>
            <div class="stack-tags">
                @foreach($project->stack as $tech)
                    <span class="stack-tag">{{ $tech }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="sidebar-card">
            <div class="sidebar-card-title"><i class="fas fa-info-circle" style="margin-{{ $locale === 'ar' ? 'left' : 'right' }}:0.4rem"></i>{{ __('Project Details') }}</div>
            <div style="display:flex;flex-direction:column;gap:0.85rem">
                @if($project->client)
                <div>
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:0.2rem">{{ __('Client') }}</div>
                    <div style="font-size:0.9rem;font-weight:600">{{ $project->client }}</div>
                </div>
                @endif
                @if($project->duration)
                <div>
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:0.2rem">{{ __('Duration') }}</div>
                    <div style="font-size:0.9rem;font-weight:600">{{ $project->duration }}</div>
                </div>
                @endif
                @if($project->category)
                <div>
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:0.2rem">{{ __('Category') }}</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--cyan)">{{ $project->category }}</div>
                </div>
                @endif
            </div>
        </div>

        @if($project->github_url || $project->live_url)
        <div class="sidebar-card">
            <div class="sidebar-card-title"><i class="fas fa-link" style="margin-{{ $locale === 'ar' ? 'left' : 'right' }}:0.4rem"></i>Links</div>
            <div style="display:flex;flex-direction:column;gap:0.6rem">
                @if($project->live_url)
                <a href="{{ $project->live_url }}" target="_blank" class="btn-primary" style="justify-content:center;font-size:0.82rem;padding:0.6rem 1rem">
                    <i class="fas fa-arrow-up-right-from-square"></i> {{ __('Live Demo') }}
                </a>
                @endif
                @if($project->github_url)
                <a href="{{ $project->github_url }}" target="_blank" class="btn-outline" style="justify-content:center;font-size:0.82rem;padding:0.6rem 1rem">
                    <i class="fab fa-github"></i> {{ __('Source Code') }}
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- RELATED PROJECTS -->
@if(!empty($related))
<div class="related-section">
    <div class="related-inner">
        <div class="related-header">
            <div style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;color:var(--cyan);letter-spacing:2px;margin-bottom:0.5rem">// {{ strtolower(__('Related Projects')) }}</div>
            <h2 style="font-size:1.75rem;font-weight:800">{{ __('Related Projects') }}</h2>
        </div>
        <div class="related-grid">
            @foreach($related as $rel)
            <a href="{{ route('project.show', $rel->id) }}" class="related-card">
                <div class="related-card-icon">{{ $rel->icon ?? '🚀' }}</div>
                <div class="related-card-title">{{ $rel->title }}</div>
                <div class="related-card-desc">{{ $rel->description }}</div>
                @if(!empty($rel->stack))
                <div class="related-card-stack">
                    @foreach(array_slice((array)$rel->stack, 0, 3) as $tech)
                        <span class="related-tag">{{ $tech }}</span>
                    @endforeach
                </div>
                @endif
                <div class="view-details-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<footer>
    <p>{{ $settings['footer_text'] ?? 'Portfolio' }}</p>
</footer>

<a href="/admin" style="position:fixed;bottom:1.5rem;{{ $locale === 'ar' ? 'left' : 'right' }}:1.5rem;background:rgba(0,212,212,0.1);border:1px solid rgba(0,212,212,0.2);color:var(--cyan);padding:0.5rem 1rem;border-radius:8px;font-size:0.75rem;text-decoration:none;font-weight:600;z-index:100;cursor:none"><i class="fas fa-lock"></i> CMS</a>

<script>
const dot   = document.getElementById('cursor-dot');
const ring  = document.getElementById('cursor-ring');
const tc    = document.getElementById('cursor-trail-container');
const TRAIL = 10;
const trails = [];
for(let i=0;i<TRAIL;i++){
    const t=document.createElement('div');
    t.className='cursor-trail';
    t.style.opacity=(1-i/TRAIL)*0.4;
    t.style.width=(4-i*0.25)+'px';
    t.style.height=(4-i*0.25)+'px';
    tc.appendChild(t);
    trails.push({el:t,x:0,y:0});
}
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;dot.style.left=mx+'px';dot.style.top=my+'px';});
document.addEventListener('mousedown',()=>document.body.classList.add('cursor-hover'));
document.addEventListener('mouseup',()=>document.body.classList.remove('cursor-hover'));
document.querySelectorAll('a,button').forEach(el=>{
    el.addEventListener('mouseenter',()=>document.body.classList.add('cursor-hover'));
    el.addEventListener('mouseleave',()=>document.body.classList.remove('cursor-hover'));
});
function anim(){
    rx+=(mx-rx)*0.12; ry+=(my-ry)*0.12;
    ring.style.left=rx+'px'; ring.style.top=ry+'px';
    let px=mx,py=my;
    trails.forEach((t,i)=>{
        const d=0.08+i*0.04;
        t.x+=(px-t.x)*d; t.y+=(py-t.y)*d;
        t.el.style.left=t.x+'px'; t.el.style.top=t.y+'px';
        t.el.style.opacity=(1-i/TRAIL)*0.3;
        px=t.x; py=t.y;
    });
    requestAnimationFrame(anim);
}
anim();

// Scroll reveal
document.querySelectorAll('.reveal').forEach(el=>{
    el.style.opacity='0';
    el.style.transform='translateY(30px)';
    el.style.transition='opacity 0.6s ease, transform 0.6s ease';
});
const obs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{
        if(e.isIntersecting){
            e.target.style.opacity='1';
            e.target.style.transform='none';
        }
    });
},{threshold:0.1,rootMargin:'0px 0px -30px 0px'});
document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));
</script>
</body>
</html>
