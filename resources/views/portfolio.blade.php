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
    $statProjects = $settings['hero_stat_projects'] ?? '30+';
    $statClients = $settings['hero_stat_clients'] ?? '15+';
    $statYears = $settings['hero_stat_years'] ?? '4+';
    $projectUrl = fn ($id) => \App\Http\Middleware\SetLocale::localizedUrl(route('project.show', $id), $locale);

    // "Diagnose your system": each symptom maps to a service (by position) and the case study that best proves the fix.
    $findCase = function (string $pattern) use ($projects) {
        foreach ($projects as $p) {
            if (preg_match($pattern, (string) $p->result.' '.$p->description)) {
                return $p;
            }
        }

        return $projects[0] ?? null;
    };
    $serviceAt = fn (int $i) => $services[$i] ?? ($services[0] ?? null);
    $symptoms = [
        ['slow', 'fa-gauge-high', __('My system is slow'), __('Pages take forever, reports time out and users leave.'),
            [__('Measure exactly where the time goes'), __('Fix slow queries, add indexes and caching'), __('Prove it with before/after numbers')],
            $serviceAt(2), $findCase('/efficien|faster|speed|performance/i')],
        ['breaking', 'fa-triangle-exclamation', __('It keeps breaking'), __('Bugs, crashes or failed notifications keep coming back.'),
            [__('Find the root cause, not just the symptom'), __('Fix it, test it and add monitoring'), __('Stabilise the system so it stays fixed')],
            $serviceAt(1), $findCase('/stable|error/i')],
        ['outdated', 'fa-clock-rotate-left', __('It’s outdated or hard to change'), __('Old Laravel/PHP, messy code, and every change feels risky.'),
            [__('Review the code and plan safe upgrades'), __('Upgrade Laravel and PHP step by step'), __('Clean up the code so new features are easy')],
            $serviceAt(1), $findCase('/duplicat|maintain|code/i')],
        ['idea', 'fa-lightbulb', __('I have an idea to build'), __('A process on spreadsheets and WhatsApp, or a product that doesn’t exist yet.'),
            [__('Turn your idea into a clear plan and price'), __('Build it step by step with regular demos'), __('Launch, document and hand everything over')],
            $serviceAt(0), $projects[0] ?? null],
    ];

    // Hero chat: a client describes the problem in plain words, I answer, and a real project proves it.
    $chats = [
        ['slow', __('Slow website'), __('My website is so slow that customers leave before it even loads 😟'),
            __('I’ll find exactly what’s slowing it down, fix it, and show you the before/after numbers. I did the same for a recent client:'), $symptoms[0][6]],
        ['breaking', __('Keeps breaking'), __('Our app keeps breaking and customers complain every day.'),
            __('I’ll find the real cause, fix it properly and make sure it stays fixed. Here’s a similar case:'), $symptoms[1][6]],
        ['idea', __('New idea'), __('We still run everything on Excel and WhatsApp. Can you build us a proper system?'),
            __('Yes! I’ll turn it into a clear plan with a fixed price, then build it step by step with you. Like this one:'), $symptoms[3][6]],
    ];
@endphp

@section('content')

