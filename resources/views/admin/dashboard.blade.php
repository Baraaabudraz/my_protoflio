@extends('admin.layout')
@section('title', __('Dashboard'))

@section('styles')
<style>
.welcome { position:relative; overflow:hidden; display:flex; align-items:center; justify-content:space-between; gap:1.5rem; flex-wrap:wrap; padding:1.75rem 2rem; margin-bottom:1.5rem; border-radius:var(--radius); background:var(--gradient); color:var(--on-accent); box-shadow:var(--shadow); }
.welcome::after { content:''; position:absolute; width:260px; height:260px; border-radius:50%; border:40px solid rgba(255,255,255,.08); top:-120px; inset-inline-end:-60px; pointer-events:none; }
.welcome-user { display:flex; align-items:center; gap:1rem; position:relative; z-index:1; }
.welcome-user img { width:60px; height:60px; border-radius:18px; object-fit:cover; object-position:50% 22%; border:3px solid rgba(255,255,255,.35); }
.welcome h2 { font:800 1.45rem var(--font-head); line-height:1.25; }
.welcome p { opacity:.85; font-size:.92rem; margin-top:.15rem; }
.welcome-actions { display:flex; gap:.6rem; flex-wrap:wrap; position:relative; z-index:1; }
.welcome .btn { background:rgba(255,255,255,.16); color:inherit; border-color:rgba(255,255,255,.25); backdrop-filter:blur(6px); }
.welcome .btn:hover { background:rgba(255,255,255,.28); }
.welcome .btn.solid { background:#fff; color:#0f172a; border-color:#fff; }

.dash-grid { display:grid; grid-template-columns:1.35fr 1fr; gap:1.5rem; align-items:start; }
.dash-col { display:flex; flex-direction:column; gap:1.5rem; min-width:0; }

.health-score { display:flex; align-items:center; gap:1.25rem; padding:1.25rem 1.4rem; border-bottom:1px solid var(--border); }
.score-ring { --p:0; width:74px; height:74px; border-radius:50%; flex-shrink:0; display:grid; place-items:center; background:conic-gradient(var(--ring-color) calc(var(--p) * 1%), var(--bg3) 0); }
.score-ring span { width:58px; height:58px; border-radius:50%; background:var(--card); display:grid; place-items:center; font:800 1.05rem var(--font-head); }
.health-score strong { display:block; font:700 1rem var(--font-head); }
.health-score p { font-size:.86rem; color:var(--muted); margin-top:.2rem; }
.checklist { list-style:none; }
.checklist li { display:flex; align-items:center; gap:.85rem; padding:.85rem 1.4rem; border-bottom:1px solid var(--border); }
.checklist li:last-child { border-bottom:none; }
.check-icon { width:28px; height:28px; border-radius:50%; flex-shrink:0; display:grid; place-items:center; font-size:.72rem; }
.check-icon.ok { background:var(--success-dim); color:var(--success); }
.check-icon.todo { background:var(--warning-dim); color:var(--warning); }
.check-text { flex:1; min-width:0; }
.check-text strong { display:block; font-size:.9rem; font-weight:600; }
.check-text small { color:var(--muted); font-size:.8rem; }
.checklist li.done .check-text strong { color:var(--text-2); font-weight:500; }

.recent-list { list-style:none; }
.recent-list li { display:flex; align-items:center; gap:.9rem; padding:.8rem 1.4rem; border-bottom:1px solid var(--border); }
.recent-list li:last-child { border-bottom:none; }
.recent-thumb { width:52px; height:38px; border-radius:8px; flex-shrink:0; overflow:hidden; background:var(--bg3); display:grid; place-items:center; font-size:1.15rem; border:1px solid var(--border); }
.recent-thumb img { width:100%; height:100%; object-fit:cover; }
.recent-info { flex:1; min-width:0; }
.recent-info strong { display:block; font-size:.9rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.recent-info small { color:var(--muted); font-size:.78rem; }

.quick-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
.quick-action { display:flex; align-items:center; gap:.75rem; padding:.9rem 1rem; border-radius:12px; border:1px solid var(--border); background:var(--bg); text-decoration:none; font-size:.88rem; font-weight:600; transition:all .2s; }
.quick-action i { width:34px; height:34px; border-radius:10px; display:grid; place-items:center; background:var(--cyan-dim); color:var(--cyan); flex-shrink:0; }
.quick-action:hover { border-color:var(--cyan-line); transform:translateY(-1px); }

.google-preview { padding:1.25rem 1.4rem; font-family:arial, sans-serif; }
.google-preview .gp-site { display:flex; align-items:center; gap:.6rem; margin-bottom:.4rem; }
.google-preview .gp-site img { width:28px; height:28px; border-radius:50%; object-fit:cover; background:var(--bg3); border:1px solid var(--border); }
.google-preview .gp-site strong { display:block; font-size:.85rem; font-weight:400; color:var(--text); }
.google-preview .gp-site small { display:block; font-size:.76rem; color:var(--muted); direction:ltr; text-align:start; }
.google-preview .gp-title { font-size:1.15rem; color:#1a0dab; line-height:1.35; margin-bottom:.25rem; }
[data-theme="dark"] .google-preview .gp-title { color:#8ab4f8; }
.google-preview .gp-desc { font-size:.86rem; color:var(--text-2); line-height:1.55; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

@media (max-width:1100px) { .dash-grid { grid-template-columns:1fr; } }
@media (max-width:520px) { .quick-grid { grid-template-columns:1fr; } .welcome { padding:1.4rem; } }
</style>
@endsection

@section('content')
@php
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? __('Good morning') : ($hour < 18 ? __('Good afternoon') : __('Good evening'));
    $firstName = explode(' ', ts($settings, 'hero_name') ?: 'Admin')[0];
    $doneChecks = count(array_filter($healthChecks, fn ($check) => $check['ok']));
    $healthPercent = (int) round($doneChecks / max(count($healthChecks), 1) * 100);
    $ringColor = $healthPercent >= 85 ? 'var(--success)' : ($healthPercent >= 50 ? 'var(--warning)' : 'var(--danger)');
@endphp

<section class="welcome">
    <div class="welcome-user">
        <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="60" height="60">
        <div>
            <h2>{{ $greeting }}, {{ $firstName }}</h2>
            <p>{{ now()->translatedFormat('l, j F Y') }} · {{ __('Here is what is happening with your website.') }}</p>
        </div>
    </div>
    <div class="welcome-actions">
        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn"><i class="fas fa-globe"></i> {{ __('View Portfolio') }}</a>
        <a href="{{ route('admin.projects.create') }}" class="btn solid"><i class="fas fa-plus"></i> {{ __('Add Project') }}</a>
    </div>
</section>

<div class="stats-grid">
    <a href="{{ route('admin.services') }}" class="stat-card">
        <span class="stat-card-icon"><i class="fas fa-handshake"></i></span>
        <span><span class="stat-card-number">{{ $stats['services'] }}</span><span class="stat-card-label" style="display:block">{{ __('Services') }}</span></span>
    </a>
    <a href="{{ route('admin.projects') }}" class="stat-card">
        <span class="stat-card-icon"><i class="fas fa-rocket"></i></span>
        <span><span class="stat-card-number">{{ $stats['projects'] }}</span><span class="stat-card-label" style="display:block">{{ __('Projects') }} · {{ $stats['projects_visible'] }} {{ __('visible') }}</span></span>
    </a>
    <a href="{{ route('admin.experience') }}" class="stat-card">
        <span class="stat-card-icon"><i class="fas fa-briefcase"></i></span>
        <span><span class="stat-card-number">{{ $stats['experiences'] }}</span><span class="stat-card-label" style="display:block">{{ __('Experience Entries') }}</span></span>
    </a>
    <a href="{{ route('admin.skills') }}" class="stat-card">
        <span class="stat-card-icon"><i class="fas fa-code-branch"></i></span>
        <span><span class="stat-card-number">{{ $stats['skills'] }}</span><span class="stat-card-label" style="display:block">{{ __('Skills Tracked') }}</span></span>
    </a>
</div>

<div class="dash-grid">
    <div class="dash-col">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-heart-pulse" style="color:var(--cyan)"></i> {{ __('Website health') }}</span>
                <span class="badge {{ $healthPercent === 100 ? 'badge-success' : 'badge-warning' }}">{{ $doneChecks }}/{{ count($healthChecks) }}</span>
            </div>
            <div class="health-score">
                <div class="score-ring" style="--p:{{ $healthPercent }}; --ring-color:{{ $ringColor }}" role="img" aria-label="{{ $healthPercent }}%"><span>{{ $healthPercent }}%</span></div>
                <div>
                    <strong>{{ $healthPercent === 100 ? __('Everything looks great!') : __('A few things need your attention') }}</strong>
                    <p>{{ __('Complete these items to make your portfolio more trustworthy and easier to find on Google.') }}</p>
                </div>
            </div>
            <ul class="checklist">
                @foreach(collect($healthChecks)->sortBy('ok') as $check)
                <li class="{{ $check['ok'] ? 'done' : '' }}">
                    <span class="check-icon {{ $check['ok'] ? 'ok' : 'todo' }}"><i class="fas {{ $check['ok'] ? 'fa-check' : 'fa-exclamation' }}"></i></span>
                    <span class="check-text">
                        <strong>{{ $check['label'] }}</strong>
                        @unless($check['ok'])<small>{{ $check['hint'] }}</small>@endunless
                    </span>
                    @if(!$check['ok'] && $check['url'])
                        <a href="{{ $check['url'] }}" class="btn btn-secondary btn-sm">{{ __('Fix') }}</a>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-clock-rotate-left" style="color:var(--cyan)"></i> {{ __('Recently updated projects') }}</span>
                <a href="{{ route('admin.projects') }}" class="btn btn-secondary btn-sm">{{ __('View all') }}</a>
            </div>
            @if(empty($recentProjects))
                <div class="empty-state"><i class="fas fa-rocket"></i>{{ __('No projects yet.') }}</div>
            @else
            <ul class="recent-list">
                @foreach($recentProjects as $recent)
                <li>
                    <span class="recent-thumb">
                        @if($recent->image)
                            <img src="{{ project_image_url($recent->image) }}" alt="" loading="lazy" onerror="this.remove()">
                        @else
                            {{ $recent->icon }}
                        @endif
                    </span>
                    <span class="recent-info">
                        <strong>{{ t($recent, 'title') }}</strong>
                        <small>{{ $recent->updated_at ? \Illuminate\Support\Carbon::parse($recent->updated_at)->diffForHumans() : '' }}</small>
                    </span>
                    @if(!$recent->visible)<span class="badge badge-muted">{{ __('Hidden') }}</span>@endif
                    @if($recent->featured)<span class="badge badge-cyan"><i class="fas fa-star"></i></span>@endif
                    <a href="{{ route('admin.projects.edit', $recent->id) }}" class="btn btn-secondary btn-sm" aria-label="{{ __('Edit') }}"><i class="fas fa-pen"></i></a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>

    <div class="dash-col">
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-bolt" style="color:var(--cyan)"></i> {{ __('Quick Actions') }}</span></div>
            <div class="card-body">
                <div class="quick-grid">
                    <a href="{{ route('admin.projects.create') }}" class="quick-action"><i class="fas fa-plus"></i>{{ __('Add Project') }}</a>
                    <a href="{{ route('admin.services.create') }}" class="quick-action"><i class="fas fa-handshake"></i>{{ __('Add Service') }}</a>
                    <a href="{{ route('admin.experience.create') }}" class="quick-action"><i class="fas fa-briefcase"></i>{{ __('Add Experience') }}</a>
                    <a href="{{ route('admin.skills') }}" class="quick-action"><i class="fas fa-code-branch"></i>{{ __('Manage Skills') }}</a>
                    <a href="{{ route('admin.settings') }}" class="quick-action"><i class="fas fa-user-pen"></i>{{ __('Edit Profile & Settings') }}</a>
                    <a href="{{ route('sitemap') }}" target="_blank" rel="noopener" class="quick-action"><i class="fas fa-sitemap"></i>{{ __('View Sitemap') }}</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fab fa-google" style="color:var(--cyan)"></i> {{ __('Google preview') }}</span>
                <a href="{{ route('admin.settings') }}" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i> <span class="btn-label">{{ __('Edit') }}</span></a>
            </div>
            <div class="google-preview" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
                <div class="gp-site">
                    <img src="{{ asset('favicon-32x32.png') }}" alt="">
                    <div><strong>{{ ts($settings, 'hero_name') }}</strong><small>{{ $googlePreview['url'] }}</small></div>
                </div>
                <div class="gp-title">{{ \Illuminate\Support\Str::limit($googlePreview['title'], 62) }}</div>
                <div class="gp-desc">{{ \Illuminate\Support\Str::limit($googlePreview['description'], 160) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
