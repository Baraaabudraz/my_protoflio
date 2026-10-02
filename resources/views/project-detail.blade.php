@extends('layouts.site')

@php
    $heroName = ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? 'Portfolio');
    $firstName = explode(' ', $heroName)[0];
    $projectTitle = t($project, 'title');
    $projectImage = $project->image ? project_image_url($project->image) : null;
    $client = t($project, 'client');
    $duration = t($project, 'duration');
    $category = t($project, 'category');
    $overview = t($project, 'overview');
    $result = t($project, 'result');
    $stack = (array) ($project->stack ?? []);
    $stages = (array) ($project->work_stages ?? []);
    $homeUrl = \App\Http\Middleware\SetLocale::localizedUrl(url('/'), $locale);
    $pageUrl = \App\Http\Middleware\SetLocale::localizedUrl(url()->current(), $locale);
    $projectUrl = fn ($id) => \App\Http\Middleware\SetLocale::localizedUrl(route('project.show', $id), $locale);
    $arrow = $isRtl ? '←' : '→';
    $back = $isRtl ? '→' : '←';
    $whatsappNumber = preg_replace('/\D+/', '', $settings['whatsapp_number'] ?? '');
    $similarMessage = __('Hello :name, I saw your project ":project" and I would like something similar.', ['name' => $firstName, 'project' => $projectTitle]);
    $similarUrl = $whatsappNumber ? 'https://wa.me/'.$whatsappNumber.'?text='.rawurlencode($similarMessage) : $homeUrl.'#contact';
@endphp

@section('content')

