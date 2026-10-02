<?php

namespace App\Http\Controllers;

use App\Services\Database;
use App\Services\SeoBuilder;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(SeoBuilder $seoBuilder)
    {
        $projects = Database::query(
            'SELECT * FROM projects WHERE visible = 1 ORDER BY featured DESC, sort_order ASC'
        );
        foreach ($projects as $p) {
            $p->stack = json_decode($p->stack ?? '[]');
        }
        $this->useGalleryAsCover($projects);

        $services = Database::query(
            'SELECT * FROM services WHERE visible = 1 ORDER BY sort_order ASC'
        );
        foreach ($services as $s) {
            $s->deliverables = json_decode($s->deliverables ?? '[]');
            $s->deliverables_ar = json_decode($s->deliverables_ar ?? '[]');
        }

        $experiences = Database::query(
            'SELECT * FROM experiences WHERE visible = 1 ORDER BY sort_order ASC'
        );
        foreach ($experiences as $e) {
            $e->tags = json_decode($e->tags ?? '[]');
        }

        $catRows = Database::query(
            'SELECT * FROM skill_categories ORDER BY sort_order ASC'
        );
        $categories = [];
        foreach ($catRows as $cat) {
            $cat->skills = Database::query(
                'SELECT * FROM skills WHERE skill_category_id = ? ORDER BY sort_order ASC',
                [$cat->id]
            );
            $categories[] = $cat;
        }

        $settingRows = Database::query('SELECT key, value FROM settings');
        $settings = [];
        foreach ($settingRows as $s) {
            $settings[$s->key] = $s->value;
        }

        $seo = $seoBuilder->home($settings, $services);

        return view('portfolio', compact('projects', 'services', 'experiences', 'categories', 'settings', 'seo'));
    }

    public function show(int $id, SeoBuilder $seoBuilder)
    {
        $project = Database::first('SELECT * FROM projects WHERE id = ? AND visible = 1', [$id]);
        if (! $project) {
            abort(404);
        }

        $project->stack = json_decode($project->stack ?? '[]');
        $project->work_stages = json_decode($project->work_stages ?? '[]');
        $workStagesAr = json_decode($project->work_stages_ar ?? '[]');
        if (app()->getLocale() === 'ar' && ! empty($workStagesAr)) {
            $project->work_stages = $workStagesAr;
        }

        // Related projects: same category, exclude current
        $related = [];
        if ($project->category) {
            $related = Database::query(
                'SELECT * FROM projects WHERE category = ? AND id != ? AND visible = 1 ORDER BY featured DESC, sort_order ASC LIMIT 3',
                [$project->category, $id]
            );
            foreach ($related as $r) {
                $r->stack = json_decode($r->stack ?? '[]');
            }
        }
        if (empty($related)) {
            $related = Database::query(
                'SELECT * FROM projects WHERE id != ? AND visible = 1 ORDER BY featured DESC, sort_order ASC LIMIT 3',
                [$id]
            );
            foreach ($related as $r) {
                $r->stack = json_decode($r->stack ?? '[]');
            }
        }

        $settingRows = Database::query('SELECT key, value FROM settings');
        $settings = [];
        foreach ($settingRows as $s) {
            $settings[$s->key] = $s->value;
        }

        // Previous / next project in the same order as the portfolio grid
        $ordered = Database::query('SELECT id, title, title_ar, icon FROM projects WHERE visible = 1 ORDER BY featured DESC, sort_order ASC');
        $position = array_search($id, array_map(fn (object $row): int => (int) $row->id, $ordered), true);
        $previousProject = $position !== false && $position > 0 ? $ordered[$position - 1] : null;
        $nextProject = $position !== false ? ($ordered[$position + 1] ?? null) : null;

        $gallery = Database::query('SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC, id ASC', [$id]);
        $this->useGalleryAsCover([$project]);
        $this->useGalleryAsCover($related);
        $project->gallery = $gallery;

        $seo = $seoBuilder->project($project, $settings);

        return view('project-detail', compact('project', 'related', 'settings', 'seo', 'previousProject', 'nextProject', 'gallery'));
    }

    /**
     * Projects without a cover image use their first gallery image instead.
     *
     * @param  array<int, object>  $projects
     */
    private function useGalleryAsCover(array $projects): void
    {
        $withoutCover = array_filter($projects, fn (object $project): bool => empty($project->image));
        if ($withoutCover === []) {
            return;
        }

        $ids = array_map(fn (object $project): int => (int) $project->id, $withoutCover);
        $firstImages = Database::query(
            'SELECT pi.project_id, pi.path FROM project_images pi
             WHERE pi.project_id IN ('.implode(',', array_fill(0, count($ids), '?')).')
               AND pi.id = (SELECT id FROM project_images WHERE project_id = pi.project_id ORDER BY sort_order ASC, id ASC LIMIT 1)',
            array_values($ids)
        );

        $covers = array_column($firstImages, 'path', 'project_id');
        foreach ($withoutCover as $project) {
            if (isset($covers[$project->id])) {
                $project->image = 'uploads/'.$covers[$project->id];
            }
        }
    }

    public function switchLocale(Request $request, string $locale)
    {
        if (in_array($locale, ['en', 'ar'])) {
            session(['locale' => $locale]);
        }

        return redirect()->back();
    }
}
