<?php

namespace App\Services;

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Str;

class SeoBuilder
{
    /**
     * SEO data for the portfolio home page.
     *
     * @param  array<string, string>  $settings
     * @param  array<int, object>  $services
     * @param  array<int, object>  $faqs
     * @return array{title: string, description: string, author: string, site_name: string, image_alt: string, type: string, json_ld: array<string, mixed>}
     */
    public function home(array $settings, array $services, array $faqs = []): array
    {
        $name = $this->name($settings);
        $tagline = ts($settings, 'hero_tagline') ?: 'Backend Engineer';
        $title = ts($settings, 'seo_title') ?: $name.' — '.$tagline;
        $description = $this->limit(ts($settings, 'seo_description') ?: ts($settings, 'hero_subtitle'));
        $homeUrl = SetLocale::localizedUrl(url('/'), SetLocale::SUPPORTED[0]);
        $pageUrl = SetLocale::localizedUrl($homeUrl, app()->getLocale());

        $offers = array_map(fn (object $service): array => [
            '@type' => 'Offer',
            'itemOffered' => [
                '@type' => 'Service',
                'name' => t($service, 'title'),
                'description' => t($service, 'summary'),
                'provider' => ['@id' => $homeUrl.'#person'],
            ],
        ], $services);

        return [
            'title' => $title,
            'description' => $description,
            'author' => $name,
            'site_name' => $name,
            'image_alt' => $name.' — '.$tagline,
            'type' => 'profile',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'WebSite',
                        '@id' => $homeUrl.'#website',
                        'url' => $homeUrl,
                        'name' => $name,
                        'inLanguage' => SetLocale::SUPPORTED,
                        'publisher' => ['@id' => $homeUrl.'#person'],
                    ],
                    [
                        '@type' => 'ProfilePage',
                        '@id' => $pageUrl.'#webpage',
                        'url' => $pageUrl,
                        'name' => $title,
                        'description' => $description,
                        'inLanguage' => app()->getLocale(),
                        'isPartOf' => ['@id' => $homeUrl.'#website'],
                        'mainEntity' => ['@id' => $homeUrl.'#person'],
                        'primaryImageOfPage' => asset('images/me.jpeg'),
                    ],
                    $this->person($settings),
                    [
                        '@type' => 'ProfessionalService',
                        '@id' => $homeUrl.'#service',
                        'name' => $name.' — '.$tagline,
                        'url' => $homeUrl,
                        'image' => asset('images/og-image.jpg'),
                        'description' => $description,
                        'areaServed' => 'Worldwide',
                        'founder' => ['@id' => $homeUrl.'#person'],
                        'hasOfferCatalog' => [
                            '@type' => 'OfferCatalog',
                            'name' => __('Services'),
                            'itemListElement' => $offers,
                        ],
                    ],
                    ...($faqs === [] ? [] : [[
                        '@type' => 'FAQPage',
                        '@id' => $pageUrl.'#faq',
                        'inLanguage' => app()->getLocale(),
                        'mainEntity' => array_map(fn (object $faq): array => [
                            '@type' => 'Question',
                            'name' => t($faq, 'question'),
                            'acceptedAnswer' => ['@type' => 'Answer', 'text' => t($faq, 'answer')],
                        ], $faqs),
                    ]]),
                ],
            ],
        ];
    }

    /**
     * SEO data for a single project page.
     *
     * @param  array<string, string>  $settings
     * @return array{title: string, description: string, author: string, site_name: string, image?: string, image_alt: string, type: string, json_ld: array<string, mixed>}
     */
    public function project(object $project, array $settings): array
    {
        $name = $this->name($settings);
        $projectTitle = t($project, 'title');
        $description = $this->limit(t($project, 'overview') ?: t($project, 'description'));
        $homeUrl = SetLocale::localizedUrl(url('/'), SetLocale::SUPPORTED[0]);
        $pageUrl = SetLocale::localizedUrl(url()->current(), app()->getLocale());
        $image = $project->image ? project_image_url($project->image) : null;
        $images = array_values(array_unique(array_filter(array_merge(
            [$image],
            array_map(fn (object $galleryImage): string => asset('uploads/'.$galleryImage->path), (array) ($project->gallery ?? []))
        ))));

        $seo = [
            'title' => $projectTitle.' — '.$name,
            'description' => $description,
            'author' => $name,
            'site_name' => $name,
            'image_alt' => $projectTitle,
            'type' => 'article',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    array_filter([
                        '@type' => 'CreativeWork',
                        '@id' => $pageUrl.'#project',
                        'name' => $projectTitle,
                        'description' => $description,
                        'url' => $pageUrl,
                        'image' => count($images) > 1 ? $images : ($images[0] ?? null),
                        'inLanguage' => app()->getLocale(),
                        'genre' => t($project, 'category') ?: null,
                        'keywords' => implode(', ', (array) ($project->stack ?? [])) ?: null,
                        'dateCreated' => $project->created_at ?? null,
                        'dateModified' => $project->updated_at ?? null,
                        'author' => ['@type' => 'Person', 'name' => $name, 'url' => $homeUrl],
                    ]),
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => $name, 'item' => SetLocale::localizedUrl($homeUrl, app()->getLocale())],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => __('Projects'), 'item' => SetLocale::localizedUrl($homeUrl, app()->getLocale()).'#projects'],
                            ['@type' => 'ListItem', 'position' => 3, 'name' => $projectTitle, 'item' => $pageUrl],
                        ],
                    ],
                ],
            ],
        ];

        if ($image) {
            $seo['image'] = $image;
        }

        return $seo;
    }

    /**
     * Schema.org Person describing the portfolio owner.
     *
     * @param  array<string, string>  $settings
     * @return array<string, mixed>
     */
    public function person(array $settings): array
    {
        $homeUrl = SetLocale::localizedUrl(url('/'), SetLocale::SUPPORTED[0]);
        $skills = array_values(array_filter(array_map('trim', explode(',', $settings['about_tags'] ?? ''))));

        return array_filter([
            '@type' => 'Person',
            '@id' => $homeUrl.'#person',
            'name' => $settings['hero_name'] ?? '',
            'alternateName' => ($settings['hero_name_ar'] ?? '') ?: null,
            'jobTitle' => ts($settings, 'hero_tagline') ?: null,
            'description' => strip_tags(ts($settings, 'about_p1')) ?: null,
            'url' => $homeUrl,
            'image' => asset('images/me.jpeg'),
            'email' => ! empty($settings['email']) ? 'mailto:'.$settings['email'] : null,
            'sameAs' => $this->profileLinks($settings) ?: null,
            'knowsAbout' => $skills ?: null,
            'knowsLanguage' => ['ar', 'en'],
        ]);
    }

    /**
     * Social profile URLs that point to an actual profile (not a bare site homepage).
     *
     * @param  array<string, string>  $settings
     * @return list<string>
     */
    public function profileLinks(array $settings): array
    {
        return array_values(array_filter(
            [$settings['github_url'] ?? '', $settings['linkedin_url'] ?? ''],
            fn (string $url): bool => filter_var($url, FILTER_VALIDATE_URL) && trim((string) parse_url($url, PHP_URL_PATH), '/') !== ''
        ));
    }

    /**
     * @param  array<string, string>  $settings
     */
    private function name(array $settings): string
    {
        return ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? config('app.name'));
    }

    private function limit(string $text): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($text))), 160);
    }
}
