<?php

namespace Tests\Feature;

use App\Services\Database;
use Tests\TestCase;

/**
 * Read-only checks against the portfolio SQLite database used by App\Services\Database.
 */
class SeoTest extends TestCase
{
    public function test_home_defaults_to_arabic_with_canonical_and_hreflang_alternates(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('<html lang="ar" dir="rtl"', false)
            ->assertSee('<link rel="canonical" href="'.url('/').'/">', false)
            ->assertSee('<link rel="alternate" hreflang="ar" href="'.url('/').'/">', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="'.url('/').'/?lang=en">', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/').'/">', false);
    }

    public function test_lang_query_serves_english_with_its_own_canonical(): void
    {
        $response = $this->get('/?lang=en');

        $response->assertOk()
            ->assertSee('<html lang="en" dir="ltr"', false)
            ->assertSee('<link rel="canonical" href="'.url('/').'/?lang=en">', false)
            ->assertSee('<meta property="og:locale" content="en_US">', false)
            ->assertSessionHas('locale', 'en');
    }

    public function test_unsupported_lang_query_is_ignored(): void
    {
        $this->get('/?lang=fr')->assertOk()->assertSee('<html lang="ar"', false);
    }

    public function test_home_has_social_meta_and_structured_data(): void
    {
        $response = $this->get('/?lang=en');

        $response->assertSee('<meta property="og:image" content="'.asset('images/og-image.jpg').'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<script type="application/ld+json">', false);

        $jsonLd = $this->extractJsonLd($response->getContent());
        $types = array_column($jsonLd['@graph'], '@type');

        $this->assertContains('Person', $types);
        $this->assertContains('WebSite', $types);
        $this->assertContains('ProfessionalService', $types);
    }

    public function test_project_page_has_localized_seo_and_breadcrumbs(): void
    {
        $project = Database::first('SELECT id, title FROM projects WHERE visible = 1 ORDER BY id LIMIT 1');

        if (! $project) {
            $this->markTestSkipped('No visible projects in the database.');
        }

        $url = route('project.show', $project->id);
        $response = $this->get($url.'?lang=en');

        $response->assertOk()
            ->assertSee('<link rel="canonical" href="'.$url.'?lang=en">', false)
            ->assertSee('<meta property="og:type" content="article">', false);

        $types = array_column($this->extractJsonLd($response->getContent())['@graph'], '@type');
        $this->assertContains('CreativeWork', $types);
        $this->assertContains('BreadcrumbList', $types);
    }

    public function test_sitemap_lists_every_page_in_both_languages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);

        $locations = array_map('strval', iterator_to_array($xml->xpath('//*[local-name()="loc"]'), false));
        $this->assertContains(url('/').'/', $locations);
        $this->assertContains(url('/').'/?lang=en', $locations);

        $projectCount = count(Database::query('SELECT id FROM projects WHERE visible = 1'));
        $this->assertCount(($projectCount + 1) * 2, $locations);
    }

    public function test_robots_txt_points_to_sitemap_without_revealing_the_admin_url(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertDontSee('admin')
            ->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_admin_login_is_not_indexable(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    /**
     * @return array<string, mixed>
     */
    private function extractJsonLd(string $html): array
    {
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $match);

        return json_decode($match[1] ?? '', true, flags: JSON_THROW_ON_ERROR);
    }
}
