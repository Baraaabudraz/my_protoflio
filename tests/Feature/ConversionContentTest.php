<?php

namespace Tests\Feature;

use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * FAQs, testimonials and project results — admin CRUD and how they appear on the public site.
 * Runs against the in-memory test database seeded from database/seeders/data.
 */
class ConversionContentTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected $seeder = PortfolioSeeder::class;

    private int $projectId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectId = (int) DB::table('projects')->where('visible', 1)->orderBy('id')->value('id');
    }

    public function test_home_page_lists_visible_faqs_with_structured_data(): void
    {
        DB::table('faqs')->insert([
            ['question' => 'Visible question?', 'answer' => 'Visible answer.', 'sort_order' => 99, 'visible' => 1],
            ['question' => 'Hidden question?', 'answer' => 'Hidden answer.', 'sort_order' => 100, 'visible' => 0],
        ]);

        $this->get('/?lang=en')
            ->assertOk()
            ->assertSee('id="faq"', false)
            ->assertSee('Visible question?')
            ->assertDontSee('Hidden question?')
            ->assertSee('"@type":"FAQPage"', false);
    }

    public function test_diagnose_section_links_each_symptom_to_a_case_study(): void
    {
        $response = $this->get('/?lang=en')->assertOk()->assertSee('id="diagnose"', false);

        foreach (['slow', 'breaking', 'outdated', 'idea'] as $symptom) {
            $response->assertSee('id="tab-'.$symptom.'"', false)->assertSee('id="panel-'.$symptom.'"', false);
        }
        $response->assertSee('data-message=', false)->assertSee(route('project.show', $this->projectId), false);

        $this->get('/?lang=ar')->assertOk()->assertSee('id="diagnose"', false);
    }

    public function test_hero_headline_keeps_the_full_sentence_for_screen_readers(): void
    {
        $this->get('/?lang=en')
            ->assertOk()
            ->assertSee('aria-label="I build, fix, speed up and rescue web systems that help your business grow."', false)
            ->assertSee('data-cycler=', false);
    }

    public function test_testimonials_section_only_appears_once_there_is_one(): void
    {
        $this->get('/?lang=en')->assertOk()->assertDontSee('id="testimonials"', false);

        DB::table('testimonials')->insert([
            'name' => 'Sara K.', 'role' => 'Operations lead', 'quote' => 'Our dashboard finally loads instantly.',
            'project_id' => $this->projectId, 'sort_order' => 0, 'visible' => 1,
        ]);

        $this->get('/?lang=en')
            ->assertOk()
            ->assertSee('id="testimonials"', false)
            ->assertSee('Our dashboard finally loads instantly.');

        $this->get(route('project.show', $this->projectId).'?lang=en')
            ->assertOk()
            ->assertSee('Our dashboard finally loads instantly.');
    }

    public function test_project_result_is_highlighted_in_both_languages(): void
    {
        DB::table('projects')->where('id', $this->projectId)->update(['result' => 'Load time cut from 6s to 1.2s', 'result_ar' => 'تقليل زمن التحميل']);

        $this->get('/?lang=en')->assertOk()->assertSee('Load time cut from 6s to 1.2s');
        $this->get(route('project.show', $this->projectId).'?lang=en')->assertOk()->assertSee('Load time cut from 6s to 1.2s');
        $this->get(route('project.show', $this->projectId).'?lang=ar')->assertOk()->assertSee('تقليل زمن التحميل');
    }

    public function test_admin_pages_require_login(): void
    {
        $this->get(route('admin.faqs'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.testimonials'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.faqs.store'), ['question' => 'Q?', 'answer' => 'A.'])->assertRedirect(route('admin.login'));
        $this->post(route('admin.testimonials.store'), ['name' => 'X', 'quote' => 'Y'])->assertRedirect(route('admin.login'));
        $this->assertDatabaseMissing('faqs', ['question' => 'Q?']);
        $this->assertDatabaseMissing('testimonials', ['name' => 'X']);
    }

    public function test_admin_can_manage_faqs(): void
    {
        $this->withSession(['admin_logged_in' => true]);

        $this->get(route('admin.faqs'))->assertOk();
        $this->get(route('admin.faqs.create'))->assertOk();

        $this->post(route('admin.faqs.store'), ['question' => 'Do you sign an NDA?', 'question_ar' => 'هل توقّع اتفاقية سرية؟', 'answer' => 'Yes.', 'sort_order' => 7, 'visible' => 'on'])
            ->assertRedirect(route('admin.faqs'));
        $id = (int) DB::table('faqs')->where('question', 'Do you sign an NDA?')->value('id');
        $this->assertSame('هل توقّع اتفاقية سرية؟', DB::table('faqs')->where('id', $id)->value('question_ar'));

        $this->get(route('admin.faqs.edit', $id))->assertOk()->assertSee('Do you sign an NDA?');
        $this->put(route('admin.faqs.update', $id), ['question' => 'Do you sign NDAs?', 'answer' => 'Always.'])
            ->assertRedirect(route('admin.faqs'));
        $this->assertDatabaseHas('faqs', ['id' => $id, 'question' => 'Do you sign NDAs?', 'visible' => 0]);

        $this->delete(route('admin.faqs.delete', $id))->assertRedirect(route('admin.faqs'));
        $this->assertDatabaseMissing('faqs', ['id' => $id]);
    }

    public function test_admin_can_manage_testimonials(): void
    {
        $this->withSession(['admin_logged_in' => true]);

        $this->get(route('admin.testimonials'))->assertOk();
        $this->get(route('admin.testimonials.create'))->assertOk();

        $this->post(route('admin.testimonials.store'), ['name' => 'Omar A.', 'quote' => 'Fast and reliable.', 'project_id' => $this->projectId, 'visible' => 'on'])
            ->assertRedirect(route('admin.testimonials'));
        $id = (int) DB::table('testimonials')->where('name', 'Omar A.')->value('id');
        $this->assertSame($this->projectId, (int) DB::table('testimonials')->where('id', $id)->value('project_id'));

        $this->put(route('admin.testimonials.update', $id), ['name' => 'Omar A.', 'quote' => 'Fast, reliable, honest.', 'project_id' => 999999, 'visible' => 'on'])
            ->assertRedirect(route('admin.testimonials'));
        $this->assertDatabaseHas('testimonials', ['id' => $id, 'quote' => 'Fast, reliable, honest.', 'project_id' => null]);

        $this->post(route('admin.testimonials.store'), ['name' => '', 'quote' => ''])->assertSessionHasErrors(['name', 'quote']);

        $this->delete(route('admin.testimonials.delete', $id))->assertRedirect(route('admin.testimonials'));
        $this->assertDatabaseMissing('testimonials', ['id' => $id]);
    }

    public function test_admin_can_save_a_project_result(): void
    {
        $this->withSession(['admin_logged_in' => true]);
        $project = DB::table('projects')->where('id', $this->projectId)->first();

        $this->put(route('admin.projects.update', $this->projectId), [
            'title' => $project->title,
            'description' => $project->description,
            'result' => '3x faster reports',
            'result_ar' => 'تقارير أسرع 3 مرات',
            'visible' => 'on',
        ])->assertRedirect(route('admin.projects'));

        $this->assertDatabaseHas('projects', ['id' => $this->projectId, 'result' => '3x faster reports', 'result_ar' => 'تقارير أسرع 3 مرات']);
        $this->get(route('admin.projects.edit', $this->projectId))->assertOk()->assertSee('3x faster reports');
    }
}
