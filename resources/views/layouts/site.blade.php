@php
    $heroName = ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? 'Portfolio');
    $heroTagline = ts($settings, 'hero_tagline') ?: ($settings['hero_tagline'] ?? '');
    $firstName = explode(' ', $heroName)[0];
    $whatsappNumber = preg_replace('/\D+/', '', $settings['whatsapp_number'] ?? '');
    $contactEmail = $settings['email'] ?? '';
    $githubUrl = $settings['github_url'] ?? '';
    $linkedinUrl = $settings['linkedin_url'] ?? '';
    $cvPath = $settings['cv_path'] ?? '';
    $cvUrl = ($cvPath !== '' && file_exists(public_path($cvPath))) ? asset($cvPath) : null;
    $onHome = request()->routeIs('home');
    $homeUrl = \App\Http\Middleware\SetLocale::localizedUrl(url('/'), $locale);
    $anchor = fn (string $id) => ($onHome ? '' : $homeUrl).'#'.$id;
    $navItems = [
        ['diagnose', __('Diagnose')],
        ['services', __('Services')],
        ['projects', __('Results')],
        ['process', __('Process')],
        ['about', __('About')],
    ];
    if (! isset($faqs) || ! empty($faqs)) {
        $navItems[] = ['faq', __('FAQ')];
    }
    $otherLocale = $locale === 'ar' ? 'en' : 'ar';
    $switchUrl = url()->current().'?lang='.$otherLocale;
    $asset = fn (string $path) => asset($path).'?v='.(@filemtime(public_path($path)) ?: 1);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo')
    <script>
        (function () {
            var root = document.documentElement, saved = null;
            root.classList.add('js');
            try { saved = localStorage.getItem('theme'); } catch (e) {}
            var themes = ['light', 'dark', 'ocean', 'sunset'];
            root.setAttribute('data-theme', themes.indexOf(saved) > -1 ? saved : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @if($isRtl)
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Noto+Naskh+Arabic:wght@500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ $asset('css/site.css') }}">
    @stack('head')
</head>
<body>

<a href="#main" class="skip-link">{{ __('Skip to content') }}</a>

<header class="site-header">
    <div class="wrap header-inner">
        <a class="brand" href="{{ $homeUrl }}" aria-label="{{ $heroName }}">
            <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="36" height="36">
            <span class="brand-text"><strong>{{ $heroName }}</strong><small>{{ $heroTagline }}</small></span>
        </a>
        <nav class="nav" aria-label="{{ __('Main navigation') }}">
            @foreach($navItems as [$id, $label])
                <a href="{{ $anchor($id) }}">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="header-tools">
            <a class="icon-btn" href="{{ $switchUrl }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}" aria-label="{{ $locale === 'ar' ? 'English' : 'العربية' }}"><span class="lang-mark">{{ $locale === 'ar' ? 'EN' : 'ع' }}</span></a>
            <div class="theme-switch">
                <button type="button" class="icon-btn" id="themeToggle" aria-haspopup="true" aria-expanded="false" aria-controls="themeMenu" aria-label="{{ __('Theme') }}"><i class="fas fa-circle-half-stroke" aria-hidden="true"></i></button>
                <div class="theme-menu" id="themeMenu" role="menu">
                    @foreach(['light' => __('Light'), 'dark' => __('Dark'), 'ocean' => __('Ocean'), 'sunset' => __('Sunset')] as $themeKey => $themeLabel)
                        <button type="button" class="theme-option" role="menuitemradio" aria-checked="false" data-theme-choice="{{ $themeKey }}"><span class="swatch sw-{{ $themeKey }}" aria-hidden="true"></span>{{ $themeLabel }}</button>
                    @endforeach
                </div>
            </div>
            <a class="btn btn-primary btn-sm header-cta" href="{{ $anchor('contact') }}">{{ __('Free consultation') }}</a>
            <button type="button" class="icon-btn menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="menuPanel" aria-label="{{ __('Menu') }}"><i class="fas fa-bars" aria-hidden="true"></i></button>
        </div>
    </div>
    <div class="menu-panel" id="menuPanel" hidden>
        <nav class="wrap" aria-label="{{ __('Main navigation') }}">
            @foreach($navItems as [$id, $label])
                <a href="{{ $anchor($id) }}">{{ $label }} <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i></a>
            @endforeach
            <a class="btn btn-primary" href="{{ $anchor('contact') }}">{{ __('Get a free consultation') }}</a>
        </nav>
    </div>
</header>

<main id="main">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="wrap footer-grid">
        <div class="footer-brand">
            <a class="brand" href="{{ $homeUrl }}">
                <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="36" height="36" loading="lazy">
                <span class="brand-text"><strong>{{ $heroName }}</strong><small>{{ $heroTagline }}</small></span>
            </a>
            <p>{{ __('I build and fix Laravel systems for businesses — secure, fast and easy to grow.') }}</p>
            <div class="footer-social">
                @if($linkedinUrl)<a class="icon-btn" href="{{ $linkedinUrl }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                @if($githubUrl)<a class="icon-btn" href="{{ $githubUrl }}" target="_blank" rel="noopener" aria-label="GitHub"><i class="fab fa-github"></i></a>@endif
                @if($whatsappNumber)<a class="icon-btn" href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>@endif
            </div>
        </div>
        <div>
            <h2 class="footer-title">{{ __('Explore') }}</h2>
            <ul>
                @foreach($navItems as [$id, $label])
                    <li><a href="{{ $anchor($id) }}">{{ $label }}</a></li>
                @endforeach
                @if($cvUrl)<li><a href="{{ $cvUrl }}" target="_blank" rel="noopener" data-cv-open>{{ __('CV') }}</a></li>@endif
            </ul>
        </div>
        <div>
            <h2 class="footer-title">{{ __('Get in touch') }}</h2>
            <ul>
                @if($contactEmail)<li><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></li>@endif
                @if($whatsappNumber)<li><a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" dir="ltr">+{{ $whatsappNumber }}</a></li>@endif
                <li>{{ __('Gaza, Palestine — working remotely worldwide') }}</li>
            </ul>
            <a class="btn btn-primary btn-sm" href="{{ $anchor('contact') }}" style="margin-top:1rem">{{ __('Free consultation') }}</a>
        </div>
    </div>
    <div class="wrap footer-bottom">
        <span>© {{ date('Y') }} {{ $heroName }}</span>
        <span>{{ ts($settings, 'footer_text') ?: ($settings['footer_text'] ?? '') }}</span>
    </div>
</footer>

{{-- Sticky consultation bar --}}
<aside class="consult-bar" id="consultBar" aria-label="{{ __('Free consultation') }}" hidden>
    <p><span class="status-dot" aria-hidden="true"></span><span><strong>{{ __('Have a project in mind?') }}</strong> <span class="consult-sub">{{ __('Get a free consultation — no obligation.') }}</span></span></p>
    <div class="consult-actions">
        <a class="btn btn-primary btn-sm" href="{{ $anchor('contact') }}">{{ __('Free consultation') }}</a>
        @if($whatsappNumber)<a class="btn btn-wa btn-sm" href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode(__('Hello, I found you through your portfolio.')) }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp" aria-hidden="true"></i><span class="wa-label">WhatsApp</span></a>@endif
        <button type="button" class="consult-close" id="consultClose" aria-label="{{ __('Close') }}"><i class="fas fa-xmark" aria-hidden="true"></i></button>
    </div>
