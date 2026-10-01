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
    $aboutHeading = ts($settings, 'about_heading') ?: ($settings['about_heading'] ?? '');
    $aboutParagraphs = array_values(array_filter([ts($settings, 'about_p1'), ts($settings, 'about_p2'), ts($settings, 'about_p3')]));
    $arrow = $isRtl ? '←' : '→';
    $stats = [
        [$settings['hero_stat_projects'] ?? '30+', __('Projects delivered')],
        [$settings['hero_stat_clients'] ?? '15+', __('Happy clients')],
        [$settings['hero_stat_years'] ?? '4+', __('Years of experience')],
    ];
    $projectUrl = fn ($id) => \App\Http\Middleware\SetLocale::localizedUrl(route('project.show', $id), $locale);
    $yearOf = fn (?string $duration) => preg_match_all('/(19|20)\d{2}/', (string) $duration, $m) ? end($m[0]) : '';
    $section = 0;
    $label = function (string $name) use (&$section) {
        $section++;

        return '<p class="label reveal"><span class="label-num">'.str_pad((string) $section, 2, '0', STR_PAD_LEFT).'</span>'.e($name).'</p>';
    };
@endphp

@section('content')

{{-- ═════════ HERO ═════════ --}}
<section class="hero" id="top" aria-labelledby="heroTitle">
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <p class="availability reveal"><span class="status-dot" aria-hidden="true"></span>{{ __('Available for new projects') }}</p>
            <h1 class="display reveal" id="heroTitle" style="--d:.06s">{{ __('I build and fix web systems that help your business') }} <em>{{ __('grow.') }}</em></h1>
            <p class="lead reveal" style="--d:.14s">{!! $heroSubtitle !!}</p>
            <div class="hero-actions reveal" style="--d:.22s">
                <a href="#contact" class="btn btn-primary btn-lg">{{ __('Get a free consultation') }}</a>
                <a href="#projects" class="text-link">{{ __('See my work') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
            </div>
            <ul class="assurances reveal" style="--d:.3s">
                <li>{{ __('Fixed quote before any work starts') }}</li>
                <li>{{ __('You own the code') }}</li>
                <li>{{ __('Support after delivery') }}</li>
            </ul>
        </div>

        <figure class="portrait reveal" style="--d:.1s">
            <div class="portrait-img">
                <picture>
                    <source srcset="{{ asset('images/me.webp') }}" type="image/webp">
                    <img src="{{ asset('images/me.jpeg') }}" alt="{{ $heroName }} — {{ $heroTagline }}" width="900" height="900" fetchpriority="high">
                </picture>
            </div>
            <figcaption>
                <span><strong>{{ $heroName }}</strong>{{ $heroTagline }}</span>
                <span class="portrait-loc"><i class="fas fa-location-dot" aria-hidden="true"></i> {{ __('Gaza · Remote') }}</span>
            </figcaption>
        </figure>
    </div>

    <div class="wrap">
        <dl class="stats reveal">
            @foreach($stats as [$value, $statLabel])
                <div><dd data-count dir="ltr">{{ $value }}</dd><dt>{{ $statLabel }}</dt></div>
            @endforeach
        </dl>
    </div>
</section>

{{-- ═════════ SERVICES ═════════ --}}
<section class="section" id="services" aria-labelledby="servicesTitle">
    <div class="wrap">
        <header class="section-head">
            {!! $label(__('Services')) !!}
            <h2 class="title reveal" id="servicesTitle">{{ __('How I can help your business') }}</h2>
            <p class="section-sub reveal">{{ __('Whether you are starting from an idea or already have a system, I make sure the technology behind your business is solid, fast, and ready to grow.') }}</p>
        </header>

        <div class="service-grid">
            @foreach($services as $service)
                @php
                    $deliverables = ($locale === 'ar' && ! empty($service->deliverables_ar)) ? $service->deliverables_ar : ($service->deliverables ?? []);
                @endphp
                <article class="card service-card reveal" style="--d: {{ $loop->index * .08 }}s">
                    <span class="service-icon" aria-hidden="true"><i class="{{ $service->icon ?: 'fa-solid fa-code' }}"></i></span>
                    <h3>{{ t($service, 'title') }}</h3>
                    <p>{{ t($service, 'summary') }}</p>
                    @if(!empty($deliverables))
                        <ul class="ticks">
                            @foreach($deliverables as $item)<li>{{ $item }}</li>@endforeach
                        </ul>
                    @endif
                    <a href="#contact" class="text-link" data-service="{{ t($service, 'title') }}">{{ __('Discuss this service') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
                </article>
            @endforeach
        </div>

        <div class="note reveal">
            <p><strong>{{ __('Not sure what you need?') }}</strong> {{ __('Describe the problem in your own words — I’ll tell you honestly what it needs, even if it’s a small fix.') }}</p>
            <a href="#contact" class="btn btn-ghost">{{ __('Ask me') }}</a>
        </div>
    </div>
</section>

{{-- ═════════ WORK ═════════ --}}
<section class="section section-tint" id="projects" aria-labelledby="workTitle">
    <div class="wrap">
        <header class="section-head">
            {!! $label(__('Selected work')) !!}
            <h2 class="title reveal" id="workTitle">{{ __('Real problems, solved') }}</h2>
            <p class="section-sub reveal">{{ __('Real systems I have built and improved — open any project to see the challenge, the approach, and the result.') }}</p>
        </header>

        <div class="work-grid">
            @foreach($projects as $project)
                @php
                    $image = $project->image ? project_image_url($project->image) : null;
                    $result = t($project, 'result');
                    $year = $yearOf($project->duration);
                @endphp
                <a href="{{ $projectUrl($project->id) }}" class="work-card reveal @if($loop->first) is-featured @endif" style="--d: {{ ($loop->index % 2) * .08 }}s">
                    <div class="work-media">
                        @if($image)
                            <img src="{{ $image }}" alt="" loading="lazy">
                        @else
                            <span class="work-emoji" aria-hidden="true">{{ $project->icon ?: '🚀' }}</span>
                        @endif
                    </div>
                    <div class="work-body">
                        <p class="work-meta">{{ t($project, 'category') }}@if($year) <span aria-hidden="true">·</span> <span dir="ltr">{{ $year }}</span>@endif</p>
                        <h3>{{ t($project, 'title') }}</h3>
                        @if($loop->first)<p class="work-desc">{{ t($project, 'description') }}</p>@endif
                        @if($result)<p class="work-result"><i class="fas fa-arrow-trend-up" aria-hidden="true"></i>{{ $result }}</p>@endif
                        <span class="text-link">{{ __('Read the case study') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ═════════ TESTIMONIALS ═════════ --}}
@if(!empty($testimonials))
<section class="section" id="testimonials" aria-labelledby="testimonialsTitle">
    <div class="wrap">
        <header class="section-head">
            {!! $label(__('Testimonials')) !!}
            <h2 class="title reveal" id="testimonialsTitle">{{ __('What clients say') }}</h2>
        </header>
        <div class="quote-grid">
            @foreach($testimonials as $testimonial)
                <figure class="card quote reveal" style="--d: {{ ($loop->index % 3) * .08 }}s">
                    <blockquote>“{{ t($testimonial, 'quote') }}”</blockquote>
                    <figcaption>
                        <strong>{{ $testimonial->name }}</strong>
                        @if(t($testimonial, 'role'))<span>{{ t($testimonial, 'role') }}</span>@endif
                        @if($testimonial->project_id && $testimonial->project_title)
                            <a href="{{ $projectUrl($testimonial->project_id) }}">{{ $locale === 'ar' && $testimonial->project_title_ar ? $testimonial->project_title_ar : $testimonial->project_title }}</a>
                        @endif
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════ PROCESS ═════════ --}}
<section class="section" id="process" aria-labelledby="processTitle">
    <div class="wrap">
        <header class="section-head">
            {!! $label(__('Process')) !!}
            <h2 class="title reveal" id="processTitle">{{ __('How we work together') }}</h2>
            <p class="section-sub reveal">{{ __('A simple, transparent process — you always know what is happening and what comes next.') }}</p>
        </header>
        <ol class="steps">
            @foreach([
                [__('Discovery'), __('We talk about your project, the problem, and the goal. No technical jargon needed.')],
                [__('Audit & Plan'), __('I review the code or requirements and send a clear plan with scope, timeline, and price.')],
                [__('Build & Update'), __('I get to work and share regular progress updates, so there are no surprises.')],
                [__('Deliver & Support'), __('You get tested, documented work — plus support after delivery to make sure everything runs smoothly.')],
            ] as [$title, $text])
                <li class="step reveal" style="--d: {{ $loop->index * .08 }}s">
                    <span class="step-num" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3>{{ $title }}</h3>
                    <p>{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ═════════ ABOUT ═════════ --}}
<section class="section section-tint" id="about" aria-labelledby="aboutTitle">
    <div class="wrap">
        <header class="section-head">
            {!! $label(__('About')) !!}
        </header>
        <div class="about-grid">
            <div>
                <h2 class="title reveal" id="aboutTitle">{!! $aboutHeading ?: e($heroName) !!}</h2>
                <div class="about-actions reveal">
                    @if($cvUrl)<a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="btn btn-ghost" data-cv-open><i class="fas fa-file-lines" aria-hidden="true"></i> {{ __('View my CV') }}</a>@endif
                    @if($linkedinUrl)<a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="icon-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                    @if($githubUrl)<a href="{{ $githubUrl }}" target="_blank" rel="noopener" class="icon-btn" aria-label="GitHub"><i class="fab fa-github"></i></a>@endif
                </div>
            </div>
            <div class="about-copy">
                @foreach($aboutParagraphs as $paragraph)
                    <p class="reveal">{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>

        <ul class="values">
            @foreach([
                [__('Clean, maintainable code'), __('Code the next developer can read and extend.')],
                [__('Honest communication'), __('Clear updates, realistic estimates, no surprises.')],
                [__('Performance first'), __('Fast systems that stay fast as you grow.')],
            ] as [$valueTitle, $valueText])
                <li class="reveal" style="--d: {{ $loop->index * .08 }}s"><strong>{{ $valueTitle }}</strong><span>{{ $valueText }}</span></li>
            @endforeach
        </ul>

        @if(!empty($experiences) || !empty($categories))
        <div class="exp-skills">
            @if(!empty($experiences))
            <div class="reveal" id="experience">
                <h3 class="sub-title">{{ __('Experience') }}</h3>
                <ol class="exp-list">
                    @foreach($experiences as $exp)
                        <li>
                            <span class="exp-date" dir="ltr">{{ $exp->date_range }}</span>
                            <span class="exp-role"><strong>{{ t($exp, 'title') }}</strong><span>{{ t($exp, 'company') }}</span></span>
                        </li>
                    @endforeach
                </ol>
            </div>
            @endif
            @if(!empty($categories))
            <div class="reveal" id="skills" style="--d:.08s">
                <h3 class="sub-title">{{ __('Toolkit') }}</h3>
                @foreach($categories as $cat)
                    <div class="skill-group">
                        <h4>{{ t($cat, 'name') }}</h4>
                        <p>{{ implode(' · ', array_map(fn ($skill) => t($skill, 'name'), $cat->skills)) }}</p>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif
    </div>
</section>

{{-- ═════════ FAQ ═════════ --}}
@if(!empty($faqs))
<section class="section" id="faq" aria-labelledby="faqTitle">
    <div class="wrap faq-grid">
        <header>
            {!! $label(__('FAQ')) !!}
            <h2 class="title reveal" id="faqTitle">{{ __('Questions clients usually ask') }}</h2>
            <p class="section-sub reveal">{{ __('Can’t find your answer? Ask me directly — I reply personally.') }}</p>
            @if($whatsappNumber)<a class="text-link reveal" href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode(__('Hello, I found you through your portfolio.')) }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i> {{ __('Ask on WhatsApp') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>@endif
        </header>
        <div class="faq-list reveal">
            @foreach($faqs as $faq)
                <details class="faq-item" @if($loop->first) open @endif>
                    <summary>{{ t($faq, 'question') }}<span class="faq-icon" aria-hidden="true"></span></summary>
                    <p>{{ t($faq, 'answer') }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════ CONTACT ═════════ --}}
<section class="section contact" id="contact" aria-labelledby="contactTitle">
    <div class="wrap contact-grid">
        <div class="contact-intro">
            {!! $label(__('Contact')) !!}
            <h2 class="display-sm reveal" id="contactTitle">{{ __('Let’s build something that') }} <em>{{ __('works.') }}</em></h2>
            <p class="section-sub reveal">{{ __('Tell me briefly what you need. You will get a clear answer on how I can help, how long it takes, and what it costs — no obligation.') }}</p>
            <ol class="contact-steps reveal">
                <li>{{ __('You send a short message about your project.') }}</li>
                <li>{{ __('I reply with questions or a first recommendation.') }}</li>
                <li>{{ __('You get a clear plan with scope, timeline, and price.') }}</li>
            </ol>
            <ul class="direct reveal">
                @if($whatsappNumber)<li><a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i><span dir="ltr">+{{ $whatsappNumber }}</span></a></li>@endif
                @if($contactEmail)<li><a href="mailto:{{ $contactEmail }}"><i class="fas fa-envelope" aria-hidden="true"></i><span>{{ $contactEmail }}</span></a></li>@endif
                @if($cvUrl)<li><a href="{{ $cvUrl }}" target="_blank" rel="noopener" data-cv-open><i class="fas fa-file-lines" aria-hidden="true"></i><span>{{ __('View & download my CV') }}</span></a></li>@endif
            </ul>
        </div>

        <div class="card form-card reveal" style="--d:.08s">
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
                    <button type="submit" class="btn btn-primary btn-lg" id="contactSubmit"><span class="btn-label">{{ __('Send Message') }}</span> <span class="arrow" aria-hidden="true">{{ $arrow }}</span></button>
                    @if($whatsappNumber)
                        <button type="button" class="btn btn-ghost btn-lg" id="contactWhatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i> {{ __('Send via WhatsApp') }}</button>
                    @endif
                </div>
                <p class="form-note">{{ __('I reply personally, usually within 1–2 days. You will also get a confirmation email.') }}</p>
            </form>
        </div>
    </div>
</section>

@endsection
