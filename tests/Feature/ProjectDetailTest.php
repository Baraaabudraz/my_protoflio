<?php

namespace Tests\Feature;

use App\Services\Database;
use Tests\TestCase;

/**
 * Read-only checks against the portfolio SQLite database used by App\Services\Database.
 */
class ProjectDetailTest extends TestCase
{
    public function test_every_visible_project_page_renders_in_both_languages(): void
    {
        $projects = Database::query('SELECT id, title, title_ar FROM projects WHERE visible = 1');

        if (empty($projects)) {
            $this->markTestSkipped('No visible projects in the database.');
        }

        foreach ($projects as $project) {
            $this->get(route('project.show', $project->id).'?lang=en')
                ->assertOk()
                ->assertSee('<h1 class="project-title">'.e($project->title).'</h1>', false)
                ->assertSee('aria-label="Breadcrumb"', false)
                ->assertSee('I want something similar');

            $this->get(route('project.show', $project->id).'?lang=ar')
                ->assertOk()
                ->assertSee('<h1 class="project-title">'.e($project->title_ar ?: $project->title).'</h1>', false);
        }
    }

    public function test_project_pages_link_to_their_neighbours_in_portfolio_order(): void
    {
        $ordered = Database::query('SELECT id FROM projects WHERE visible = 1 ORDER BY featured DESC, sort_order ASC');

        if (count($ordered) < 2) {
            $this->markTestSkipped('Need at least two visible projects.');
        }

        $first = $ordered[0]->id;
        $second = $ordered[1]->id;

        $this->get(route('project.show', $first).'?lang=en')
            ->assertSee('rel="next"', false)
            ->assertDontSee('rel="prev"', false)
            ->assertSee(route('project.show', $second).'?lang=en', false);

        $this->get(route('project.show', $second).'?lang=en')
            ->assertSee('rel="prev"', false)
            ->assertSee(route('project.show', $first).'?lang=en', false);
    }

    public function test_unknown_or_hidden_project_returns_404(): void
    {
        $this->get(route('project.show', 999999))->assertNotFound();

        $hidden = Database::first('SELECT id FROM projects WHERE visible = 0 LIMIT 1');
        if ($hidden) {
            $this->get(route('project.show', $hidden->id))->assertNotFound();
        }
    }
}
