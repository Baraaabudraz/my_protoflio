<?php

namespace App\Support;

/**
 * Describes the portfolio's content tables and how they are stored as seeder data files.
 *
 * Content is exported from the local database into database/seeders/data/<table>.php
 * (committed to Git) and seeded into other environments from there.
 */
class PortfolioContent
{
    /**
     * Content tables in insert order (parents before children), with their JSON columns.
     *
     * @var array<string, list<string>>
     */
    public const TABLES = [
        'settings' => [],
        'services' => ['deliverables', 'deliverables_ar'],
        'projects' => ['stack', 'work_stages', 'work_stages_ar'],
        'experiences' => ['tags'],
        'skill_categories' => [],
        'skills' => [],
    ];

    /**
     * Absolute path of the data file for a table.
     */
    public static function dataPath(string $table): string
    {
        return database_path("seeders/data/{$table}.php");
    }

    /**
     * Rows of a table as stored in its data file (JSON columns decoded), or null when no file exists.
     *
     * @return list<array<string, mixed>>|null
     */
    public static function load(string $table): ?array
    {
        $path = self::dataPath($table);

        return is_file($path) ? require $path : null;
    }

    /**
     * Prepare a stored row for insertion: JSON columns are encoded back to strings.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function toDatabaseRow(string $table, array $row): array
    {
        foreach (self::TABLES[$table] as $column) {
            if (array_key_exists($column, $row) && is_array($row[$column])) {
                $row[$column] = json_encode($row[$column], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        return $row;
    }

    /**
     * Prepare a database row for export: JSON columns are decoded to arrays for readable diffs.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function fromDatabaseRow(string $table, array $row): array
    {
        foreach (self::TABLES[$table] as $column) {
            if (is_string($row[$column] ?? null)) {
                $decoded = json_decode($row[$column], true);
                $row[$column] = is_array($decoded) ? $decoded : $row[$column];
            }
        }

        return $row;
    }

    /**
     * Render a value as short-array PHP source.
     */
    public static function toPhp(mixed $value, int $depth = 0): string
    {
        if ($value === null) {
            return 'null';
        }

        if (! is_array($value)) {
            return var_export($value, true);
        }

        if ($value === []) {
            return '[]';
        }

        $indent = str_repeat('    ', $depth + 1);
        $isList = array_is_list($value);
        $lines = array_map(
            fn ($key, $item) => $indent.($isList ? '' : var_export($key, true).' => ').self::toPhp($item, $depth + 1).',',
            array_keys($value),
            $value
        );

        return "[\n".implode("\n", $lines)."\n".str_repeat('    ', $depth).']';
    }
}
