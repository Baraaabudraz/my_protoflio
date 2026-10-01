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
        ['services', __('Services')],
        ['projects', __('My Work')],
        ['process', __('Process')],
        ['about', __('About')],
        ['contact', __('Contact')],
    ];
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
            var saved = null;
            try { saved = localStorage.getItem('theme'); } catch (e) {}
            var themes = ['light', 'dark', 'ocean', 'sunset'];
            var theme = themes.indexOf(saved) > -1 ? saved : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @if($isRtl)
        <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ $asset('css/site.css') }}">
    @stack('head')
</head>
<body class="@yield('body-class')">

<a href="#main" class="skip-link">{{ __('Skip to content') }}</a>
<div class="progress-bar" aria-hidden="true"></div>
<div class="cursor" aria-hidden="true"><span class="cursor-dot"></span><span class="cursor-label"></span></div>

<header class="site-header">
    <div class="wrap">
        <a class="brand" href="{{ $homeUrl }}" aria-label="{{ $heroName }}" data-magnetic>
            <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="34" height="34">
            <span>{{ $firstName }}<span class="dot">.</span></span>
        </a>
        <nav class="nav" aria-label="{{ __('Main navigation') }}">
            @foreach($navItems as [$id, $label])
                <a class="ulink" href="{{ $anchor($id) }}">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="header-tools">
            <a class="tool-btn" href="{{ url()->current() }}?lang={{ $locale === 'ar' ? 'en' : 'ar' }}" hreflang="{{ $locale === 'ar' ? 'en' : 'ar' }}" lang="{{ $locale === 'ar' ? 'en' : 'ar' }}" aria-label="{{ $locale === 'ar' ? 'English' : 'العربية' }}">{{ $locale === 'ar' ? 'EN' : 'ع' }}</a>
            <div class="theme-switch">
                <button type="button" class="tool-btn" id="themeToggle" aria-haspopup="true" aria-expanded="false" aria-controls="themeMenu" aria-label="{{ __('Theme') }}"><i class="fas fa-circle-half-stroke"></i></button>
                <div class="theme-menu" id="themeMenu" role="menu">
                    @foreach(['light' => __('Paper'), 'dark' => __('Ink'), 'ocean' => __('Ocean'), 'sunset' => __('Sunset')] as $themeKey => $themeLabel)
                        <button type="button" class="theme-option" role="menuitemradio" aria-checked="false" data-theme-choice="{{ $themeKey }}"><span class="swatch sw-{{ $themeKey }}"></span>{{ $themeLabel }}</button>
                    @endforeach
                </div>
            </div>
            <a class="btn btn-accent header-cta" href="{{ $anchor('contact') }}" data-magnetic>{{ __('Let’s talk') }}</a>
            <button type="button" class="tool-btn menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="menuOverlay"><span class="label">{{ __('Menu') }}</span></button>
        </div>
    </div>
</header>

<div class="menu-overlay" id="menuOverlay">
    <nav aria-label="{{ __('Main navigation') }}">
        @foreach($navItems as [$id, $label])
            <a href="{{ $anchor($id) }}"><span style="--i: {{ $loop->index }}">{{ $label }}</span></a>
        @endforeach
    </nav>
    <div class="menu-foot">
        <a class="tool-btn" href="{{ url()->current() }}?lang={{ $locale === 'ar' ? 'en' : 'ar' }}" lang="{{ $locale === 'ar' ? 'en' : 'ar' }}">{{ $locale === 'ar' ? 'English' : 'العربية' }}</a>
        <button type="button" class="tool-btn" data-theme-cycle><i class="fas fa-circle-half-stroke"></i>&nbsp; {{ __('Theme') }}</button>
        @if($whatsappNumber)<a class="tool-btn" href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i>&nbsp; WhatsApp</a>@endif
    </div>
</div>

<main id="main">
    @yield('content')
</main>

<footer class="site-footer @yield('footer-class')">
    <div class="wrap">
        <div class="footer-top">
            <div>
                <h4>{{ __('Navigate') }}</h4>
                <ul>
                    @foreach($navItems as [$id, $label])
                        <li><a class="ulink" href="{{ $anchor($id) }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4>{{ __('Elsewhere') }}</h4>
                <ul>
                    @if($linkedinUrl)<li><a class="ulink" href="{{ $linkedinUrl }}" target="_blank" rel="noopener">LinkedIn</a></li>@endif
                    @if($githubUrl)<li><a class="ulink" href="{{ $githubUrl }}" target="_blank" rel="noopener">GitHub</a></li>@endif
                    @if($whatsappNumber)<li><a class="ulink" href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener">WhatsApp</a></li>@endif
                    @if($cvUrl)<li><a class="ulink" href="{{ $cvUrl }}" target="_blank" rel="noopener" data-cv-open>{{ __('CV') }}</a></li>@endif
                </ul>
            </div>
            <div>
                <h4>{{ __('Say hello') }}</h4>
                <ul>
                    @if($contactEmail)<li><a class="ulink" href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></li>@endif
                    @if($whatsappNumber)<li><a class="ulink" href="https://wa.me/{{ $whatsappNumber }}" dir="ltr">+{{ $whatsappNumber }}</a></li>@endif
                </ul>
            </div>
            <div>
                <h4>{{ __('Local time') }}</h4>
                <p><span data-clock="Asia/Gaza" dir="ltr">--:--</span> · {{ __('Gaza, Palestine') }}</p>
                <p style="margin-top:.8rem"><a class="ulink" href="#main">{{ __('Back to top') }} ↑</a></p>
            </div>
        </div>
        <span class="wordmark" aria-hidden="true">{{ $firstName }}<span class="dot">.</span></span>
    </div>
    <div class="wrap footer-bottom">
        <span>© {{ date('Y') }} {{ $heroName }}</span>
        <span>{{ ts($settings, 'footer_text') ?: ($settings['footer_text'] ?? '') }}</span>
    </div>
</footer>

@if($cvUrl)
<dialog class="cv-modal" id="cvModal" aria-labelledby="cvModalTitle">
    <div class="cv-modal-head">
        <div>
            <h2 id="cvModalTitle">{{ __('Curriculum Vitae') }}</h2>
            <p class="muted" style="font-size:.88rem">{{ $heroName }} · {{ $heroTagline }}</p>
        </div>
        <div class="cv-modal-actions">
            <a href="{{ $cvUrl }}" download="{{ basename($cvPath) }}" class="btn btn-accent"><i class="fas fa-download"></i> {{ __('Download') }}</a>
            <a href="{{ $cvUrl }}" target="_blank" rel="noopener" class="icon-btn" aria-label="{{ __('Open in new tab') }}"><i class="fas fa-up-right-from-square"></i></a>
            <button type="button" class="icon-btn" data-cv-close autofocus aria-label="{{ __('Close') }}"><i class="fas fa-xmark"></i></button>
        </div>
    </div>
    <div class="cv-modal-body"><iframe title="{{ __('Curriculum Vitae') }} — {{ $heroName }}" data-src="{{ $cvUrl }}#view=FitH"></iframe></div>
</dialog>
@endif

@stack('dialogs')

<script type="application/json" id="site-i18n">{!! json_encode([
    'menu' => __('Menu'),
    'close' => __('Close'),
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
