<?php

namespace App\Http\Controllers;

use App\Services\Database;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        $projects = Database::query(
            'SELECT * FROM projects WHERE visible = 1 ORDER BY featured DESC, sort_order ASC'
        );
        foreach ($projects as $p) {
            $p->stack = json_decode($p->stack ?? '[]');
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

        return view('portfolio', compact('projects', 'experiences', 'categories', 'settings'));
    }

    public function show(int $id)
    {
        $project = Database::first('SELECT * FROM projects WHERE id = ? AND visible = 1', [$id]);
        if (!$project) abort(404);

        $project->stack       = json_decode($project->stack ?? '[]');
        $project->work_stages = json_decode($project->work_stages ?? '[]');

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

        return view('project-detail', compact('project', 'related', 'settings'));
    }

    public function switchLocale(Request $request, string $locale)
    {
        if (in_array($locale, ['en', 'ar'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }
}
