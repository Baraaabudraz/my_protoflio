<?php

namespace Tests\Feature;

use App\Services\Database;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Runs against the in-memory test database, seeded with the exported portfolio content.
 */
class PortfolioCvTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected $seeder = PortfolioSeeder::class;

    public function test_home_offers_the_cv_when_one_is_configured(): void
    {
        $cvPath = Database::first("SELECT value FROM settings WHERE key = 'cv_path'")?->value;

        if (! $cvPath || ! file_exists(public_path($cvPath))) {
            $this->markTestSkipped('No CV configured.');
        }

        $this->get('/?lang=en')
            ->assertOk()
            ->assertSee('href="'.asset($cvPath).'"', false)
            ->assertSee('data-cv-open', false)
            ->assertSee('<dialog class="cv-modal" id="cvModal"', false)
            ->assertSee('download="'.basename($cvPath).'"', false)
            ->assertSee('View my CV');
    }

    public function test_cv_file_is_publicly_downloadable(): void
    {
        $cvPath = Database::first("SELECT value FROM settings WHERE key = 'cv_path'")?->value;

        if (! $cvPath || ! file_exists(public_path($cvPath))) {
            $this->markTestSkipped('No CV configured.');
        }

        $this->assertStringStartsWith('%PDF', file_get_contents(public_path($cvPath), length: 4));
    }
}
