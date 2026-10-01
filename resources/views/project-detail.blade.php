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
    $upNext = $nextProject ?? ($related[0] ?? null);
    $chapter = 0;
    $chapterLabel = function () use (&$chapter) {
        return '('.chr(65 + $chapter++).')';
    };
@endphp

@section('footer-class', 'is-standalone')

@section('content')

{{-- ═════════ HERO ═════════ --}}
<header class="p-hero">
    <div class="wrap">
        <nav class="crumbs mono" aria-label="{{ __('Breadcrumb') }}">
            <a href="{{ $homeUrl }}">{{ __('Home') }}</a><span aria-hidden="true">/</span>
            <a href="{{ $homeUrl }}#projects">{{ __('My Work') }}</a><span aria-hidden="true">/</span>
            <span aria-current="page">{{ $projectTitle }}</span>
        </nav>

        <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.5rem" class="reveal">
            @if($category)<span class="chip">{{ $category }}</span>@endif
            @if($project->featured)<span class="chip chip-solid">✳ {{ __('Featured') }}</span>@endif
            @if($duration)<span class="chip" dir="auto">{{ $duration }}</span>@endif
        </div>

        <h1 class="p-title" data-split>{{ $projectTitle }}</h1>

        <div class="p-intro">
            <p class="lead reveal" style="--d:.3s">{{ t($project, 'description') }}</p>
            <div class="hero-actions reveal" style="--d:.45s">
                <a href="{{ $similarUrl }}" @if($whatsappNumber) target="_blank" rel="noopener" @endif class="btn btn-accent" data-magnetic>{{ __('I want something similar') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
                @if($project->live_url)<a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn btn-line" data-magnetic>{{ __('Live Demo') }} ↗</a>@endif
            </div>
        </div>

        <dl class="facts reveal">
            <div><dt>{{ __('Client') }}</dt><dd>{{ $client ?: __('Not specified') }}</dd></div>
            <div><dt>{{ __('Duration') }}</dt><dd dir="auto">{{ $duration ?: __('Not specified') }}</dd></div>
            <div><dt>{{ __('Category') }}</dt><dd>{{ $category ?: __('Not specified') }}</dd></div>
            <div><dt>{{ __('Technologies') }}</dt><dd>{{ count($stack) }} {{ __('tools') }}</dd></div>
        </dl>
    </div>
</header>

{{-- ═════════ COVER ═════════ --}}
<div class="p-cover">
    @if($projectImage)
        <img src="{{ $projectImage }}" alt="{{ $projectTitle }}" fetchpriority="high" data-parallax="0.1">
    @else
        <div class="placeholder" aria-hidden="true">
            <span class="emoji">{{ $project->icon ?: '✳' }}</span>
            @if(!empty($stack))<div class="stack">@foreach(array_slice($stack, 0, 6) as $tech)<span>{{ $tech }}</span>@endforeach</div>@endif
        </div>
    @endif
</div>

<div class="wrap">
    {{-- ═════════ STORY ═════════ --}}
    @if($overview)
    <section class="p-body" aria-labelledby="storyTitle">
        <div class="side reveal"><span class="idx">{{ $chapterLabel() }}</span><h2 id="storyTitle">{{ __('The challenge & the solution') }}</h2></div>
        <p class="p-story reveal">{{ $overview }}</p>
    </section>
    @endif

    {{-- ═════════ GALLERY ═════════ --}}
    @if(!empty($gallery))
    <section class="p-body" aria-labelledby="galleryTitle" id="gallery">
        <div class="side reveal"><span class="idx">{{ $chapterLabel() }}</span><h2 id="galleryTitle">{{ __('Screenshots') }} <span class="muted">({{ count($gallery) }})</span></h2></div>
        <ul class="gallery">
            @foreach($gallery as $image)
                @php
                    $caption = t($image, 'caption');
                @endphp
                <li class="reveal" style="--d: {{ ($loop->index % 3) * .08 }}s">
                    <a href="{{ asset('uploads/'.$image->path) }}" class="gallery-item" data-full="{{ asset('uploads/'.$image->path) }}" data-caption="{{ $caption }}" data-cursor="{{ __('Open') }}"
                       aria-label="{{ __('Open image :number of :total', ['number' => $loop->iteration, 'total' => count($gallery)]) }}{{ $caption ? ' — '.$caption : '' }}">
                        <img src="{{ asset('uploads/'.($loop->index % 5 === 0 ? $image->path : $image->thumb_path)) }}"
                             alt="{{ $caption ?: $projectTitle.' — '.__('screenshot :number', ['number' => $loop->iteration]) }}"
                             loading="lazy" decoding="async" @if($image->width && $image->height) width="{{ $image->width }}" height="{{ $image->height }}" @endif>
                        @if($caption)<span class="cap">{{ $caption }}</span>@endif
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- ═════════ STAGES ═════════ --}}
    <section class="p-body" aria-labelledby="stagesTitle">
        <div class="side reveal"><span class="idx">{{ $chapterLabel() }}</span><h2 id="stagesTitle">{{ __('How I built it') }}</h2></div>
        @if(!empty($stages))
            <ol class="steps">
                @foreach($stages as $stage)
                    <li class="reveal" style="--d: {{ $loop->index * .06 }}s"><span class="n">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span>{{ $stage }}</span></li>
                @endforeach
            </ol>
        @else
            <p class="muted">{{ __('No stages added yet.') }}</p>
        @endif
    </section>

    {{-- ═════════ STACK & SHARE ═════════ --}}
    <section class="p-body" aria-labelledby="stackTitle">
        <div class="side reveal"><span class="idx">{{ $chapterLabel() }}</span><h2 id="stackTitle">{{ __('Tech Stack') }}</h2></div>
        <div class="reveal">
            <div class="toolbox">@foreach($stack as $tech)<span class="chip">{{ $tech }}</span>@endforeach</div>
            @if($project->live_url || $project->github_url)
                <div class="p-links">
                    @if($project->live_url)<a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn">{{ __('Live Demo') }} ↗</a>@endif
                    @if($project->github_url)<a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="btn btn-line"><i class="fab fa-github" aria-hidden="true"></i> {{ __('Source Code') }}</a>@endif
                </div>
            @endif
            <div class="share">
                <span class="mono muted">{{ __('Share this project') }}</span>
                <button type="button" class="icon-btn" id="copyLink" data-url="{{ $pageUrl }}" aria-label="{{ __('Copy link') }}"><i class="fas fa-link"></i></button>
                <a class="icon-btn" href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode($pageUrl) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on LinkedIn') }}"><i class="fab fa-linkedin-in"></i></a>
                <a class="icon-btn" href="https://wa.me/?text={{ rawurlencode($projectTitle.' — '.$pageUrl) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on WhatsApp') }}"><i class="fab fa-whatsapp"></i></a>
                <a class="icon-btn" href="https://x.com/intent/post?url={{ rawurlencode($pageUrl) }}&text={{ rawurlencode($projectTitle) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on X') }}"><i class="fab fa-x-twitter"></i></a>
                <span class="copy-feedback" id="copyFeedback" role="status"></span>
            </div>
        </div>
    </section>
