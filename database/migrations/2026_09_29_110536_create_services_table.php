<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the services table. Skipped when the table already exists. Content is seeded by PortfolioSeeder.
     */
    public function up(): void
    {
        if (Schema::hasTable('services')) {
            return;
        }

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->default('fa-solid fa-code');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('summary');
            $table->text('summary_ar')->nullable();
            $table->json('deliverables')->nullable();
            $table->json('deliverables_ar')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
