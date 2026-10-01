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
    $aboutParagraphs = array_values(array_filter([ts($settings, 'about_p1'), ts($settings, 'about_p2'), ts($settings, 'about_p3')]));
    $arrow = $isRtl ? '←' : '→';
    $stats = [
        [$settings['hero_stat_projects'] ?? '30+', __('Projects delivered')],
        [$settings['hero_stat_clients'] ?? '15+', __('Happy clients')],
        [$settings['hero_stat_years'] ?? '4+', __('Years of experience')],
    ];
    $industries = array_values(array_unique(array_filter(array_map(fn ($p) => t($p, 'category'), $projects))));
    $projectUrl = fn ($id) => \App\Http\Middleware\SetLocale::localizedUrl(route('project.show', $id), $locale);
    $featured = $projects[0] ?? null;
    $moreProjects = array_slice($projects, 1);
    $serviceIcons = ['fa-solid fa-laptop-code', 'fa-solid fa-screwdriver-wrench', 'fa-solid fa-gauge-high'];
@endphp

@section('content')

{{-- ═════════ HERO ═════════ --}}
<section class="hero" id="top" aria-labelledby="heroTitle">
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <p class="pill reveal"><span class="status-dot" aria-hidden="true"></span>{{ __('Available for new projects') }} · {{ __('Replies within 1–2 days') }}</p>
            <h1 class="hero-title reveal" id="heroTitle" style="--d:.05s">
                {{ __('Laravel systems that run your business —') }}
                <span class="hl">{{ __('built right, or fixed fast.') }}</span>
            </h1>
            <p class="lead reveal" style="--d:.12s">{!! $heroSubtitle !!}</p>
            <div class="hero-actions reveal" style="--d:.18s">
                <a href="#contact" class="btn btn-primary btn-lg">{{ __('Get a free consultation') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
                <a href="#projects" class="btn btn-ghost btn-lg">{{ __('See the results') }}</a>
            </div>
            <ul class="trust-list reveal" style="--d:.24s">
                <li><i class="fas fa-circle-check" aria-hidden="true"></i>{{ __('Fixed quote before any work starts') }}</li>
                <li><i class="fas fa-circle-check" aria-hidden="true"></i>{{ __('You own the code') }}</li>
                <li><i class="fas fa-circle-check" aria-hidden="true"></i>{{ __('Support after delivery') }}</li>
            </ul>
        </div>

        <div class="hero-visual reveal" style="--d:.1s">
            <div class="portrait">
                <picture>
                    <source srcset="{{ asset('images/me.webp') }}" type="image/webp">
                    <img src="{{ asset('images/me.jpeg') }}" alt="{{ $heroName }} — {{ $heroTagline }}" width="900" height="900" fetchpriority="high">
                </picture>
            </div>
            <div class="float-card fc-top">
                <span class="fc-icon"><i class="fas fa-rocket" aria-hidden="true"></i></span>
                <span><strong dir="ltr">{{ $stats[0][0] }}</strong><small>{{ $stats[0][1] }}</small></span>
            </div>
            <div class="float-card fc-bottom">
                <span class="fc-icon is-good"><i class="fas fa-shield-halved" aria-hidden="true"></i></span>
                <span><strong>{{ $heroName }}</strong><small>{{ $heroTagline }}</small></span>
            </div>
        </div>
    </div>

    <div class="wrap">
        <dl class="stats-band reveal">
            @foreach($stats as [$value, $label])
                <div><dt>{{ $label }}</dt><dd data-count dir="ltr">{{ $value }}</dd></div>
            @endforeach
            <div><dt>{{ __('Clients in') }}</dt><dd class="stat-text">{{ __('Palestine · KSA · Iraq') }}</dd></div>
        </dl>
    </div>
</section>

{{-- ═════════ INDUSTRIES ═════════ --}}
@if(!empty($industries))
<section class="industries" aria-label="{{ __('Industries I have built for') }}">
    <div class="wrap">
        <p>{{ __('Systems I have built for') }}</p>
        <ul>
            @foreach($industries as $industry)<li>{{ $industry }}</li>@endforeach
        </ul>
    </div>
</section>
@endif

{{-- ═════════ SERVICES ═════════ --}}
<section class="section" id="services" aria-labelledby="servicesTitle">
    <div class="wrap">
        <header class="section-head reveal">
            <p class="eyebrow">{{ __('Services') }}</p>
            <h2 id="servicesTitle">{{ __('What I can do for your business') }}</h2>
            <p class="section-sub">{{ __('Whether you are starting from an idea or already have a system, I make sure the technology behind your business is solid, fast, and ready to grow.') }}</p>
        </header>

        <div class="service-grid">
            @foreach($services as $service)
                @php
                    $deliverables = ($locale === 'ar' && ! empty($service->deliverables_ar)) ? $service->deliverables_ar : ($service->deliverables ?? []);
                @endphp
                <article class="service-card reveal" style="--d: {{ $loop->index * .08 }}s">
                    <span class="service-icon" aria-hidden="true"><i class="{{ $service->icon ?: $serviceIcons[$loop->index % 3] }}"></i></span>
                    <h3>{{ t($service, 'title') }}</h3>
                    <p>{{ t($service, 'summary') }}</p>
                    @if(!empty($deliverables))
                        <ul class="check-list">
                            @foreach($deliverables as $item)<li>{{ $item }}</li>@endforeach
                        </ul>
                    @endif
                    <a href="#contact" class="card-link" data-service="{{ t($service, 'title') }}">{{ __('Discuss this service') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
                </article>
            @endforeach
        </div>

        <div class="help-note reveal">
            <span class="help-icon" aria-hidden="true"><i class="fas fa-comments"></i></span>
            <div>
                <strong>{{ __('Not sure what you need?') }}</strong>
                <p>{{ __('Describe the problem in your own words — I’ll tell you honestly what it needs, even if it’s a small fix.') }}</p>
            </div>
            <a href="#contact" class="btn btn-ghost">{{ __('Ask me') }}</a>
        </div>
    </div>
</section>

{{-- ═════════ RESULTS / CASE STUDIES ═════════ --}}
<section class="section section-alt" id="projects" aria-labelledby="workTitle">
    <div class="wrap">
        <header class="section-head reveal">
            <p class="eyebrow">{{ __('Case studies') }}</p>
            <h2 id="workTitle">{{ __('Real problems, solved') }}</h2>
            <p class="section-sub">{{ __('Real systems I have built and improved — open any project to see the challenge, the approach, and the result.') }}</p>
        </header>

        @if($featured)
            @php
                $featuredImage = $featured->image ? project_image_url($featured->image) : null;
                $featuredResult = t($featured, 'result');
            @endphp
            <a href="{{ $projectUrl($featured->id) }}" class="case-featured reveal">
                <div class="case-media">
                    @if($featuredImage)
                        <img src="{{ $featuredImage }}" alt="" loading="lazy">
                    @else
                        <span class="case-emoji" aria-hidden="true">{{ $featured->icon ?: '🚀' }}</span>
                    @endif
                </div>
                <div class="case-body">
                    <div class="case-meta">
                        @if(t($featured, 'category'))<span class="tag">{{ t($featured, 'category') }}</span>@endif
                        @if($featured->featured)<span class="tag tag-accent"><i class="fas fa-star" aria-hidden="true"></i> {{ __('Featured') }}</span>@endif
                    </div>
                    <h3>{{ t($featured, 'title') }}</h3>
                    <p>{{ t($featured, 'description') }}</p>
                    @if($featuredResult)<p class="result"><i class="fas fa-arrow-trend-up" aria-hidden="true"></i><span><small>{{ __('Result') }}</small>{{ $featuredResult }}</span></p>@endif
                    @if(!empty($featured->stack))<ul class="stack">@foreach(array_slice($featured->stack, 0, 5) as $tech)<li>{{ $tech }}</li>@endforeach</ul>@endif
                    <span class="card-link">{{ __('Read the case study') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></span>
                </div>
            </a>
        @endif

        @if(!empty($moreProjects))
            <div class="case-grid">
                @foreach($moreProjects as $project)
                    @php
                        $image = $project->image ? project_image_url($project->image) : null;
                        $result = t($project, 'result');
                    @endphp
                    <a href="{{ $projectUrl($project->id) }}" class="case-card reveal" style="--d: {{ ($loop->index % 3) * .08 }}s">
                        <div class="case-media">
                            @if($image)
                                <img src="{{ $image }}" alt="" loading="lazy">
                            @else
                                <span class="case-emoji" aria-hidden="true">{{ $project->icon ?: '🚀' }}</span>
                            @endif
                        </div>
                        <div class="case-body">
                            @if(t($project, 'category'))<span class="tag">{{ t($project, 'category') }}</span>@endif
                            <h3>{{ t($project, 'title') }}</h3>
                            <p class="clamp-3">{{ t($project, 'description') }}</p>
                            @if($result)<p class="result is-compact"><i class="fas fa-arrow-trend-up" aria-hidden="true"></i><span>{{ $result }}</span></p>@endif
                            <span class="card-link">{{ __('Read the case study') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ═════════ TESTIMONIALS ═════════ --}}
@if(!empty($testimonials))
<section class="section" id="testimonials" aria-labelledby="testimonialsTitle">
    <div class="wrap">
        <header class="section-head reveal">
            <p class="eyebrow">{{ __('Testimonials') }}</p>
            <h2 id="testimonialsTitle">{{ __('What clients say') }}</h2>
        </header>
        <div class="quote-grid">
            @foreach($testimonials as $testimonial)
                <figure class="quote-card reveal" style="--d: {{ ($loop->index % 3) * .08 }}s">
                    <i class="fas fa-quote-left quote-mark" aria-hidden="true"></i>
                    <blockquote>{{ t($testimonial, 'quote') }}</blockquote>
                    <figcaption>
                        <span class="avatar" aria-hidden="true">{{ mb_substr($testimonial->name, 0, 1) }}</span>
                        <span>
                            <strong>{{ $testimonial->name }}</strong>
                            @if(t($testimonial, 'role'))<small>{{ t($testimonial, 'role') }}</small>@endif
                            @if($testimonial->project_id && $testimonial->project_title)
                                <a href="{{ $projectUrl($testimonial->project_id) }}">{{ $locale === 'ar' && $testimonial->project_title_ar ? $testimonial->project_title_ar : $testimonial->project_title }}</a>
                            @endif
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════ PROCESS ═════════ --}}
<section class="section {{ empty($testimonials) ? '' : 'section-alt' }}" id="process" aria-labelledby="processTitle">
    <div class="wrap">
        <header class="section-head reveal">
            <p class="eyebrow">{{ __('Process') }}</p>
            <h2 id="processTitle">{{ __('How we work together') }}</h2>
            <p class="section-sub">{{ __('A simple, transparent process — you always know what is happening and what comes next.') }}</p>
        </header>
        <ol class="steps-row">
            @foreach([
                ['fa-comments', __('Discovery'), __('We talk about your project, the problem, and the goal. No technical jargon needed.')],
                ['fa-clipboard-list', __('Audit & Plan'), __('I review the code or requirements and send a clear plan with scope, timeline, and price.')],
                ['fa-code', __('Build & Update'), __('I get to work and share regular progress updates, so there are no surprises.')],
                ['fa-circle-check', __('Deliver & Support'), __('You get tested, documented work — plus support after delivery to make sure everything runs smoothly.')],
            ] as [$icon, $title, $text])
                <li class="step reveal" style="--d: {{ $loop->index * .08 }}s">
                    <span class="step-num" aria-hidden="true"><i class="fas {{ $icon }}"></i></span>
                    <small>{{ __('Step') }} {{ $loop->iteration }}</small>
                    <h3>{{ $title }}</h3>
                    <p>{{ $text }}</p>
                </li>
            @endforeach
        </ol>
        <ul class="guarantees reveal">
            <li><i class="fas fa-file-signature" aria-hidden="true"></i>{{ __('Clear scope & price before starting') }}</li>
            <li><i class="fas fa-clock-rotate-left" aria-hidden="true"></i>{{ __('Regular progress updates') }}</li>
            <li><i class="fas fa-book-open" aria-hidden="true"></i>{{ __('Clean, documented code you own') }}</li>
            <li><i class="fas fa-life-ring" aria-hidden="true"></i>{{ __('Support after delivery') }}</li>
        </ul>
    </div>
</section>

{{-- ═════════ ABOUT ═════════ --}}
<section class="section {{ empty($testimonials) ? 'section-alt' : '' }}" id="about" aria-labelledby="aboutTitle">
    <div class="wrap about-grid">
        <div class="about-photo reveal">
            <picture>
                <source srcset="{{ asset('images/me.webp') }}" type="image/webp">
                <img src="{{ asset('images/me.jpeg') }}" alt="{{ $heroName }} — {{ $heroTagline }}" width="900" height="900" loading="lazy">
            </picture>
            <div class="about-badge"><i class="fas fa-location-dot" aria-hidden="true"></i> {{ __('Gaza, Palestine — working remotely worldwide') }}</div>
        </div>
        <div class="about-copy">
            <p class="eyebrow reveal">{{ __('Who Am I') }}</p>
            <h2 id="aboutTitle" class="reveal">{!! $aboutHeading ?: e($heroName) !!}</h2>
            @foreach($aboutParagraphs as $paragraph)
                <p class="reveal">{{ $paragraph }}</p>
            @endforeach
            <ul class="values reveal">
                @foreach([
                    ['fa-broom', __('Clean, maintainable code'), __('Code the next developer can read and extend.')],
                    ['fa-handshake', __('Honest communication'), __('Clear updates, realistic estimates, no surprises.')],
                    ['fa-bolt', __('Performance first'), __('Fast systems that stay fast as you grow.')],
                ] as [$icon, $valueTitle, $valueText])
                    <li><i class="fas {{ $icon }}" aria-hidden="true"></i><span><strong>{{ $valueTitle }}</strong>{{ $valueText }}</span></li>
                @endforeach
            </ul>
            <div class="about-actions reveal">
                @if($cvUrl)<a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="btn btn-primary" data-cv-open><i class="fas fa-file-lines" aria-hidden="true"></i> {{ __('View my CV') }}</a>@endif
                @if($linkedinUrl)<a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="icon-btn icon-btn-lg" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                @if($githubUrl)<a href="{{ $githubUrl }}" target="_blank" rel="noopener" class="icon-btn icon-btn-lg" aria-label="GitHub"><i class="fab fa-github"></i></a>@endif
            </div>
        </div>
    </div>

    @if(!empty($experiences) || !empty($categories))
    <div class="wrap exp-skills">
        @if(!empty($experiences))
        <div class="reveal" id="experience">
            <h3 class="sub-title"><i class="fas fa-briefcase" aria-hidden="true"></i> {{ __('Experience') }}</h3>
            <ol class="timeline">
                @foreach($experiences as $exp)
                    <li>
                        <span class="dates" dir="ltr">{{ $exp->date_range }}</span>
                        <strong>{{ t($exp, 'title') }}</strong>
                        <span class="company">{{ t($exp, 'company') }}</span>
                        @if(t($exp, 'description'))<p>{{ t($exp, 'description') }}</p>@endif
                    </li>
                @endforeach
            </ol>
        </div>
        @endif
        @if(!empty($categories) || !empty($techTags))
        <div class="reveal" id="skills" style="--d:.1s">
            <h3 class="sub-title"><i class="fas fa-layer-group" aria-hidden="true"></i> {{ __('Toolkit') }}</h3>
            @foreach($categories as $cat)
                <div class="skill-group">
                    <h4>{{ t($cat, 'name') }}</h4>
                    <ul class="chips">@foreach($cat->skills as $skill)<li>{{ t($skill, 'name') }}</li>@endforeach</ul>
                </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif
</section>

{{-- ═════════ FAQ ═════════ --}}
@if(!empty($faqs))
<section class="section" id="faq" aria-labelledby="faqTitle">
    <div class="wrap faq-grid">
        <header class="faq-side reveal">
            <p class="eyebrow">{{ __('FAQ') }}</p>
            <h2 id="faqTitle">{{ __('Questions clients usually ask') }}</h2>
            <p class="section-sub">{{ __('Can’t find your answer? Ask me directly — I reply personally.') }}</p>
            <div class="faq-cta">
                @if($whatsappNumber)<a class="btn btn-wa" href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode(__('Hello, I found you through your portfolio.')) }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i> {{ __('Ask on WhatsApp') }}</a>@endif
                <a class="btn btn-ghost" href="#contact">{{ __('Send a message') }}</a>
            </div>
        </header>
        <div class="faq-list reveal" style="--d:.08s">
            @foreach($faqs as $faq)
                <details class="faq-item" @if($loop->first) open @endif>
                    <summary>{{ t($faq, 'question') }}<span class="faq-icon" aria-hidden="true"></span></summary>
                    <div class="faq-answer"><p>{{ t($faq, 'answer') }}</p></div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════ CONTACT ═════════ --}}
<section class="section contact" id="contact" aria-labelledby="contactTitle">
    <div class="wrap">
        <div class="contact-card">
            <div class="contact-intro">
                <p class="eyebrow">{{ __('Contact') }}</p>
                <h2 id="contactTitle">{{ __('Let’s talk about your project') }}</h2>
                <p>{{ __('Tell me briefly what you need. You will get a clear answer on how I can help, how long it takes, and what it costs — no obligation.') }}</p>
                <ol class="contact-steps">
                    <li>{{ __('You send a short message about your project.') }}</li>
                    <li>{{ __('I reply with questions or a first recommendation.') }}</li>
                    <li>{{ __('You get a clear plan with scope, timeline, and price.') }}</li>
                </ol>
                <div class="direct">
                    @if($whatsappNumber)<a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i><span><small>WhatsApp</small><span dir="ltr">+{{ $whatsappNumber }}</span></span></a>@endif
                    @if($contactEmail)<a href="mailto:{{ $contactEmail }}"><i class="fas fa-envelope" aria-hidden="true"></i><span><small>{{ __('Email') }}</small><span>{{ $contactEmail }}</span></span></a>@endif
                    @if($cvUrl)<a href="{{ $cvUrl }}" target="_blank" rel="noopener" data-cv-open><i class="fas fa-file-lines" aria-hidden="true"></i><span><small>{{ __('Curriculum Vitae') }}</small><span>{{ __('View & download my CV') }}</span></span></a>@endif
                </div>
            </div>

            <div class="contact-form">
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
                    <p class="form-note"><i class="fas fa-lock" aria-hidden="true"></i> {{ __('I reply personally, usually within 1–2 days. You will also get a confirmation email.') }}</p>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