</div>

{{-- ═════════ CTA ═════════ --}}
<section class="cta-band" aria-labelledby="ctaTitle">
    <div class="wrap">
        <h2 id="ctaTitle"><span data-split>{{ __('Need something similar?') }}</span></h2>
        <div class="hero-actions">
            @if($whatsappNumber)<a href="{{ $similarUrl }}" target="_blank" rel="noopener" class="btn btn-wa" data-magnetic><i class="fab fa-whatsapp" aria-hidden="true"></i> {{ __('Chat on WhatsApp') }}</a>@endif
            <a href="{{ $homeUrl }}#contact" class="btn" data-magnetic>{{ __('Discuss Your Project') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
        </div>
    </div>
</section>

{{-- ═════════ NEXT ═════════ --}}
<div class="wrap">
    @if($upNext)
        <a href="{{ $projectUrl($upNext->id) }}" class="next-project" rel="next" data-cursor="{{ __('Next') }}">
            <small class="mono muted">{{ __('Next project') }} {{ $arrow }}</small>
            <span class="t">{{ t($upNext, 'title') }}</span>
        </a>
    @endif
    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:1rem;padding-block:1.5rem 4rem">
        @if($previousProject)
            <a class="prev-link" href="{{ $projectUrl($previousProject->id) }}" rel="prev">{{ $back }} {{ __('Previous project') }}: {{ t($previousProject, 'title') }}</a>
        @else
            <span></span>
        @endif
        <a class="prev-link" href="{{ $homeUrl }}#projects">{{ __('All projects') }} {{ $arrow }}</a>
    </div>
</div>

@endsection

@if(!empty($gallery))
@push('dialogs')
<dialog class="lightbox" id="lightbox" aria-label="{{ __('Gallery') }} — {{ $projectTitle }}" data-title="{{ $projectTitle }}">
    <div class="lb-bar">
        <span class="mono" id="lightboxCounter" aria-live="polite"></span>
        <div style="display:flex;gap:.5rem">
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
