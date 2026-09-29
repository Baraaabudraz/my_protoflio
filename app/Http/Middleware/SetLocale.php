<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SetLocale
{
    /**
     * Locales the site is available in. The first one is the default and is served on clean URLs.
     *
     * @var list<string>
     */
    public const SUPPORTED = ['ar', 'en'];

    /**
     * Resolve the locale from the `?lang=` query (crawlable, used for SEO/hreflang), then the session.
     */
    public function handle(Request $request, Closure $next)
    {
        $queryLocale = $request->query('lang');

        if (is_string($queryLocale) && in_array($queryLocale, self::SUPPORTED, true)) {
            session(['locale' => $queryLocale]);
            $locale = $queryLocale;
        } else {
            $locale = session('locale', config('app.locale', self::SUPPORTED[0]));
        }

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::SUPPORTED[0];
        }

        app()->setLocale($locale);

        View::share('locale', $locale);
        View::share('isRtl', $locale === 'ar');

        return $next($request);
    }

    /**
     * The canonical URL of a page in the given locale: the default locale uses the clean URL.
     */
    public static function localizedUrl(string $url, string $locale): string
    {
        if (trim((string) parse_url($url, PHP_URL_PATH), '/') === '') {
            $url = rtrim($url, '/').'/';
        }

        return $locale === self::SUPPORTED[0] ? $url : $url.'?lang='.$locale;
    }
}
