<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the services table with two starter services. Skipped when the table already exists.
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

        $now = now()->toDateTimeString();

        DB::table('services')->insert([
            [
                'icon' => 'fa-solid fa-screwdriver-wrench',
                'title' => 'Maintenance, Upgrades & Fixes',
                'title_ar' => 'الصيانة والتحديث وإصلاح الأخطاء',
                'summary' => 'Your Laravel app is outdated, buggy, or nobody wants to touch the code? I stabilize it, upgrade it safely, and keep it running so you can focus on your business.',
                'summary_ar' => 'تطبيقك على Laravel قديم أو مليء بالأخطاء أو لا أحد يجرؤ على لمس الكود؟ أعيد له الاستقرار، وأحدّثه بأمان، وأحافظ على عمله لتتفرغ أنت لعملك.',
                'deliverables' => json_encode(['Laravel & PHP version upgrades', 'Bug fixing & error monitoring', 'Security patches & dependency updates', 'Ongoing monthly support']),
                'deliverables_ar' => json_encode(['ترقية إصدارات Laravel وPHP', 'إصلاح الأخطاء ومراقبتها', 'سد الثغرات الأمنية وتحديث الحزم', 'دعم فني شهري مستمر'], JSON_UNESCAPED_UNICODE),
                'sort_order' => 0,
                'visible' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'icon' => 'fa-solid fa-gauge-high',
                'title' => 'Performance & Database Optimization',
                'title_ar' => 'تحسين الأداء وقواعد البيانات',
                'summary' => 'Slow pages lose customers. I find the real bottlenecks — heavy queries, missing indexes, no caching — and make your app fast and ready to handle more users.',
                'summary_ar' => 'الصفحات البطيئة تُفقدك العملاء. أكتشف الأسباب الحقيقية للبطء — استعلامات ثقيلة، فهارس مفقودة، غياب التخزين المؤقت — وأجعل تطبيقك سريعًا وجاهزًا لاستقبال مستخدمين أكثر.',
                'deliverables' => json_encode(['Performance audit with a clear report', 'Query & index optimization', 'Caching with Redis & queues', 'Before/after speed measurements']),
                'deliverables_ar' => json_encode(['تدقيق شامل للأداء مع تقرير واضح', 'تحسين الاستعلامات والفهارس', 'التخزين المؤقت باستخدام Redis والطوابير', 'قياس السرعة قبل التحسين وبعده'], JSON_UNESCAPED_UNICODE),
                'sort_order' => 1,
                'visible' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
