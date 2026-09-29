{{--
    SEO head tags shared by public pages.
    Expects: $seo = ['title', 'description', 'image', 'image_alt', 'type', 'json_ld' (array)], plus $locale.
--}}
@php
    $seoPageUrl = url()->current();
    $seoCanonical = \App\Http\Middleware\SetLocale::localizedUrl($seoPageUrl, $locale);
    $seoOgLocales = ['ar' => 'ar_AR', 'en' => 'en_US'];
    $seoImage = $seo['image'] ?? asset('images/og-image.jpg');
@endphp
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
<meta name="author" content="{{ $seo['author'] ?? '' }}">
<meta name="theme-color" content="#00759c">
<link rel="canonical" href="{{ $seoCanonical }}">
@foreach(\App\Http\Middleware\SetLocale::SUPPORTED as $seoAltLocale)
<link rel="alternate" hreflang="{{ $seoAltLocale }}" href="{{ \App\Http\Middleware\SetLocale::localizedUrl($seoPageUrl, $seoAltLocale) }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ \App\Http\Middleware\SetLocale::localizedUrl($seoPageUrl, \App\Http\Middleware\SetLocale::SUPPORTED[0]) }}">

<meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
<meta property="og:site_name" content="{{ $seo['site_name'] ?? $seo['title'] }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:locale" content="{{ $seoOgLocales[$locale] ?? 'en_US' }}">
@foreach($seoOgLocales as $seoOgKey => $seoOgLocale)
    @if($seoOgKey !== $locale)
<meta property="og:locale:alternate" content="{{ $seoOgLocale }}">
    @endif
@endforeach
<meta property="og:image" content="{{ $seoImage }}">
@if(!isset($seo['image']))
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/jpeg">
@endif
<meta property="og:image:alt" content="{{ $seo['image_alt'] ?? $seo['title'] }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

@if(!empty($seo['json_ld']))
<script type="application/ld+json">{!! json_encode($seo['json_ld'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endif
