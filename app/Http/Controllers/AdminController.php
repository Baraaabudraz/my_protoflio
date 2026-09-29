<?php

namespace App\Http\Controllers;

use App\Services\Database;
use App\Services\SeoBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function auth()
    {
        return Session::get('admin_logged_in') === true;
    }

    private function getSettings(): array
    {
        $rows = Database::query('SELECT key, value FROM settings');
        $out = [];
        foreach ($rows as $r) {
            $out[$r->key] = $r->value;
        }

        return $out;
    }

    // ─── Auth ───
    public function loginForm()
    {
        if ($this->auth()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $password = env('ADMIN_PASSWORD', 'admin123');
        if ($request->password === $password) {
            Session::put('admin_logged_in', true);

            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', __('Incorrect password.'));
    }

    public function logout()
    {
        Session::forget('admin_logged_in');

        return redirect()->route('admin.login');
    }

    // ─── Dashboard ───
    public function dashboard(SeoBuilder $seoBuilder)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }

        $count = fn (string $sql): int => (int) Database::first($sql)->c;
        $stats = [
            'services' => $count('SELECT COUNT(*) as c FROM services'),
            'projects' => $count('SELECT COUNT(*) as c FROM projects'),
            'projects_visible' => $count('SELECT COUNT(*) as c FROM projects WHERE visible = 1'),
            'experiences' => $count('SELECT COUNT(*) as c FROM experiences'),
            'skills' => $count('SELECT COUNT(*) as c FROM skills'),
        ];

        $recentProjects = Database::query('SELECT id, title, title_ar, icon, image, visible, featured, updated_at FROM projects ORDER BY updated_at DESC LIMIT 5');

        $settings = $this->getSettings();
        $profileLinks = $seoBuilder->profileLinks($settings);
        $isFilled = fn (string $key): bool => trim($settings[$key] ?? '') !== '';

        $healthChecks = [
            [
                'label' => __('Admin password changed from the default'),
                'ok' => env('ADMIN_PASSWORD', 'admin123') !== 'admin123',
                'hint' => __('Set ADMIN_PASSWORD in the .laravel.env file.'),
                'url' => null,
            ],
            [
                'label' => __('WhatsApp number added'),
                'ok' => $isFilled('whatsapp_number'),
                'hint' => __('Lets clients contact you in one tap.'),
                'url' => route('admin.settings'),
            ],
            [
                'label' => __('GitHub & LinkedIn point to your real profiles'),
                'ok' => count($profileLinks) === 2,
                'hint' => __('Used by Google to connect your profiles.'),
                'url' => route('admin.settings'),
            ],
            [
                'label' => __('SEO title & description filled in both languages'),
                'ok' => $isFilled('seo_title') && $isFilled('seo_title_ar') && $isFilled('seo_description') && $isFilled('seo_description_ar'),
                'hint' => __('Controls how you appear in Google results.'),
                'url' => route('admin.settings'),
            ],
            [
                'label' => __('Every project has a cover image'),
                'ok' => $count("SELECT COUNT(*) as c FROM projects WHERE visible = 1 AND (image IS NULL OR image = '')") === 0,
                'hint' => __('Projects with images get more clicks.'),
                'url' => route('admin.projects'),
            ],
            [
                'label' => __('Every project is translated to Arabic'),
                'ok' => $count("SELECT COUNT(*) as c FROM projects WHERE visible = 1 AND (title_ar IS NULL OR title_ar = '' OR description_ar IS NULL OR description_ar = '')") === 0,
                'hint' => __('Arabic visitors otherwise see English text.'),
                'url' => route('admin.projects'),
            ],
            [
                'label' => __('Every service is translated to Arabic'),
                'ok' => $count("SELECT COUNT(*) as c FROM services WHERE visible = 1 AND (title_ar IS NULL OR title_ar = '' OR summary_ar IS NULL OR summary_ar = '')") === 0,
                'hint' => __('Arabic visitors otherwise see English text.'),
                'url' => route('admin.services'),
            ],
        ];

        $googlePreview = [
            'title' => ts($settings, 'seo_title') ?: (ts($settings, 'hero_name').' — '.ts($settings, 'hero_tagline')),
            'url' => url('/'),
            'description' => ts($settings, 'seo_description') ?: strip_tags(ts($settings, 'hero_subtitle')),
        ];

        return view('admin.dashboard', compact('stats', 'recentProjects', 'healthChecks', 'googlePreview', 'settings'));
    }

    // ─── Projects ───
    public function projects()
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $projects = Database::query('SELECT * FROM projects ORDER BY sort_order ASC');
        foreach ($projects as $p) {
            $p->stack = json_decode($p->stack ?? '[]');
            $p->featured = (bool) $p->featured;
            $p->visible = (bool) $p->visible;
        }

        return view('admin.projects.index', compact('projects'));
    }

    public function projectCreate()
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }

        return view('admin.projects.form', ['project' => null]);
    }

    public function projectStore(Request $request)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'image_url' => 'nullable|string|max:2048',
        ]);
        $stack = $request->stack ? json_encode(array_map('trim', explode(',', $request->stack))) : '[]';
        $featured = $request->has('featured') ? 1 : 0;
        $visible = $request->has('visible') ? 1 : 0;
        $now = now()->toDateTimeString();
        $workStages = $this->parseWorkStages($request->work_stages ?? '');
        $workStagesAr = $this->parseWorkStages($request->work_stages_ar ?? '');
        $image = $this->resolveProjectImage($request);

        Database::execute(
            'INSERT INTO projects
             (title,title_ar,description,description_ar,icon,image,stack,github_url,live_url,featured,sort_order,visible,
              client,client_ar,duration,duration_ar,category,category_ar,overview,overview_ar,work_stages,work_stages_ar,
              created_at,updated_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $request->title,          $request->title_ar,
                $request->description,    $request->description_ar,
                $request->icon ?? '🚀',  $image, $stack,
                $request->github_url,     $request->live_url,
                $featured, (int) ($request->sort_order ?? 0), $visible,
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
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $project = Database::first('SELECT * FROM projects WHERE id = ?', [$id]);
        if (! $project) {
            abort(404);
        }
        $project->stack = json_decode($project->stack ?? '[]');
        $project->work_stages = json_decode($project->work_stages ?? '[]');
        $project->work_stages_ar = json_decode($project->work_stages_ar ?? '[]');
        $project->featured = (bool) $project->featured;
        $project->visible = (bool) $project->visible;

        return view('admin.projects.form', compact('project'));
    }

    public function projectUpdate(Request $request, int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'image_url' => 'nullable|string|max:2048',
        ]);
        $stack = $request->stack ? json_encode(array_map('trim', explode(',', $request->stack))) : '[]';
        $featured = $request->has('featured') ? 1 : 0;
        $visible = $request->has('visible') ? 1 : 0;
        $workStages = $this->parseWorkStages($request->work_stages ?? '');
        $workStagesAr = $this->parseWorkStages($request->work_stages_ar ?? '');
        $currentProject = Database::first('SELECT image FROM projects WHERE id = ?', [$id]);
        $image = $this->resolveProjectImage($request, $currentProject?->image);

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
                $request->icon ?? '🚀', $image, $stack,
                $request->github_url,  $request->live_url,
                $featured, (int) ($request->sort_order ?? 0), $visible,
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
        if (empty(trim($raw))) {
            return '[]';
        }
        $lines = array_filter(array_map('trim', explode("\n", $raw)));
        $stages = [];
        foreach ($lines as $line) {
            if (! empty($line)) {
                $stages[] = $line;
            }
        }

        return json_encode($stages);
    }

    private function resolveProjectImage(Request $request, ?string $currentImage = null): ?string
    {
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('project-covers', 'public');

            return '/storage/'.$path;
        }

        if ($request->filled('image_url')) {
            $image = trim((string) $request->input('image_url'));
            if (preg_match('~drive\.google\.com/(?:file/d/|open\?id=)([^/?&]+)~i', $image, $match)) {
                return 'https://drive.google.com/uc?export=view&id='.rawurlencode($match[1]);
            }

            return $image;
        }

        if ($request->boolean('remove_image')) {
            return null;
        }

        return $currentImage ?: null;
    }

    public function projectDelete(int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        Database::execute('DELETE FROM projects WHERE id = ?', [$id]);

        return redirect()->route('admin.projects')->with('success', 'Project deleted.');
    }

    // ─── Services ───
    public function services()
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $services = Database::query('SELECT * FROM services ORDER BY sort_order ASC');
        foreach ($services as $s) {
            $s->deliverables = json_decode($s->deliverables ?? '[]');
            $s->visible = (bool) $s->visible;
        }

        return view('admin.services.index', compact('services'));
    }

    public function serviceCreate()
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }

        return view('admin.services.form', ['service' => null]);
    }

    public function serviceStore(Request $request)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'icon' => 'nullable|string|max:100',
        ]);
        $now = now()->toDateTimeString();

        Database::execute(
            'INSERT INTO services
             (icon,title,title_ar,summary,summary_ar,deliverables,deliverables_ar,sort_order,visible,created_at,updated_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)',
            [
                $request->icon ?: 'fa-solid fa-code',
                $request->title,   $request->title_ar,
                $request->summary, $request->summary_ar,
                $this->parseWorkStages($request->deliverables ?? ''),
                $this->parseWorkStages($request->deliverables_ar ?? ''),
                (int) ($request->sort_order ?? 0), $request->has('visible') ? 1 : 0, $now, $now,
            ]
        );

        return redirect()->route('admin.services')->with('success', __('Service created!'));
    }

    public function serviceEdit(int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $service = Database::first('SELECT * FROM services WHERE id = ?', [$id]);
        if (! $service) {
            abort(404);
        }
        $service->deliverables = json_decode($service->deliverables ?? '[]');
        $service->deliverables_ar = json_decode($service->deliverables_ar ?? '[]');
        $service->visible = (bool) $service->visible;

        return view('admin.services.form', compact('service'));
    }

    public function serviceUpdate(Request $request, int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'icon' => 'nullable|string|max:100',
        ]);

        Database::execute(
            'UPDATE services SET
             icon=?,title=?,title_ar=?,summary=?,summary_ar=?,deliverables=?,deliverables_ar=?,
             sort_order=?,visible=?,updated_at=?
             WHERE id=?',
            [
                $request->icon ?: 'fa-solid fa-code',
                $request->title,   $request->title_ar,
                $request->summary, $request->summary_ar,
                $this->parseWorkStages($request->deliverables ?? ''),
                $this->parseWorkStages($request->deliverables_ar ?? ''),
                (int) ($request->sort_order ?? 0), $request->has('visible') ? 1 : 0,
                now()->toDateTimeString(), $id,
            ]
        );

        return redirect()->route('admin.services')->with('success', __('Service updated!'));
    }

    public function serviceDelete(int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        Database::execute('DELETE FROM services WHERE id = ?', [$id]);

        return redirect()->route('admin.services')->with('success', __('Service deleted.'));
    }

    // ─── Experience ───
    public function experiences()
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $experiences = Database::query('SELECT * FROM experiences ORDER BY sort_order ASC');
        foreach ($experiences as $e) {
            $e->tags = json_decode($e->tags ?? '[]');
            $e->visible = (bool) $e->visible;
        }

        return view('admin.experience.index', compact('experiences'));
    }

    public function experienceCreate()
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }

        return view('admin.experience.form', ['experience' => null]);
    }

    public function experienceStore(Request $request)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'date_range' => 'required|string|max:100',
            'description' => 'required|string',
        ]);
        $tags = $request->tags ? json_encode(array_map('trim', explode(',', $request->tags))) : '[]';
        $visible = $request->has('visible') ? 1 : 0;
        $now = now()->toDateTimeString();

        Database::execute(
            'INSERT INTO experiences
             (title,title_ar,company,company_ar,date_range,description,description_ar,tags,sort_order,visible,created_at,updated_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $request->title,   $request->title_ar,
                $request->company, $request->company_ar,
                $request->date_range,
                $request->description, $request->description_ar,
                $tags, (int) ($request->sort_order ?? 0), $visible, $now, $now,
            ]
        );

        return redirect()->route('admin.experience')->with('success', 'Experience created!');
    }

    public function experienceEdit(int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $experience = Database::first('SELECT * FROM experiences WHERE id = ?', [$id]);
        if (! $experience) {
            abort(404);
        }
        $experience->tags = json_decode($experience->tags ?? '[]');
        $experience->visible = (bool) $experience->visible;

        return view('admin.experience.form', compact('experience'));
    }

    public function experienceUpdate(Request $request, int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'date_range' => 'required|string|max:100',
            'description' => 'required|string',
        ]);
        $tags = $request->tags ? json_encode(array_map('trim', explode(',', $request->tags))) : '[]';
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
                $tags, (int) ($request->sort_order ?? 0), $visible,
                now()->toDateTimeString(), $id,
            ]
        );

        return redirect()->route('admin.experience')->with('success', 'Experience updated!');
    }

    public function experienceDelete(int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        Database::execute('DELETE FROM experiences WHERE id = ?', [$id]);

        return redirect()->route('admin.experience')->with('success', 'Experience deleted.');
    }

    // ─── Skills ───
    public function skills()
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $catRows = Database::query('SELECT * FROM skill_categories ORDER BY sort_order ASC');
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
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate(['name' => 'required', 'type' => 'required']);
        $now = now()->toDateTimeString();
        Database::execute(
            'INSERT INTO skill_categories (name,name_ar,icon,type,sort_order,created_at,updated_at) VALUES (?,?,?,?,?,?,?)',
            [$request->name, $request->name_ar, $request->icon ?? '⚙️', $request->type, (int) ($request->sort_order ?? 0), $now, $now]
        );

        return back()->with('success', 'Category created!');
    }

    public function skillCategoryDelete(int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        Database::execute('DELETE FROM skills WHERE skill_category_id = ?', [$id]);
        Database::execute('DELETE FROM skill_categories WHERE id = ?', [$id]);

        return back()->with('success', 'Category deleted.');
    }

    public function skillStore(Request $request)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate(['name' => 'required', 'skill_category_id' => 'required']);
        $now = now()->toDateTimeString();
        Database::execute(
            'INSERT INTO skills (skill_category_id,name,name_ar,percentage,sort_order,created_at,updated_at) VALUES (?,?,?,?,?,?,?)',
            [$request->skill_category_id, $request->name, $request->name_ar,
                $request->percentage ?: null, (int) ($request->sort_order ?? 0), $now, $now]
        );

        return back()->with('success', 'Skill added!');
    }

    public function skillDelete(int $id)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        Database::execute('DELETE FROM skills WHERE id = ?', [$id]);

        return back()->with('success', 'Skill deleted.');
    }

    // ─── Settings ───
    public function settings()
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $settings = $this->getSettings();

        return view('admin.settings', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        if (! $this->auth()) {
            return redirect()->route('admin.login');
        }
        $request->validate([
            'cv_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);
        $fields = [
            'hero_name',    'hero_name_ar',
            'hero_tagline', 'hero_tagline_ar',
            'hero_subtitle', 'hero_subtitle_ar',
            'hero_stat_years', 'hero_stat_projects', 'hero_stat_clients',
            'about_heading', 'about_heading_ar',
            'about_p1',     'about_p1_ar',
            'about_p2',     'about_p2_ar',
            'about_p3',     'about_p3_ar',
            'about_tags',   'about_tags_ar',
            'email', 'whatsapp_number', 'github_url', 'linkedin_url',
            'footer_text',  'footer_text_ar',
            'seo_title', 'seo_title_ar',
            'seo_description', 'seo_description_ar',
        ];
        $now = now()->toDateTimeString();
        foreach ($fields as $field) {
            $val = $request->input($field, '');
            Database::execute(
                'INSERT OR REPLACE INTO settings (key, value, created_at, updated_at) VALUES (?,?,?,?)',
                [$field, $val, $now, $now]
            );
        }

        $cvPath = $this->resolveCvUpload($request);
        if ($cvPath !== null) {
            Database::execute(
                'INSERT OR REPLACE INTO settings (key, value, created_at, updated_at) VALUES (?,?,?,?)',
                ['cv_path', $cvPath, $now, $now]
            );
        }

        return back()->with('success', __('Settings saved!'));
    }

    /**
     * Store an uploaded CV in public/cv and return its public path, '' to remove it, or null to keep the current one.
     */
    private function resolveCvUpload(Request $request): ?string
    {
        if ($request->hasFile('cv_file')) {
            $fileName = Str::slug(pathinfo($request->file('cv_file')->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'cv';
            $request->file('cv_file')->move(public_path('cv'), $fileName.'.pdf');

            return 'cv/'.$fileName.'.pdf';
        }

        if ($request->boolean('remove_cv')) {
            return '';
        }

        return null;
    }
}
