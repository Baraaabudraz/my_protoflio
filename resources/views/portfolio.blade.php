@extends('layouts.site')

@php
    $heroName = ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? 'Your Name');
    $heroTagline = ts($settings, 'hero_tagline') ?: ($settings['hero_tagline'] ?? '');
    $heroSubtitle = ts($settings, 'hero_subtitle') ?: ($settings['hero_subtitle'] ?? '');
    $whatsappNumber = preg_replace('/\D+/', '', $settings['whatsapp_number'] ?? '');
    $contactEmail = $settings['email'] ?? '';
    $githubUrl = $settings['github_url'] ?? '';
    $linkedinUrl = $settings['linkedin_url'] ?? '';
    $cvPath = $settings['cv_path'] ?? '';
    $cvUrl = ($cvPath !== '' && file_exists(public_path($cvPath))) ? asset($cvPath) : null;
    $techTags = array_values(array_filter(array_map('trim', explode(',', ts($settings, 'about_tags') ?: ($settings['about_tags'] ?? '')))));
    $aboutHeading = ts($settings, 'about_heading') ?: ($settings['about_heading'] ?? '');
    $statement = ts($settings, 'about_p1') ?: ($settings['about_p1'] ?? '');
    $aboutParagraphs = array_filter([ts($settings, 'about_p2'), ts($settings, 'about_p3')]);
    $arrow = $isRtl ? '←' : '→';
    $section = 0;
    $label = function (string $name) use (&$section) {
        $section++;

        return '<div class="section-label reveal"><span class="idx">('.str_pad((string) $section, 2, '0', STR_PAD_LEFT).')</span><span class="name">'.e($name).'</span><span class="rule line-grow" aria-hidden="true"></span></div>';
    };
    $yearOf = fn (?string $duration) => preg_match_all('/(19|20)\d{2}/', (string) $duration, $m) ? end($m[0]) : '';
@endphp

@section('content')

