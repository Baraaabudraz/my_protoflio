<?php

namespace App\Http\Controllers;

use App\Services\Database;

class PortfolioController extends Controller
{
    public function index()
    {
        $db = Database::connection();

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
}
