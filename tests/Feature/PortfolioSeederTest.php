<?php

namespace Tests\Feature;

use App\Support\PortfolioContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Runs against the in-memory SQLite database configured in phpunit.xml — never the live portfolio.sqlite.
 */
class PortfolioSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_fills_every_content_table_from_the_exported_data(): void
    {
        $this->artisan('portfolio:seed')->assertSuccessful();

        foreach (array_keys(PortfolioContent::TABLES) as $table) {
            $this->assertSame(count(PortfolioContent::load($table)), DB::table($table)->count(), "{$table} row count");
        }

        $project = PortfolioContent::load('projects')[0];
        $this->assertSame($project['title_ar'], DB::table('projects')->where('id', $project['id'])->value('title_ar'));
        $this->assertSame($project['stack'], json_decode(DB::table('projects')->where('id', $project['id'])->value('stack'), true));
    }

    public function test_safe_seed_keeps_content_edited_on_the_server_and_restores_missing_settings(): void
    {
        $this->artisan('portfolio:seed')->assertSuccessful();
        $project = PortfolioContent::load('projects')[0];
        DB::table('projects')->where('id', $project['id'])->update(['title' => 'Edited on server']);
        $setting = PortfolioContent::load('settings')[0];
        DB::table('settings')->where('key', $setting['key'])->delete();

        $this->artisan('portfolio:seed')->assertSuccessful();

        $this->assertSame('Edited on server', DB::table('projects')->where('id', $project['id'])->value('title'));
        $this->assertSame($setting['value'], DB::table('settings')->where('key', $setting['key'])->value('value'));
    }

    public function test_fresh_seed_replaces_all_content(): void
    {
        $this->artisan('portfolio:seed')->assertSuccessful();
        $project = PortfolioContent::load('projects')[0];
        DB::table('projects')->where('id', $project['id'])->update(['title' => 'Edited on server']);

        $this->artisan('portfolio:seed', ['--fresh' => true, '--force' => true])->assertSuccessful();

        $this->assertSame($project['title'], DB::table('projects')->where('id', $project['id'])->value('title'));
        $this->assertSame(count(PortfolioContent::load('projects')), DB::table('projects')->count());
    }

    public function test_fresh_seed_asks_for_confirmation(): void
    {
        $this->artisan('portfolio:seed')->assertSuccessful();

        $this->artisan('portfolio:seed', ['--fresh' => true])
            ->expectsConfirmation('This deletes all settings, services, projects, experience and skills on this server and replaces them with the exported data. Continue?', 'no')
            ->assertFailed();

        $this->assertSame(count(PortfolioContent::load('projects')), DB::table('projects')->count());
    }

    public function test_exported_data_round_trips_through_php_source(): void
    {
        $rows = PortfolioContent::load('projects');

        $this->assertSame($rows, eval('return '.PortfolioContent::toPhp($rows).';'));
    }
}
