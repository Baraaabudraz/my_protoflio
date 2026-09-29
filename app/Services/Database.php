<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PDO;

/**
 * Thin raw-SQL helper over Laravel's SQLite connection (database/portfolio.sqlite).
 *
 * Sharing Laravel's connection means the app, migrations, seeders and tests all use the
 * same database — tests get the in-memory database configured in phpunit.xml.
 */
class Database
{
    public static function connection(): PDO
    {
        return DB::connection('sqlite')->getPdo();
    }

    /**
     * @param  array<int, mixed>  $params
     * @return array<int, object>
     */
    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param  array<int, mixed>  $params
     */
    public static function first(string $sql, array $params = []): mixed
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    /**
     * @param  array<int, mixed>  $params
     */
    public static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::connection()->prepare($sql);

        return $stmt->execute($params);
    }

    public static function lastInsertId(): string
    {
        return self::connection()->lastInsertId();
    }
}
