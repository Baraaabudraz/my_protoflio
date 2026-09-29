<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Services\Database;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * XML sitemap with hreflang alternates for every public page.
     */
    public function sitemap(): Response
    {
        $projects = Database::query('SELECT id, updated_at FROM projects WHERE visible = 1 ORDER BY featured DESC, sort_order ASC');

        $homeLastModified = collect([
            Database::first('SELECT MAX(updated_at) AS d FROM settings')?->d,
            Database::first('SELECT MAX(updated_at) AS d FROM services')?->d,
            Database::first('SELECT MAX(updated_at) AS d FROM projects')?->d,
            Database::first('SELECT MAX(updated_at) AS d FROM experiences')?->d,
        ])->filter()->max();

        $pages = [['url' => url('/'), 'lastmod' => $homeLastModified, 'priority' => '1.0']];
        foreach ($projects as $project) {
            $pages[] = ['url' => route('project.show', $project->id), 'lastmod' => $project->updated_at, 'priority' => '0.8'];
        }

        return response()
            ->view('seo.sitemap', ['pages' => $pages, 'locales' => SetLocale::SUPPORTED], 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * robots.txt pointing crawlers at the sitemap. The CMS is not listed so its URL stays private; its pages carry noindex.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /lang/',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
