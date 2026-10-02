<?php

namespace Database\Seeders;

use App\Support\PortfolioContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the portfolio content from database/seeders/data (see `php artisan portfolio:export`).
 *
 * Safe by default: empty tables are filled and missing settings are added, but existing
 * content is never touched. With $fresh = true, all content is replaced by the data files.
 */
class PortfolioSeeder extends Seeder
{
    public function run(bool $fresh = false): void
    {
        DB::transaction(function () use ($fresh) {
            if ($fresh) {
                // Children before parents so foreign keys are respected
                foreach (array_reverse(array_keys(PortfolioContent::TABLES)) as $table) {
                    DB::table($table)->delete();
                }
            }

            foreach (array_keys(PortfolioContent::TABLES) as $table) {
                $this->seedTable($table);
            }
        });
    }

    private function seedTable(string $table): void
    {
        $rows = PortfolioContent::load($table);

        if ($rows === null) {
            $this->command?->warn("  {$table}: no data file — run `php artisan portfolio:export` locally first");

            return;
        }

        $rows = array_map(fn (array $row): array => PortfolioContent::toDatabaseRow($table, $row), $rows);

        if ($table === 'settings') {
            // Add settings that don't exist yet; keep values already edited on this server.
            // Settings are identified by key, so ids are left to the database.
            $rows = array_map(fn (array $row): array => array_diff_key($row, ['id' => true]), $rows);
            $added = 0;
            foreach (array_chunk($rows, 50) as $chunk) {
                $added += DB::table('settings')->insertOrIgnore($chunk);
            }
            $this->command?->line("  settings: {$added} added");

            return;
        }

        if (DB::table($table)->exists()) {
            $this->command?->line("  {$table}: skipped (already has content)");

            return;
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table($table)->insert($chunk);
        }
        $this->command?->line("  {$table}: ".count($rows).' inserted');
    }
}