</aside>

@if($cvUrl)
<dialog class="cv-modal" id="cvModal" aria-labelledby="cvModalTitle">
    <div class="cv-modal-head">
        <div>
            <h2 id="cvModalTitle">{{ __('Curriculum Vitae') }}</h2>
            <p class="muted">{{ $heroName }} · {{ $heroTagline }}</p>
        </div>
        <div class="cv-modal-actions">
            <a href="{{ $cvUrl }}" download="{{ basename($cvPath) }}" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> {{ __('Download') }}</a>
            <a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="icon-btn" aria-label="{{ __('Open in new tab') }}"><i class="fas fa-up-right-from-square"></i></a>
            <button type="button" class="icon-btn" data-cv-close autofocus aria-label="{{ __('Close') }}"><i class="fas fa-xmark"></i></button>
        </div>
    </div>
    <div class="cv-modal-body"><iframe title="{{ __('Curriculum Vitae') }} — {{ $heroName }}" data-src="{{ $cvUrl }}#view=FitH"></iframe></div>
</dialog>
@endif

@stack('dialogs')

<script type="application/json" id="site-i18n">{!! json_encode([
    'sending' => __('Sending…'),
    'send' => __('Send Message'),
    'checkFields' => __('Please check the highlighted fields.'),
    'network' => __('Your message could not be sent. Please check your connection, or contact me on WhatsApp.'),
    'greeting' => __('Hello, I found you through your portfolio.'),
    'name' => __('Name'),
    'service' => __('Service'),
    'budget' => __('Budget'),
    'message' => __('Message'),
    'copied' => __('Link copied!'),
    'copy' => __('Copy link'),
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
<script src="{{ $asset('js/site.js') }}" defer></script>
@include('partials.whatsapp-sticker')
</body>
</html>
