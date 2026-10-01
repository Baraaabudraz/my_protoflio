<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FAQs, client testimonials and a headline result per project (shown on case-study cards).
     */
    public function up(): void
    {
        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->string('question_ar')->nullable();
                $table->text('answer');
                $table->text('answer_ar')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('visible')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('role')->nullable();
                $table->string('role_ar')->nullable();
                $table->text('quote');
                $table->text('quote_ar')->nullable();
                $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
                $table->integer('sort_order')->default(0);
                $table->boolean('visible')->default(true);
                $table->timestamps();
            });
        }

        $missing = array_values(array_filter(['result', 'result_ar'], fn (string $column): bool => ! Schema::hasColumn('projects', $column)));
        if ($missing !== []) {
            Schema::table('projects', function (Blueprint $table) use ($missing) {
                foreach ($missing as $column) {
                    $table->string($column)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('faqs');

        $existing = array_values(array_filter(['result', 'result_ar'], fn (string $column): bool => Schema::hasColumn('projects', $column)));
        if ($existing !== []) {
            Schema::table('projects', fn (Blueprint $table) => $table->dropColumn($existing));
        }
    }
};
