<?php

namespace App\Http\Controllers;

use App\Services\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    private function auth()
    {
        return Session::get('admin_logged_in') === true;
    }

    private function getSettings(): array
    {
        $rows = Database::query('SELECT key, value FROM settings');
        $out  = [];
        foreach ($rows as $r) { $out[$r->key] = $r->value; }
        return $out;
    }

    // ─── Auth ───
    public function loginForm()
    {
        if ($this->auth()) return redirect()->route('admin.dashboard');
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $password = env('ADMIN_PASSWORD', 'admin123');
        if ($request->password === $password) {
            Session::put('admin_logged_in', true);
            return redirect()->route('admin.dashboard');
        }
        return back()->with('error', 'Incorrect password.');
    }

    public function logout()
    {
        Session::forget('admin_logged_in');
        return redirect()->route('admin.login');
    }

    // ─── Dashboard ───
    public function dashboard()
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $stats = [
            'projects'    => Database::first('SELECT COUNT(*) as c FROM projects')->c,
            'experiences' => Database::first('SELECT COUNT(*) as c FROM experiences')->c,
            'skills'      => Database::first('SELECT COUNT(*) as c FROM skills')->c,
        ];
        return view('admin.dashboard', compact('stats'));
    }

    // ─── Projects ───
    public function projects()
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $projects = Database::query('SELECT * FROM projects ORDER BY sort_order ASC');
        foreach ($projects as $p) {
            $p->stack    = json_decode($p->stack ?? '[]');
            $p->featured = (bool) $p->featured;
            $p->visible  = (bool) $p->visible;
        }
        return view('admin.projects.index', compact('projects'));
    }

    public function projectCreate()
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        return view('admin.projects.form', ['project' => null]);
    }

    public function projectStore(Request $request)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|string|max:2048',
        ]);
        $stack      = $request->stack ? json_encode(array_map('trim', explode(',', $request->stack))) : '[]';
        $featured   = $request->has('featured') ? 1 : 0;
        $visible    = $request->has('visible')  ? 1 : 0;
        $now        = now()->toDateTimeString();
        $workStages = $this->parseWorkStages($request->work_stages ?? '');
        $workStagesAr = $this->parseWorkStages($request->work_stages_ar ?? '');

        Database::execute(
            'INSERT INTO projects
             (title,title_ar,description,description_ar,icon,image,stack,github_url,live_url,featured,sort_order,visible,
              client,client_ar,duration,duration_ar,category,category_ar,overview,overview_ar,work_stages,work_stages_ar,
              created_at,updated_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $request->title,          $request->title_ar,
                $request->description,    $request->description_ar,
                $request->icon ?? '🚀',  $request->image, $stack,
                $request->github_url,     $request->live_url,
                $featured, (int)($request->sort_order ?? 0), $visible,
                $request->client,         $request->client_ar,
                $request->duration,       $request->duration_ar,
                $request->category,       $request->category_ar,
                $request->overview,       $request->overview_ar,
                $workStages,              $workStagesAr,
                $now, $now,
            ]
        );
        return redirect()->route('admin.projects')->with('success', 'Project created!');
    }

    public function projectEdit(int $id)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $project = Database::first('SELECT * FROM projects WHERE id = ?', [$id]);
        if (!$project) abort(404);
        $project->stack       = json_decode($project->stack ?? '[]');
        $project->work_stages = json_decode($project->work_stages ?? '[]');
        $project->work_stages_ar = json_decode($project->work_stages_ar ?? '[]');
        $project->featured    = (bool) $project->featured;
        $project->visible     = (bool) $project->visible;
        return view('admin.projects.form', compact('project'));
    }

    public function projectUpdate(Request $request, int $id)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|string|max:2048',
        ]);
        $stack       = $request->stack ? json_encode(array_map('trim', explode(',', $request->stack))) : '[]';
        $featured    = $request->has('featured') ? 1 : 0;
        $visible     = $request->has('visible')  ? 1 : 0;
        $workStages  = $this->parseWorkStages($request->work_stages ?? '');
        $workStagesAr = $this->parseWorkStages($request->work_stages_ar ?? '');

        Database::execute(
            'UPDATE projects SET
             title=?,title_ar=?,description=?,description_ar=?,icon=?,image=?,stack=?,github_url=?,live_url=?,
             featured=?,sort_order=?,visible=?,
             client=?,client_ar=?,duration=?,duration_ar=?,category=?,category_ar=?,
             overview=?,overview_ar=?,work_stages=?,work_stages_ar=?,updated_at=?
             WHERE id=?',
            [
                $request->title,       $request->title_ar,
                $request->description, $request->description_ar,
                $request->icon ?? '🚀', $request->image, $stack,
                $request->github_url,  $request->live_url,
                $featured, (int)($request->sort_order ?? 0), $visible,
                $request->client,      $request->client_ar,
                $request->duration,    $request->duration_ar,
                $request->category,    $request->category_ar,
                $request->overview,    $request->overview_ar,
                $workStages,           $workStagesAr,
                now()->toDateTimeString(), $id,
            ]
        );
        return redirect()->route('admin.projects')->with('success', 'Project updated!');
    }

    private function parseWorkStages(string $raw): string
    {
        if (empty(trim($raw))) return '[]';
        $lines  = array_filter(array_map('trim', explode("\n", $raw)));
        $stages = [];
        foreach ($lines as $line) {
            if (!empty($line)) $stages[] = $line;
        }
        return json_encode($stages);
    }

    public function projectDelete(int $id)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        Database::execute('DELETE FROM projects WHERE id = ?', [$id]);
        return redirect()->route('admin.projects')->with('success', 'Project deleted.');
    }

    // ─── Experience ───
    public function experiences()
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $experiences = Database::query('SELECT * FROM experiences ORDER BY sort_order ASC');
        foreach ($experiences as $e) {
            $e->tags    = json_decode($e->tags ?? '[]');
            $e->visible = (bool) $e->visible;
        }
        return view('admin.experience.index', compact('experiences'));
    }

    public function experienceCreate()
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        return view('admin.experience.form', ['experience' => null]);
    }

    public function experienceStore(Request $request)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $request->validate([
            'title'       => 'required|string|max:255',
            'company'     => 'required|string|max:255',
            'date_range'  => 'required|string|max:100',
            'description' => 'required|string',
        ]);
        $tags    = $request->tags ? json_encode(array_map('trim', explode(',', $request->tags))) : '[]';
        $visible = $request->has('visible') ? 1 : 0;
        $now     = now()->toDateTimeString();

        Database::execute(
            'INSERT INTO experiences
             (title,title_ar,company,company_ar,date_range,description,description_ar,tags,sort_order,visible,created_at,updated_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $request->title,   $request->title_ar,
                $request->company, $request->company_ar,
                $request->date_range,
                $request->description, $request->description_ar,
                $tags, (int)($request->sort_order ?? 0), $visible, $now, $now,
            ]
        );
        return redirect()->route('admin.experience')->with('success', 'Experience created!');
    }

    public function experienceEdit(int $id)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $experience = Database::first('SELECT * FROM experiences WHERE id = ?', [$id]);
        if (!$experience) abort(404);
        $experience->tags    = json_decode($experience->tags ?? '[]');
        $experience->visible = (bool) $experience->visible;
        return view('admin.experience.form', compact('experience'));
    }

    public function experienceUpdate(Request $request, int $id)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $request->validate([
            'title'       => 'required|string|max:255',
            'company'     => 'required|string|max:255',
            'date_range'  => 'required|string|max:100',
            'description' => 'required|string',
        ]);
        $tags    = $request->tags ? json_encode(array_map('trim', explode(',', $request->tags))) : '[]';
        $visible = $request->has('visible') ? 1 : 0;

        Database::execute(
            'UPDATE experiences SET
             title=?,title_ar=?,company=?,company_ar=?,date_range=?,description=?,description_ar=?,
             tags=?,sort_order=?,visible=?,updated_at=?
             WHERE id=?',
            [
                $request->title,   $request->title_ar,
                $request->company, $request->company_ar,
                $request->date_range,
                $request->description, $request->description_ar,
                $tags, (int)($request->sort_order ?? 0), $visible,
                now()->toDateTimeString(), $id,
            ]
        );
        return redirect()->route('admin.experience')->with('success', 'Experience updated!');
    }

    public function experienceDelete(int $id)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        Database::execute('DELETE FROM experiences WHERE id = ?', [$id]);
        return redirect()->route('admin.experience')->with('success', 'Experience deleted.');
    }

    // ─── Skills ───
    public function skills()
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $catRows    = Database::query('SELECT * FROM skill_categories ORDER BY sort_order ASC');
        $categories = [];
        foreach ($catRows as $cat) {
            $cat->skills = Database::query(
                'SELECT * FROM skills WHERE skill_category_id = ? ORDER BY sort_order ASC', [$cat->id]
            );
            $categories[] = $cat;
        }
        return view('admin.skills.index', compact('categories'));
    }

    public function skillCategoryStore(Request $request)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $request->validate(['name' => 'required', 'type' => 'required']);
        $now = now()->toDateTimeString();
        Database::execute(
            'INSERT INTO skill_categories (name,name_ar,icon,type,sort_order,created_at,updated_at) VALUES (?,?,?,?,?,?,?)',
            [$request->name, $request->name_ar, $request->icon ?? '⚙️', $request->type, (int)($request->sort_order ?? 0), $now, $now]
        );
        return back()->with('success', 'Category created!');
    }

    public function skillCategoryDelete(int $id)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        Database::execute('DELETE FROM skills WHERE skill_category_id = ?', [$id]);
        Database::execute('DELETE FROM skill_categories WHERE id = ?', [$id]);
        return back()->with('success', 'Category deleted.');
    }

    public function skillStore(Request $request)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $request->validate(['name' => 'required', 'skill_category_id' => 'required']);
        $now = now()->toDateTimeString();
        Database::execute(
            'INSERT INTO skills (skill_category_id,name,name_ar,percentage,sort_order,created_at,updated_at) VALUES (?,?,?,?,?,?,?)',
            [$request->skill_category_id, $request->name, $request->name_ar,
             $request->percentage ?: null, (int)($request->sort_order ?? 0), $now, $now]
        );
        return back()->with('success', 'Skill added!');
    }

    public function skillDelete(int $id)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        Database::execute('DELETE FROM skills WHERE id = ?', [$id]);
        return back()->with('success', 'Skill deleted.');
    }

    // ─── Settings ───
    public function settings()
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $settings = $this->getSettings();
        return view('admin.settings', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        if (!$this->auth()) return redirect()->route('admin.login');
        $fields = [
            'hero_name',    'hero_name_ar',
            'hero_tagline', 'hero_tagline_ar',
            'hero_subtitle','hero_subtitle_ar',
            'hero_stat_years','hero_stat_projects','hero_stat_clients',
            'about_heading','about_heading_ar',
            'about_p1',     'about_p1_ar',
            'about_p2',     'about_p2_ar',
            'about_p3',     'about_p3_ar',
            'about_tags',   'about_tags_ar',
            'email','github_url','linkedin_url',
            'footer_text',  'footer_text_ar',
        ];
        $now = now()->toDateTimeString();
        foreach ($fields as $field) {
            $val = $request->input($field, '');
            Database::execute(
                'INSERT OR REPLACE INTO settings (key, value, created_at, updated_at) VALUES (?,?,?,?)',
                [$field, $val, $now, $now]
            );
        }
        return back()->with('success', 'Settings saved!');
    }
}
