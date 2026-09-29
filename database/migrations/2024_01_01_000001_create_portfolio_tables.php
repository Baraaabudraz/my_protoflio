<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the portfolio tables.
     *
     * Each table is only created when missing: the production database was built before this
     * migration was tracked, so running it there must not fail on tables that already exist.
     */
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('icon')->default('🚀');
                $table->text('image')->nullable();
                $table->json('stack')->nullable();
                $table->string('github_url')->nullable();
                $table->string('live_url')->nullable();
                $table->boolean('featured')->default(false);
                $table->integer('sort_order')->default(0);
                $table->boolean('visible')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('experiences')) {
            Schema::create('experiences', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('company');
                $table->string('date_range');
                $table->text('description');
                $table->json('tags')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('visible')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('skill_categories')) {
            Schema::create('skill_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icon')->default('⚙️');
                $table->string('type')->default('bars');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('skills')) {
            Schema::create('skills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('skill_category_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->integer('percentage')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('admin_sessions')) {
            Schema::create('admin_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('token')->unique();
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('admin_sessions');
    }
};
