{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach($pages as $page)
@foreach($locales as $pageLocale)
    <url>
        <loc>{{ \App\Http\Middleware\SetLocale::localizedUrl($page['url'], $pageLocale) }}</loc>
@if($page['lastmod'])
        <lastmod>{{ \Illuminate\Support\Carbon::parse($page['lastmod'])->toAtomString() }}</lastmod>
@endif
        <priority>{{ $page['priority'] }}</priority>
@foreach($locales as $alternateLocale)
        <xhtml:link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ \App\Http\Middleware\SetLocale::localizedUrl($page['url'], $alternateLocale) }}"/>
@endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ \App\Http\Middleware\SetLocale::localizedUrl($page['url'], $locales[0]) }}"/>
    </url>
@endforeach
@endforeach
</urlset>
