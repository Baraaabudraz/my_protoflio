<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columns that were added directly to the live database and never captured in a migration.
     *
     * @var array<string, list<string>>
     */
    private array $columns = [
        'projects' => [
            'client', 'duration', 'category', 'overview', 'work_stages',
            'title_ar', 'description_ar', 'overview_ar', 'client_ar', 'duration_ar', 'category_ar', 'work_stages_ar',
        ],
        'experiences' => ['title_ar', 'company_ar', 'description_ar'],
        'skill_categories' => ['name_ar'],
        'skills' => ['name_ar'],
    ];

    /**
     * Add the bilingual (Arabic) and project case-study columns. Existing columns are skipped,
     * so this is safe on databases that already have them.
     */
    public function up(): void
    {
        foreach ($this->columns as $tableName => $columns) {
            $missing = array_values(array_filter($columns, fn (string $column): bool => ! Schema::hasColumn($tableName, $column)));

            if ($missing === []) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($missing) {
                foreach ($missing as $column) {
                    $table->text($column)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->columns as $tableName => $columns) {
            $existing = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn($tableName, $column)));

            if ($existing !== []) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropColumn($existing));
            }
        }
    }
};
