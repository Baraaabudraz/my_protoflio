<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = ['en', 'ar'];
        $locale    = session('locale', config('app.locale', 'ar'));

        if (!in_array($locale, $supported)) {
            $locale = 'ar';
        }

        app()->setLocale($locale);

        View::share('locale', $locale);
        View::share('isRtl', $locale === 'ar');

        return $next($request);
    }
}