{{-- ═════════ HERO ═════════ --}}
<section class="hero" id="top" aria-labelledby="heroTitle">
    <div class="wrap">
        <div class="hero-meta mono reveal">
            <span>{{ $heroName }}</span>
            <span><span class="status-dot" aria-hidden="true"></span>{{ __('Available for new projects') }}</span>
            <span>{{ __('Gaza, Palestine') }} · <span data-clock="Asia/Gaza" dir="ltr">--:--</span></span>
        </div>

        <h1 class="hero-title display" id="heroTitle">
            <span class="row"><span data-split>{{ __('Build.') }}</span>
                <span class="hero-photo" aria-hidden="true"><img src="{{ asset('images/me.webp') }}" alt="" width="300" height="300" fetchpriority="high"></span>
            </span>
            <span class="row"><span class="serif no-split">{{ __('and') }}</span><span data-split class="accent-word" style="--d:200ms">{{ __('Fix.') }}</span></span>
            <span class="row"><span data-split style="--d:400ms">{{ __('Scale.') }}</span><span class="serif no-split">{{ __('web systems') }}</span></span>
        </h1>

        <div class="hero-bottom">
            <div class="reveal" style="--d:.5s">
                <p class="lead">{!! $heroSubtitle !!}</p>
                <span class="scroll-cue mono" aria-hidden="true"><span class="bar"></span>{{ __('Scroll') }}</span>
            </div>
            <div class="hero-actions reveal" style="--d:.65s">
                <a href="#contact" class="btn btn-accent" data-magnetic>{{ __('Discuss Your Project') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
                <a href="#projects" class="btn btn-line" data-magnetic>{{ __('See my work') }}</a>
            </div>
        </div>
    </div>
</section>

{{-- ═════════ MARQUEES ═════════ --}}
<div aria-hidden="true">
    @if(!empty($techTags))
    <div class="marquee" dir="ltr">
        <div class="marquee-track" data-dir="-1" data-speed="0.6">
            @foreach([1, 2] as $copy)
                @foreach($techTags as $tag)
                    <span class="marquee-item"><span class="{{ $loop->even ? 'outline' : '' }}" dir="auto">{{ $tag }}</span><span class="star">✳</span></span>
                @endforeach
            @endforeach
        </div>
    </div>
    @endif
    <div class="marquee is-accent" dir="ltr">
        <div class="marquee-track" data-dir="1" data-speed="0.5">
            @foreach([1, 2] as $copy)
                @foreach($services as $service)
                    <span class="marquee-item"><span dir="auto">{{ t($service, 'title') }}</span><span class="star">✳</span></span>
                @endforeach
                <span class="marquee-item"><span class="outline" dir="auto">{{ __('Free initial consultation') }}</span><span class="star">✳</span></span>
            @endforeach
        </div>
    </div>
</div>

{{-- ═════════ INTRO STATEMENT ═════════ --}}
<section class="section" aria-label="{{ __('Introduction') }}">
    <div class="wrap">
        {!! $label(__('Introduction')) !!}
        @if($statement)<p class="statement" data-scrub>{{ $statement }}</p>@endif
        <div class="stats">
            <div class="stat reveal"><div class="stat-num" data-count dir="ltr">{{ $settings['hero_stat_years'] ?? '4+' }}</div><div class="stat-label">{{ __('Years building for the web') }}</div></div>
            <div class="stat reveal" style="--d:.1s"><div class="stat-num" data-count dir="ltr">{{ $settings['hero_stat_projects'] ?? '30+' }}</div><div class="stat-label">{{ __('Projects delivered') }}</div></div>
            <div class="stat reveal" style="--d:.2s"><div class="stat-num" data-count dir="ltr">{{ $settings['hero_stat_clients'] ?? '15+' }}</div><div class="stat-label">{{ __('Happy clients') }}</div></div>
        </div>
    </div>
</section>

{{-- ═════════ SERVICES ═════════ --}}
<section class="section" id="services" aria-labelledby="servicesTitle" style="padding-top:0">
    <div class="wrap">
        {!! $label(__('Services')) !!}
        <div class="section-head">
            <h2 class="h-section" id="servicesTitle"><span data-split>{{ __('How I can') }}</span> <span class="serif">{{ __('help you') }}</span></h2>
            <p class="lead reveal">{{ __('Whether you are starting from an idea or already have a system, I make sure the technology behind your business is solid, fast, and ready to grow.') }}</p>
        </div>

        <div class="rows">
            @foreach($services as $service)
                @php
                    $deliverables = ($locale === 'ar' && ! empty($service->deliverables_ar)) ? $service->deliverables_ar : ($service->deliverables ?? []);
                @endphp
                <details class="row-item reveal" @if($loop->first) open @endif>
                    <summary class="row-head">
                        <span class="num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="title">{{ t($service, 'title') }}</span>
                        <span class="plus" aria-hidden="true"></span>
                    </summary>
                    <div class="row-body">
                        <div>
                            <p>{{ t($service, 'summary') }}</p>
                            <a href="#contact" class="btn" data-service="{{ t($service, 'title') }}" data-magnetic>{{ __('Discuss this service') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
                        </div>
                        @if(!empty($deliverables))
                            <div>
                                <p class="mono muted" style="margin-bottom:1rem;font-size:.75rem">{{ __("What's included") }}</p>
                                <ul class="deliverables">
                                    @foreach($deliverables as $item)<li>{{ $item }}</li>@endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>

        <div class="help-note reveal">
            <div>
                <strong>{{ __('Not sure what you need?') }}</strong>
                <p class="muted">{{ __('Describe the problem in your own words — I’ll tell you honestly what it needs, even if it’s a small fix.') }}</p>
            </div>
            <a href="#contact" class="btn btn-line" data-magnetic>{{ __('Ask me') }}</a>
        </div>
    </div>
</section>

{{-- ═════════ WORK ═════════ --}}
<section class="section" id="projects" aria-labelledby="workTitle" style="padding-top:0">
    <div class="wrap">
        {!! $label(__('Selected work')) !!}
        <div class="section-head">
            <h2 class="h-section" id="workTitle"><span data-split>{{ __('Work I’m') }}</span> <span class="serif">{{ __('proud of') }}</span></h2>
            <p class="lead reveal">{{ __('Real systems I have built and improved — open any project to see the challenge, the approach, and the result.') }}</p>
        </div>

        <div class="work-list">
            @foreach($projects as $project)
                @php
                    $image = $project->image ? project_image_url($project->image) : null;
                @endphp
                <a href="{{ \App\Http\Middleware\SetLocale::localizedUrl(route('project.show', $project->id), $locale) }}"
                   class="work-row reveal" data-preview data-cursor="{{ __('View') }}"
                   @if($image) data-img="{{ $image }}" @else data-emoji="{{ $project->icon }}" @endif>
                    <span class="thumb" aria-hidden="true">@if($image)<img src="{{ $image }}" alt="" loading="lazy">@else{{ $project->icon }}@endif</span>
                    <span class="num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="title">{{ t($project, 'title') }}@if($project->featured)<span class="featured" title="{{ __('Featured') }}"></span>@endif</span>
                    <span class="cat">{{ t($project, 'category') }}</span>
                    <span class="year">{{ $yearOf($project->duration) }}</span>
                    <span class="go" aria-hidden="true">{{ $arrow }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <div class="work-preview" aria-hidden="true"></div>
</section>

{{-- ═════════ PROCESS (pinned horizontal) ═════════ --}}
@php
    $steps = [
        [__('Discovery'), __('We talk about your project, the problem, and the goal. No technical jargon needed.')],
        [__('Audit & Plan'), __('I review the code or requirements and send a clear plan with scope, timeline, and price.')],
        [__('Build & Update'), __('I get to work and share regular progress updates, so there are no surprises.')],
        [__('Deliver & Support'), __('You get tested, documented work — plus support after delivery to make sure everything runs smoothly.')],
    ];
@endphp
<section class="process" id="process" aria-labelledby="processTitle">
    <div class="process-pin">
        <div class="wrap">{!! $label(__('Process')) !!}</div>
        <div class="process-track">
            <div class="process-intro">
                <h2 class="h-section" id="processTitle"><span data-split>{{ __('How we') }}</span> <span class="serif">{{ __('work together') }}</span></h2>
                <p class="lead">{{ __('A simple, transparent process — you always know what is happening and what comes next.') }}</p>
            </div>
            @foreach($steps as [$title, $text])
                <article class="process-card">
                    <span class="big" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $text }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="process-progress" aria-hidden="true"><span></span></div>
    </div>
</section>
<div class="wrap" style="padding-block:clamp(2rem,5vw,4rem)">
    <ul class="guarantees reveal">
        <li><i class="fas fa-file-signature" aria-hidden="true"></i>{{ __('Clear scope & price before starting') }}</li>
        <li><i class="fas fa-clock-rotate-left" aria-hidden="true"></i>{{ __('Regular progress updates') }}</li>
        <li><i class="fas fa-book-open" aria-hidden="true"></i>{{ __('Clean, documented code you own') }}</li>
        <li><i class="fas fa-life-ring" aria-hidden="true"></i>{{ __('Support after delivery') }}</li>
    </ul>
</div>

{{-- ═════════ ABOUT ═════════ --}}
<section class="section" id="about" aria-labelledby="aboutTitle">
    <div class="wrap">
        {!! $label(__('Who Am I')) !!}
        <div class="section-head">
            <h2 class="h-section" id="aboutTitle"><span data-split>{{ __('The person') }}</span> <span class="serif">{{ __('behind the code') }}</span></h2>
        </div>
        <div class="about-grid">
            <figure class="figure reveal">
                <div class="figure-frame">
                    <picture>
                        <source srcset="{{ asset('images/me.webp') }}" type="image/webp">
                        <img src="{{ asset('images/me.jpeg') }}" alt="{{ $heroName }} — {{ $heroTagline }}" width="900" height="900" loading="lazy" data-parallax="0.08">
                    </picture>
                </div>
                <figcaption><span class="serif">{{ __('fig. 01') }}</span><span>{{ $heroName }} · {{ __('Gaza, Palestine') }}</span></figcaption>
            </figure>

            <div class="about-copy">
                @if($aboutHeading)<h3 class="reveal">{!! $aboutHeading !!}</h3>@endif
                @foreach($aboutParagraphs as $paragraph)
                    <p class="reveal">{{ $paragraph }}</p>
                @endforeach

                <ol class="values">
                    @foreach([
                        [__('Clean, maintainable code'), __('Code the next developer can read and extend.')],
                        [__('Honest communication'), __('Clear updates, realistic estimates, no surprises.')],
                        [__('Performance first'), __('Fast systems that stay fast as you grow.')],
                    ] as [$valueTitle, $valueText])
                        <li class="reveal" style="--d: {{ $loop->index * .08 }}s"><span class="n">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><strong>{{ $valueTitle }}</strong><span>{{ $valueText }}</span></div></li>
                    @endforeach
                </ol>

                @if(!empty($techTags))
                    <p class="mono muted" style="margin-bottom:.9rem">{{ __('My toolbox') }}</p>
                    <div class="toolbox reveal">
                        @foreach($techTags as $tag)<span class="chip">{{ $tag }}</span>@endforeach
                    </div>
                @endif

                <div class="about-actions reveal">
                    @if($cvUrl)<a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="btn" data-cv-open data-magnetic><i class="fas fa-file-lines" aria-hidden="true"></i> {{ __('View my CV') }}</a>@endif
                    @if($linkedinUrl)<a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="icon-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                    @if($githubUrl)<a href="{{ $githubUrl }}" target="_blank" rel="noopener" class="icon-btn" aria-label="GitHub"><i class="fab fa-github"></i></a>@endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════ EXPERIENCE ═════════ --}}
@if(!empty($experiences))
<section class="section" id="experience" aria-labelledby="experienceTitle" style="padding-top:0">
    <div class="wrap">
        {!! $label(__('Experience')) !!}
        <div class="section-head">
            <h2 class="h-section" id="experienceTitle"><span data-split>{{ __('Where I’ve') }}</span> <span class="serif">{{ __('been building') }}</span></h2>
        </div>
        <div class="rows">
            @foreach($experiences as $exp)
                <details class="row-item exp-item reveal">
                    <summary class="exp-head">
                        <span class="dates" dir="ltr">{{ $exp->date_range }}</span>
                        <span class="role" @if($loop->first && str_contains(strtolower($exp->date_range), 'present')) data-now="{{ __('Now') }}" @endif>{{ t($exp, 'title') }}</span>
                        <span class="company">{{ t($exp, 'company') }}</span>
                        <span class="plus" aria-hidden="true"></span>
                    </summary>
                    <div class="exp-body">
                        <span aria-hidden="true"></span>
                        <p>{{ t($exp, 'description') }}</p>
                        @if(!empty($exp->tags))
                            <div class="tags">@foreach($exp->tags as $tag)<span class="chip">{{ $tag }}</span>@endforeach</div>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════ SKILLS ═════════ --}}
@if(!empty($categories))
<section class="section" id="skills" aria-labelledby="skillsTitle" style="padding-top:0">
    <div class="wrap">
        {!! $label(__('Toolkit')) !!}
        <h2 class="sr-only" id="skillsTitle">{{ __('Technical Skills') }}</h2>
        <div class="skills-grid">
            @foreach($categories as $cat)
                <div class="skill-col reveal" style="--d: {{ ($loop->index % 4) * .08 }}s">
                    <h3><span aria-hidden="true">{{ $cat->icon }}</span>{{ t($cat, 'name') }}</h3>
                    @if($cat->type === 'bars')
                        @foreach($cat->skills as $skill)
                            <div class="skill-line"><span class="name">{{ t($skill, 'name') }}</span><span class="dots" aria-hidden="true"></span><span class="pct">{{ $skill->percentage }}%</span></div>
                        @endforeach
                    @else
                        <div class="skill-tags">@foreach($cat->skills as $skill)<span class="chip">{{ t($skill, 'name') }}</span>@endforeach</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════ CONTACT ═════════ --}}