{{-- ═════════ HEADER ═════════ --}}
<section class="p-hero">
    <div class="aurora is-soft" aria-hidden="true"><span></span><span></span></div>
    <div class="grid-bg" aria-hidden="true"></div>
    <div class="wrap">
        <nav class="crumbs" aria-label="{{ __('Breadcrumb') }}">
            <a href="{{ $homeUrl }}">{{ __('Home') }}</a><span aria-hidden="true">/</span>
            <a href="{{ $homeUrl }}#projects">{{ __('Case studies') }}</a><span aria-hidden="true">/</span>
            <span aria-current="page">{{ $projectTitle }}</span>
        </nav>

        <div class="p-hero-grid">
            <div class="reveal">
                <div class="case-meta">
                    @if($category)<span class="tag">{{ $category }}</span>@endif
                    @if($project->featured)<span class="tag tag-accent"><i class="fas fa-star" aria-hidden="true"></i> {{ __('Featured') }}</span>@endif
                </div>
                <h1 class="p-title">{{ $projectTitle }}</h1>
                <p class="lead">{{ t($project, 'description') }}</p>
                <div class="hero-actions">
                    <a href="{{ $similarUrl }}" @if($whatsappNumber) target="_blank" rel="noopener" @endif class="btn btn-primary btn-lg">{{ __('I want something similar') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
                    @if($project->live_url)<a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn btn-ghost btn-lg">{{ __('Live Demo') }} <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>@endif
                </div>
            </div>

            <aside class="glass-card p-summary reveal" style="--d:.08s" aria-label="{{ __('Project summary') }}">
                @if($result)
                    <div class="result-box">
                        <span class="result-icon" aria-hidden="true"><i class="fas fa-arrow-trend-up"></i></span>
                        <div><small>{{ __('Result') }}</small><strong>{{ $result }}</strong></div>
                    </div>
                @endif
                <dl class="facts">
                    <div><dt><i class="fas fa-user-tie" aria-hidden="true"></i> {{ __('Client') }}</dt><dd>{{ $client ?: __('Not specified') }}</dd></div>
                    <div><dt><i class="fas fa-calendar" aria-hidden="true"></i> {{ __('Duration') }}</dt><dd dir="auto">{{ $duration ?: __('Not specified') }}</dd></div>
                    <div><dt><i class="fas fa-folder" aria-hidden="true"></i> {{ __('Category') }}</dt><dd>{{ $category ?: __('Not specified') }}</dd></div>
                    <div><dt><i class="fas fa-layer-group" aria-hidden="true"></i> {{ __('Technologies') }}</dt><dd>{{ count($stack) }} {{ __('tools') }}</dd></div>
                </dl>
            </aside>
        </div>
    </div>
</section>

{{-- ═════════ COVER ═════════ --}}
<div class="wrap">
    <div class="p-cover reveal" style="view-transition-name: project-{{ $project->id }}">
        <div class="console-bar" aria-hidden="true">
            <span class="dots"><i></i><i></i><i></i></span>
            <span class="mono url-bar">{{ $project->live_url ? preg_replace('#^https?://#', '', rtrim($project->live_url, '/')) : \Illuminate\Support\Str::slug($project->title).'.app' }}</span>
            <span></span>
        </div>
        @if($projectImage)
            <img src="{{ $projectImage }}" alt="{{ $projectTitle }}" fetchpriority="high">
        @else
            <div class="placeholder" aria-hidden="true">
                <span class="emoji">{{ $project->icon ?: '🚀' }}</span>
                @if(!empty($stack))<ul class="stack">@foreach(array_slice($stack, 0, 6) as $tech)<li>{{ $tech }}</li>@endforeach</ul>@endif
            </div>
        @endif
    </div>
</div>

{{-- ═════════ BODY ═════════ --}}
<div class="wrap p-layout">
    <div class="p-main">
        @if($overview)
        <section class="p-section reveal" aria-labelledby="storyTitle">
            <h2 id="storyTitle"><span class="p-num" aria-hidden="true"><i class="fas fa-lightbulb"></i></span>{{ __('The challenge & the solution') }}</h2>
            <p class="p-story">{{ $overview }}</p>
        </section>
        @endif

        <section class="p-section reveal" aria-labelledby="stagesTitle">
            <h2 id="stagesTitle"><span class="p-num" aria-hidden="true"><i class="fas fa-list-check"></i></span>{{ __('How I built it') }}</h2>
            @if(!empty($stages))
                <ol class="p-steps pipeline-mini">
                    @foreach($stages as $stage)
                        <li><span class="n" aria-hidden="true">{{ $loop->iteration }}</span><span>{{ $stage }}</span></li>
                    @endforeach
                </ol>
            @else
                <p class="muted">{{ __('No stages added yet.') }}</p>
            @endif
        </section>

        @if(!empty($gallery))
        <section class="p-section reveal" aria-labelledby="galleryTitle" id="gallery">
            <h2 id="galleryTitle"><span class="p-num" aria-hidden="true"><i class="fas fa-images"></i></span>{{ __('Screenshots') }} <span class="muted">({{ count($gallery) }})</span></h2>
            <ul class="gallery">
                @foreach($gallery as $image)
                    @php
                        $caption = t($image, 'caption');
                    @endphp
                    <li>
                        <a href="{{ asset('uploads/'.$image->path) }}" class="gallery-item" data-full="{{ asset('uploads/'.$image->path) }}" data-caption="{{ $caption }}"
                           aria-label="{{ __('Open image :number of :total', ['number' => $loop->iteration, 'total' => count($gallery)]) }}{{ $caption ? ' — '.$caption : '' }}">
                            <img src="{{ asset('uploads/'.$image->thumb_path) }}"
                                 alt="{{ $caption ?: $projectTitle.' — '.__('screenshot :number', ['number' => $loop->iteration]) }}"
                                 loading="lazy" decoding="async" @if($image->width && $image->height) width="{{ $image->width }}" height="{{ $image->height }}" @endif>
                            <span class="zoom" aria-hidden="true"><i class="fas fa-magnifying-glass-plus"></i></span>
                        </a>
                        @if($caption)<p class="cap">{{ $caption }}</p>@endif
                    </li>
                @endforeach
            </ul>
        </section>
        @endif

        @foreach($testimonials as $testimonial)
            <figure class="glass-card quote-card is-wide reveal">
                <i class="fas fa-quote-left quote-mark" aria-hidden="true"></i>
                <blockquote>{{ t($testimonial, 'quote') }}</blockquote>
                <figcaption>
                    <span class="avatar" aria-hidden="true">{{ mb_substr($testimonial->name, 0, 1) }}</span>
                    <span><strong>{{ $testimonial->name }}</strong>@if(t($testimonial, 'role'))<small>{{ t($testimonial, 'role') }}</small>@endif</span>
                </figcaption>
            </figure>
        @endforeach
    </div>

    <aside class="p-side">
        <div class="glass-card side-card reveal">
            <h2 class="side-title">{{ __('Tech Stack') }}</h2>
            <ul class="chips">@foreach($stack as $tech)<li>{{ $tech }}</li>@endforeach</ul>
            @if($project->live_url || $project->github_url)
                <div class="side-links">
                    @if($project->live_url)<a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">{{ __('Live Demo') }} <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>@endif
                    @if($project->github_url)<a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="btn btn-ghost btn-sm"><i class="fab fa-github" aria-hidden="true"></i> {{ __('Source Code') }}</a>@endif
                </div>
            @endif
        </div>
        <div class="glass-card side-card side-cta reveal">
            <h2 class="side-title">{{ __('Need something similar?') }}</h2>
            <p>{{ __('Tell me about your project and get a clear plan with scope, timeline and price — free.') }}</p>
            @if($whatsappNumber)<a href="{{ $similarUrl }}" target="_blank" rel="noopener" class="btn btn-wa"><i class="fab fa-whatsapp" aria-hidden="true"></i> {{ __('Chat on WhatsApp') }}</a>@endif
            <a href="{{ $homeUrl }}#contact" class="btn btn-primary">{{ __('Get a free consultation') }}</a>
        </div>
        <div class="glass-card side-card reveal">
            <h2 class="side-title">{{ __('Share this project') }}</h2>
            <div class="share">
                <button type="button" class="icon-btn" id="copyLink" data-url="{{ $pageUrl }}" aria-label="{{ __('Copy link') }}"><i class="fas fa-link"></i></button>
                <a class="icon-btn" href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode($pageUrl) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on LinkedIn') }}"><i class="fab fa-linkedin-in"></i></a>
                <a class="icon-btn" href="https://wa.me/?text={{ rawurlencode($projectTitle.' — '.$pageUrl) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on WhatsApp') }}"><i class="fab fa-whatsapp"></i></a>
                <a class="icon-btn" href="https://x.com/intent/post?url={{ rawurlencode($pageUrl) }}&text={{ rawurlencode($projectTitle) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on X') }}"><i class="fab fa-x-twitter"></i></a>
                <span class="copy-feedback" id="copyFeedback" role="status"></span>
            </div>
        </div>
    </aside>
</div>

{{-- ═════════ MORE CASE STUDIES ═════════ --}}
@if(!empty($related))
<section class="section section-alt" aria-labelledby="relatedTitle">
    <div class="wrap">
        <header class="section-head">
            <p class="eyebrow">{{ __('Case studies') }}</p>
            <h2 id="relatedTitle">{{ __('More projects') }}</h2>
        </header>
        <div class="case-grid">
            @foreach($related as $item)
                @php
                    $itemImage = $item->image ? project_image_url($item->image) : null;
                    $itemResult = t($item, 'result');
                @endphp
                <a href="{{ $projectUrl($item->id) }}" class="glass-card case-card reveal" data-spotlight style="--d: {{ $loop->index * .08 }}s">
                    <div class="case-media" style="view-transition-name: project-{{ $item->id }}">
                        @if($itemImage)<img src="{{ $itemImage }}" alt="" loading="lazy">@else<span class="case-emoji" aria-hidden="true">{{ $item->icon ?: '🚀' }}</span>@endif
                    </div>
                    <div class="case-body">
                        @if(t($item, 'category'))<span class="tag">{{ t($item, 'category') }}</span>@endif
                        <h3>{{ t($item, 'title') }}</h3>
                        <p class="clamp-3">{{ t($item, 'description') }}</p>
                        @if($itemResult)<span class="result-chip"><i class="fas fa-arrow-trend-up" aria-hidden="true"></i> {{ $itemResult }}</span>@endif
                        <span class="card-link">{{ __('Read the case study') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<nav class="wrap pager" aria-label="{{ __('Projects') }}">
    @if($previousProject)
        <a href="{{ $projectUrl($previousProject->id) }}" rel="prev"><small>{{ $back }} {{ __('Previous project') }}</small><strong>{{ t($previousProject, 'title') }}</strong></a>
    @else
        <span></span>
    @endif
    @if($nextProject)
        <a href="{{ $projectUrl($nextProject->id) }}" rel="next" class="is-next"><small>{{ __('Next project') }} {{ $arrow }}</small><strong>{{ t($nextProject, 'title') }}</strong></a>
    @endif
</nav>

@endsection

@if(!empty($gallery))
@push('dialogs')
<dialog class="lightbox" id="lightbox" aria-label="{{ __('Gallery') }} — {{ $projectTitle }}" data-title="{{ $projectTitle }}">
    <div class="lb-bar">
        <span id="lightboxCounter" aria-live="polite"></span>
        <div class="lb-tools">
            <a class="lb-btn" id="lightboxOpen" href="#" target="_blank" rel="noopener" aria-label="{{ __('Open in new tab') }}"><i class="fas fa-up-right-from-square"></i></a>
            <button type="button" class="lb-btn" id="lightboxClose" aria-label="{{ __('Close') }}" autofocus><i class="fas fa-xmark"></i></button>
        </div>
    </div>
    <div class="lb-stage" id="lightboxStage">
        <button type="button" class="lb-btn lb-nav lb-prev" id="lightboxPrev" aria-label="{{ __('Previous image') }}">{{ $back }}</button>
        <img id="lightboxImage" src="" alt="">
        <button type="button" class="lb-btn lb-nav lb-next" id="lightboxNext" aria-label="{{ __('Next image') }}">{{ $arrow }}</button>
    </div>
    <p class="lb-cap" id="lightboxCaption"></p>
</dialog>
@endpush
@endif