{{-- ═════════ HERO ═════════ --}}
<section class="hero" id="top" aria-labelledby="heroTitle">
    <div class="aurora" aria-hidden="true"><span></span><span></span><span></span></div>
    <div class="grid-bg" aria-hidden="true"></div>
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <p class="pill reveal"><span class="status-dot" aria-hidden="true"></span>{{ __('Available for new projects') }} <span class="pill-sep" aria-hidden="true"></span> {{ __('Replies within 1–2 days') }}</p>
            <h1 class="hero-title reveal" id="heroTitle" style="--d:.06s">
                {{ __('Your business runs on its system.') }}
                <span class="grad">{{ __('I make sure it never lets you down.') }}</span>
            </h1>
            <p class="lead reveal" style="--d:.14s">{!! $heroSubtitle !!}</p>
            <div class="hero-actions reveal" style="--d:.22s">
                <a href="#diagnose" class="btn btn-primary btn-lg"><i class="fas fa-stethoscope" aria-hidden="true"></i> {{ __('Diagnose my system') }}</a>
                <a href="#contact" class="btn btn-ghost btn-lg">{{ __('Get a free consultation') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
            </div>
            <dl class="hero-stats reveal" style="--d:.3s">
                <div><dd data-count dir="ltr">{{ $statProjects }}</dd><dt>{{ __('Projects delivered') }}</dt></div>
                <div><dd data-count dir="ltr">{{ $statClients }}</dd><dt>{{ __('Happy clients') }}</dt></div>
                <div><dd data-count dir="ltr">{{ $statYears }}</dd><dt>{{ __('Years of experience') }}</dt></div>
            </dl>
        </div>

        <figure class="hero-visual reveal" style="--d:.12s" aria-label="{{ __('How a conversation with me usually goes') }}">
            <div class="chat" data-chat>
                <div class="chat-head">
                    <span class="chat-avatar"><img src="{{ asset('images/me-thumb.webp') }}" alt="" width="40" height="40"><span class="chat-online" aria-hidden="true"></span></span>
                    <span class="chat-who"><strong>{{ $heroName }}</strong><small>{{ __('Online · replies within 1–2 days') }}</small></span>
                    <i class="fab fa-whatsapp chat-brand" aria-hidden="true"></i>
                </div>

                <div class="chat-body">
                    @foreach($chats as [$key, $tabLabel, $problem, $reply, $case])
                        <div class="chat-thread @if($loop->first) is-active @endif" id="chat-{{ $key }}" role="tabpanel" aria-labelledby="chat-tab-{{ $key }}">
                            <p class="bubble bubble-client"><span class="sr-only">{{ __('Client') }}: </span>{{ $problem }}</p>
                            <p class="bubble bubble-typing" aria-hidden="true"><i></i><i></i><i></i></p>
                            <p class="bubble bubble-me"><span class="sr-only">{{ $heroName }}: </span>{{ $reply }}</p>
                            @if($case)
                                <a class="bubble bubble-proof" href="{{ $projectUrl($case->id) }}">
                                    <span class="proof-media" aria-hidden="true">@if($case->image)<img src="{{ project_image_url($case->image) }}" alt="" loading="lazy">@else{{ $case->icon ?: '🚀' }}@endif</span>
                                    <span class="proof-text">
                                        <strong>{{ t($case, 'title') }}</strong>
                                        @if(t($case, 'result'))<span class="proof-result"><i class="fas fa-circle-check" aria-hidden="true"></i> {{ t($case, 'result') }}</span>@endif
                                    </span>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="chat-foot">
                    <div class="chat-tabs" role="tablist" aria-label="{{ __('Example problems') }}">
                        @foreach($chats as [$key, $tabLabel])
                            <button type="button" class="chat-tab @if($loop->first) is-active @endif" role="tab" id="chat-tab-{{ $key }}" aria-controls="chat-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" @unless($loop->first) tabindex="-1" @endunless>{{ $tabLabel }}</button>
                        @endforeach
                    </div>
                    <a class="chat-input" href="{{ $whatsappNumber ? 'https://wa.me/'.$whatsappNumber.'?text='.rawurlencode(__('Hello, I found you through your portfolio.')) : '#contact' }}" @if($whatsappNumber) target="_blank" rel="noopener" @endif>
                        <span>{{ __('Tell me about your problem…') }}</span>
                        <span class="chat-send" aria-hidden="true"><i class="fas fa-paper-plane"></i></span>
                    </a>
                </div>
            </div>
        </figure>
    </div>
</section>

{{-- ═════════ DIAGNOSE ═════════ --}}
<section class="section" id="diagnose" aria-labelledby="diagnoseTitle">
    <div class="wrap">
        <header class="section-head center reveal">
            <p class="eyebrow">{{ __('Free diagnosis') }}</p>
            <h2 id="diagnoseTitle">{{ __('What’s going on with your system?') }}</h2>
            <p class="section-sub">{{ __('Pick what sounds like you — see how I would fix it, and a real project where I already did.') }}</p>
        </header>

        <div class="diagnose reveal">
            <div class="symptoms" role="tablist" aria-label="{{ __('Choose a symptom') }}">
                @foreach($symptoms as [$key, $icon, $title, $text])
                    <button type="button" class="symptom @if($loop->first) is-active @endif" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" @unless($loop->first) tabindex="-1" @endunless>
                        <span class="symptom-icon" aria-hidden="true"><i class="fas {{ $icon }}"></i></span>
                        <span><strong>{{ $title }}</strong><small>{{ $text }}</small></span>
                    </button>
                @endforeach
            </div>

            @foreach($symptoms as [$key, $icon, $title, $text, $plan, $service, $case])
                <div class="diag-panel @if($loop->first) is-active @endif" role="tabpanel" id="panel-{{ $key }}" aria-labelledby="tab-{{ $key }}" tabindex="0">
                    <div class="diag-plan">
                        <p class="mono diag-label"><i class="fas fa-terminal" aria-hidden="true"></i> {{ __('Diagnosis & plan') }}</p>
                        <ol class="plan-steps">
                            @foreach($plan as $step)
                                <li style="--i: {{ $loop->index }}"><span class="n mono" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $step }}</li>
                            @endforeach
                        </ol>
                        @if($service)
                            <div class="diag-service">
                                <span class="mono">{{ __('Recommended service') }}</span>
                                <strong>{{ t($service, 'title') }}</strong>
                            </div>
                        @endif
                        <a href="#contact" class="btn btn-primary" @if($service) data-service="{{ t($service, 'title') }}" @endif data-message="{{ $title }}. ">{{ __('Fix this with me') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></a>
                    </div>
                    @if($case)
                        <a class="diag-case" href="{{ $projectUrl($case->id) }}">
                            <p class="mono diag-label"><i class="fas fa-circle-check" aria-hidden="true"></i> {{ __('Proof — a similar case') }}</p>
                            <div class="diag-case-media">
                                @if($case->image)<img src="{{ project_image_url($case->image) }}" alt="" loading="lazy">@else<span aria-hidden="true">{{ $case->icon ?: '🚀' }}</span>@endif
                            </div>
                            <strong>{{ t($case, 'title') }}</strong>
                            @if(t($case, 'result'))<span class="result-chip"><i class="fas fa-arrow-trend-up" aria-hidden="true"></i> {{ t($case, 'result') }}</span>@endif
                            <span class="card-link">{{ __('Read the case study') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></span>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

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
                <article class="glass-card service-card reveal" data-spotlight style="--d: {{ $loop->index * .08 }}s">
                    <div class="service-top">
                        <span class="service-icon" aria-hidden="true"><i class="{{ $service->icon ?: 'fa-solid fa-code' }}"></i></span>
                        <span class="mono service-num" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
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
    </div>
</section>

{{-- ═════════ RESULTS / CASE STUDIES ═════════ --}}
<section class="section" id="projects" aria-labelledby="workTitle">
    <div class="wrap">
        <header class="section-head reveal">
            <p class="eyebrow">{{ __('Case studies') }}</p>
            <h2 id="workTitle">{{ __('Real problems, solved') }}</h2>
            <p class="section-sub">{{ __('Real systems I have built and improved — open any project to see the challenge, the approach, and the result.') }}</p>
        </header>

        <div class="case-grid">
            @foreach($projects as $project)
                @php
                    $image = $project->image ? project_image_url($project->image) : null;
                    $result = t($project, 'result');
                @endphp
                <a href="{{ $projectUrl($project->id) }}" class="glass-card case-card reveal @if($loop->first) is-featured @endif" data-spotlight style="--d: {{ ($loop->index % 3) * .08 }}s">
                    <div class="case-media" style="view-transition-name: project-{{ $project->id }}">
                        @if($image)
                            <img src="{{ $image }}" alt="" loading="lazy">
                        @else
                            <span class="case-emoji" aria-hidden="true">{{ $project->icon ?: '🚀' }}</span>
                        @endif
                        @if(t($project, 'category'))<span class="tag case-tag">{{ t($project, 'category') }}</span>@endif
                    </div>
                    <div class="case-body">
                        <h3>{{ t($project, 'title') }}</h3>
                        <p class="clamp-3">{{ t($project, 'description') }}</p>
                        @if($result)<span class="result-chip"><i class="fas fa-arrow-trend-up" aria-hidden="true"></i> {{ $result }}</span>@endif
                        @if($loop->first && !empty($project->stack))<ul class="stack">@foreach(array_slice($project->stack, 0, 5) as $tech)<li>{{ $tech }}</li>@endforeach</ul>@endif
                        <span class="card-link">{{ __('Read the case study') }} <span class="arrow" aria-hidden="true">{{ $arrow }}</span></span>
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
        <header class="section-head center reveal">
            <p class="eyebrow">{{ __('Testimonials') }}</p>
            <h2 id="testimonialsTitle">{{ __('What clients say') }}</h2>
        </header>
        <div class="quote-grid">
            @foreach($testimonials as $testimonial)
                <figure class="glass-card quote-card reveal" data-spotlight style="--d: {{ ($loop->index % 3) * .08 }}s">
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

{{-- ═════════ PROCESS (deploy pipeline) ═════════ --}}
<section class="section" id="process" aria-labelledby="processTitle">
    <div class="wrap process-grid">
        <header class="section-head process-head reveal">
            <p class="eyebrow">{{ __('Process') }}</p>
            <h2 id="processTitle">{{ __('How we work together') }}</h2>
            <p class="section-sub">{{ __('A simple, transparent process — you always know what is happening and what comes next.') }}</p>
            <ul class="guarantees">
                <li><i class="fas fa-file-signature" aria-hidden="true"></i>{{ __('Clear scope & price before starting') }}</li>
                <li><i class="fas fa-clock-rotate-left" aria-hidden="true"></i>{{ __('Regular progress updates') }}</li>
                <li><i class="fas fa-book-open" aria-hidden="true"></i>{{ __('Clean, documented code you own') }}</li>
                <li><i class="fas fa-life-ring" aria-hidden="true"></i>{{ __('Support after delivery') }}</li>
            </ul>
        </header>
        <ol class="pipeline" data-pipeline>
            <span class="pipeline-line" aria-hidden="true"><span></span></span>
            @foreach([
                ['fa-comments', __('Discovery'), __('We talk about your project, the problem, and the goal. No technical jargon needed.')],
                ['fa-clipboard-list', __('Audit & Plan'), __('I review the code or requirements and send a clear plan with scope, timeline, and price.')],
                ['fa-code', __('Build & Update'), __('I get to work and share regular progress updates, so there are no surprises.')],
                ['fa-rocket', __('Deliver & Support'), __('You get tested, documented work — plus support after delivery to make sure everything runs smoothly.')],
            ] as [$icon, $title, $text])
                <li class="stage">
                    <span class="stage-node" aria-hidden="true"><i class="fas {{ $icon }}"></i></span>
                    <div class="glass-card stage-card">
                        <span class="mono stage-step">{{ __('Step') }} {{ $loop->iteration }}</span>
                        <h3>{{ $title }}</h3>
                        <p>{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ═════════ ABOUT ═════════ --}}
<section class="section" id="about" aria-labelledby="aboutTitle">
    <div class="wrap about-grid">
        <div class="about-visual reveal">
            <div class="frame">
                <span class="corner c1" aria-hidden="true"></span><span class="corner c2" aria-hidden="true"></span><span class="corner c3" aria-hidden="true"></span><span class="corner c4" aria-hidden="true"></span>
                <picture>
                    <source srcset="{{ asset('images/me.webp') }}" type="image/webp">
                    <img src="{{ asset('images/me.jpeg') }}" alt="{{ $heroName }} — {{ $heroTagline }}" width="900" height="900" loading="lazy">
                </picture>
            </div>
            <dl class="glass-card spec-sheet">
                <div><dt class="mono">{{ __('Role') }}</dt><dd>{{ $heroTagline }}</dd></div>
                <div><dt class="mono">{{ __('Based in') }}</dt><dd>{{ __('Gaza, Palestine — working remotely worldwide') }}</dd></div>
                <div><dt class="mono">{{ __('Experience') }}</dt><dd><span dir="ltr">{{ $statYears }}</span> {{ __('years') }} · <span dir="ltr">{{ $statProjects }}</span> {{ __('projects') }}</dd></div>
                <div><dt class="mono">{{ __('Languages') }}</dt><dd>{{ __('Arabic · English') }}</dd></div>
                @if(!empty($techTags))<div><dt class="mono">{{ __('Stack') }}</dt><dd dir="ltr">{{ implode(' · ', array_slice($techTags, 0, 4)) }}</dd></div>@endif
            </dl>
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
        <div class="glass-card panel reveal" id="experience">
            <h3 class="sub-title"><i class="fas fa-briefcase" aria-hidden="true"></i> {{ __('Experience') }}</h3>
            <ol class="timeline">
                @foreach($experiences as $exp)
                    <li>
                        <span class="dates mono" dir="ltr">{{ $exp->date_range }}</span>
                        <strong>{{ t($exp, 'title') }}</strong>
                        <span class="company">{{ t($exp, 'company') }}</span>
                        @if(t($exp, 'description'))<p>{{ t($exp, 'description') }}</p>@endif
                    </li>
                @endforeach
            </ol>
        </div>
        @endif
        @if(!empty($categories))
        <div class="glass-card panel reveal" id="skills" style="--d:.1s">
            <h3 class="sub-title"><i class="fas fa-layer-group" aria-hidden="true"></i> {{ __('Toolkit') }}</h3>
            @foreach($categories as $cat)
                <div class="skill-group">
                    <h4 class="mono">{{ t($cat, 'name') }}</h4>
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
                <details class="glass-card faq-item" @if($loop->first) open @endif>
                    <summary><span class="mono faq-n" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="faq-q">{{ t($faq, 'question') }}</span><span class="faq-icon" aria-hidden="true"></span></summary>
                    <div class="faq-answer"><p>{{ t($faq, 'answer') }}</p></div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════ CONTACT ═════════ --}}
<section class="section contact" id="contact" aria-labelledby="contactTitle">
    <div class="aurora is-soft" aria-hidden="true"><span></span><span></span></div>
    <div class="wrap contact-grid">
        <div class="contact-intro reveal">
            <p class="eyebrow">{{ __('Contact') }}</p>
            <h2 id="contactTitle">{{ __('Let’s talk about your project') }}</h2>
            <p class="section-sub">{{ __('Tell me briefly what you need. You will get a clear answer on how I can help, how long it takes, and what it costs — no obligation.') }}</p>
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

        <div class="terminal reveal" style="--d:.08s">
            <div class="console-bar">
                <span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="mono">new-project.request</span>
                <span class="console-status mono"><span class="status-dot" aria-hidden="true"></span>{{ __('Online') }}</span>
            </div>
            <div class="terminal-body">
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