<section class="section contact" id="contact" aria-labelledby="contactTitle">
    <div class="wrap">
        {!! $label(__('Contact')) !!}
        <h2 class="talk" id="contactTitle"><span data-split>{{ __('Let’s') }}</span> <span data-split class="accent-word" style="--d:150ms">{{ __('talk.') }}</span></h2>

        <div class="contact-grid">
            <div>
                <p class="lead reveal" style="color:inherit;opacity:.85;margin-bottom:2rem">{{ __('Tell me briefly what you need. You will get a clear answer on how I can help, how long it takes, and what it costs — no obligation.') }}</p>
                <ol class="contact-steps reveal">
                    <li>{{ __('You send a short message about your project.') }}</li>
                    <li>{{ __('I reply with questions or a first recommendation.') }}</li>
                    <li>{{ __('You get a clear plan with scope, timeline, and price.') }}</li>
                </ol>
                <div class="direct reveal">
                    @if($contactEmail)<a href="mailto:{{ $contactEmail }}"><span><small>{{ __('Email') }}</small><span class="val">{{ $contactEmail }}</span></span><span aria-hidden="true">{{ $arrow }}</span></a>@endif
                    @if($whatsappNumber)<a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener"><span><small>WhatsApp</small><span class="val" dir="ltr">+{{ $whatsappNumber }}</span></span><span aria-hidden="true">{{ $arrow }}</span></a>@endif
                    @if($linkedinUrl)<a href="{{ $linkedinUrl }}" target="_blank" rel="noopener"><span><small>LinkedIn</small><span class="val">{{ __('Connect with me') }}</span></span><span aria-hidden="true">{{ $arrow }}</span></a>@endif
                    @if($cvUrl)<a href="{{ $cvUrl }}" target="_blank" rel="noopener" data-cv-open><span><small>{{ __('Curriculum Vitae') }}</small><span class="val">{{ __('View & download my CV') }}</span></span><span aria-hidden="true">{{ $arrow }}</span></a>@endif
                </div>
            </div>

            <div class="reveal">
                <div class="form-status is-success" id="contactStatus" role="status" aria-live="polite" @unless(session('contact_success')) hidden @endunless>
                    <i class="fas fa-circle-check" aria-hidden="true"></i><span>{{ session('contact_success') }}</span>
                </div>
                <div class="form-status is-error" id="contactError" role="alert" @unless(session('contact_error') || $errors->any()) hidden @endunless>
                    <i class="fas fa-circle-exclamation" aria-hidden="true"></i><span>{{ session('contact_error') ?: ($errors->any() ? __('Please check the highlighted fields.') : '') }}</span>
                </div>

                <form id="contactForm" class="form" method="POST" action="{{ route('contact.store') }}" novalidate data-whatsapp="{{ $whatsappNumber }}">
                    @csrf
                    <div class="hp-field" aria-hidden="true"><label for="cf-website">Website</label><input type="text" id="cf-website" name="website" tabindex="-1" autocomplete="off"></div>
                    <div class="form-row">
                        <div class="field">
                            <label for="cf-name">{{ __('Name') }}</label>
                            <input type="text" id="cf-name" name="name" value="{{ old('name') }}" placeholder="{{ __('Your name') }}" autocomplete="name" required maxlength="100" aria-describedby="cf-name-error" class="@error('name') is-invalid @enderror">
                            <p class="field-error" id="cf-name-error">@error('name'){{ $message }}@enderror</p>
                        </div>
                        <div class="field">
                            <label for="cf-email">{{ __('Email') }}</label>
                            <input type="email" id="cf-email" name="email" value="{{ old('email') }}" placeholder="you@company.com" autocomplete="email" required maxlength="150" dir="ltr" aria-describedby="cf-email-error" class="@error('email') is-invalid @enderror">
                            <p class="field-error" id="cf-email-error">@error('email'){{ $message }}@enderror</p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field">
                            <label for="cf-service">{{ __('What do you need?') }}</label>
                            <select id="cf-service" name="service">
                                @foreach($services as $service)
                                    <option value="{{ t($service, 'title') }}" @selected(old('service') === t($service, 'title'))>{{ t($service, 'title') }}</option>
                                @endforeach
                                <option value="{{ __('Something else') }}" @selected(old('service') === __('Something else'))>{{ __('Something else') }}</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="cf-budget">{{ __('Estimated budget') }} <small>({{ __('optional') }})</small></label>
                            <select id="cf-budget" name="budget">
                                <option value="">{{ __('Not sure yet') }}</option>
                                @foreach(['< $500', '$500 – $1,500', '$1,500 – $5,000', '$5,000+'] as $budgetOption)
                                    <option value="{{ $budgetOption }}" @selected(old('budget') === $budgetOption)>{{ $budgetOption }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="cf-phone">{{ __('Phone / WhatsApp') }} <small>({{ __('optional') }})</small></label>
                        <input type="tel" id="cf-phone" name="phone" value="{{ old('phone') }}" placeholder="+970 59 000 0000" autocomplete="tel" maxlength="30" dir="ltr">
                    </div>
                    <div class="field">
                        <label for="cf-message">{{ __('Message') }}</label>
                        <textarea id="cf-message" name="message" placeholder="{{ __('What is the problem, and what result do you want?') }}" required maxlength="5000" aria-describedby="cf-message-error" class="@error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                        <p class="field-error" id="cf-message-error">@error('message'){{ $message }}@enderror</p>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-accent" id="contactSubmit" data-magnetic><span class="btn-label">{{ __('Send Message') }}</span> <span class="arrow" aria-hidden="true">{{ $arrow }}</span></button>
                        @if($whatsappNumber)
                            <button type="button" class="btn btn-line" id="contactWhatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i> {{ __('Send via WhatsApp') }}</button>
                        @endif
                    </div>
                    <p class="muted" style="font-size:.88rem">{{ __('I reply personally, usually within 1–2 days. You will also get a confirmation email.') }}</p>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
