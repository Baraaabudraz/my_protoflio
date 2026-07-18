<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Experience;
use App\Models\SkillCategory;
use App\Models\Skill;
use App\Models\Setting;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // Settings
        $settings = [
            'hero_name'           => 'Baraa M. Abu Draz',
            'hero_tagline'        => 'Backend Engineer',
            'hero_subtitle'       => 'Passionate Backend Engineer with <strong>5+ years</strong> of experience designing scalable web applications using <strong>Laravel</strong>, PHP, and modern development practices. Building robust backend solutions that create real-world impact.',
            'hero_stat_years'     => '5+',
            'hero_stat_projects'  => '30+',
            'hero_stat_clients'   => '15+',
            'about_heading'       => 'Crafting Scalable Backend Solutions',
            'about_p1'            => "I'm Baraa M. Abu Draz, a Passionate Backend Engineer with over 5 years of experience designing and developing scalable web applications. I specialize in Laravel and PHP, applying modern development practices to build robust, maintainable systems.",
            'about_p2'            => 'My focus is on delivering clean, well-architected backend solutions — from REST API design and database optimization to deployment pipelines and team collaboration. I thrive in cross-functional environments where engineering excellence meets real-world impact.',
            'about_p3'            => "Whether it's building a complex SaaS platform, designing a high-performance API, or mentoring a team, I bring precision and passion to every project.",
            'about_tags'          => 'Laravel, PHP 8, REST APIs, MySQL, Redis, Docker, Git, Linux',
            'email'               => 'abudrazbaraa@gmail.com',
            'github_url'          => 'https://github.com/',
            'linkedin_url'        => 'https://linkedin.com/',
            'footer_text'         => 'Crafted with ❤️ by Baraa M. Abu Draz · Backend Engineer · 2026',
        ];
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        // Projects
        $projects = [
            ['title' => 'Multi-Tenant SaaS Platform', 'description' => 'A fully-featured multi-tenant SaaS application built with Laravel, handling per-tenant database isolation, subscription billing via Stripe, role-based access control, and a comprehensive REST API consumed by a Vue.js frontend.', 'icon' => '🏗️', 'stack' => ['Laravel 10', 'PHP 8.2', 'MySQL', 'Redis', 'Vue.js', 'Stripe', 'Docker'], 'featured' => true, 'sort_order' => 0],
            ['title' => 'E-Commerce API Platform', 'description' => 'Headless e-commerce backend with product catalog management, cart, checkout, real-time inventory tracking, and order fulfillment. Optimized for high-traffic scenarios.', 'icon' => '🛒', 'stack' => ['Laravel', 'REST API', 'PostgreSQL', 'Redis', 'Queue'], 'featured' => false, 'sort_order' => 1],
            ['title' => 'Healthcare Management System', 'description' => 'Patient record management, appointment scheduling, and medical reporting system. Features secure data handling, PDF generation, and SMS/email notifications.', 'icon' => '🏥', 'stack' => ['Laravel', 'Livewire', 'MySQL', 'Twilio', 'DomPDF'], 'featured' => false, 'sort_order' => 2],
            ['title' => 'Real-Time Analytics Dashboard', 'description' => 'Live business intelligence dashboard with WebSocket integration using Laravel Echo and Pusher. Displays real-time KPIs, sales data, and user behavior metrics.', 'icon' => '📊', 'stack' => ['Laravel', 'WebSockets', 'Pusher', 'Vue.js', 'Chart.js'], 'featured' => false, 'sort_order' => 3],
            ['title' => 'Auth & Permission Microservice', 'description' => 'Standalone authentication and authorization microservice with JWT, OAuth2, fine-grained role/permission management, and API rate limiting built for distributed architectures.', 'icon' => '🔐', 'stack' => ['Laravel Passport', 'JWT', 'OAuth2', 'Spatie', 'Redis'], 'featured' => false, 'sort_order' => 4],
        ];
        foreach ($projects as $p) {
            Project::create(array_merge($p, ['visible' => true, 'github_url' => null, 'live_url' => null]));
        }

        // Experience
        $experiences = [
            ['title' => 'Senior Backend Engineer', 'company' => 'Tech Company / SaaS Platform', 'date_range' => '2022 – Present', 'description' => 'Led backend architecture for a multi-tenant SaaS platform serving thousands of users. Designed and implemented RESTful APIs, optimized database queries cutting response times by 60%, and mentored junior developers on Laravel best practices.', 'tags' => ['Laravel', 'PHP 8', 'MySQL', 'Redis', 'Docker', 'REST API'], 'sort_order' => 0],
            ['title' => 'Backend Developer', 'company' => 'Digital Agency', 'date_range' => '2020 – 2022', 'description' => "Built and maintained web applications for various clients across e-commerce, healthcare, and education sectors. Developed payment gateway integrations, automated workflows with Laravel queues, and implemented robust authentication systems.", 'tags' => ['Laravel', 'Vue.js', 'PostgreSQL', 'Stripe API', 'AWS'], 'sort_order' => 1],
            ['title' => 'PHP Developer', 'company' => 'Startup / Freelance', 'date_range' => '2018 – 2020', 'description' => 'Started career building custom Laravel applications and WordPress solutions for local businesses. Developed strong foundations in OOP, MVC architecture, and database design while delivering projects on tight deadlines.', 'tags' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Git'], 'sort_order' => 2],
        ];
        foreach ($experiences as $e) {
            Experience::create(array_merge($e, ['visible' => true]));
        }

        // Skills
        $categories = [
            ['name' => 'Backend Development', 'icon' => '⚙️', 'type' => 'bars', 'sort_order' => 0, 'skills' => [
                ['name' => 'Laravel / PHP', 'percentage' => 95],
                ['name' => 'RESTful API Design', 'percentage' => 92],
                ['name' => 'OOP & Design Patterns', 'percentage' => 90],
                ['name' => 'Microservices', 'percentage' => 78],
            ]],
            ['name' => 'Database & Caching', 'icon' => '🗄️', 'type' => 'bars', 'sort_order' => 1, 'skills' => [
                ['name' => 'MySQL / PostgreSQL', 'percentage' => 90],
                ['name' => 'Redis / Caching', 'percentage' => 85],
                ['name' => 'Query Optimization', 'percentage' => 88],
                ['name' => 'Eloquent ORM', 'percentage' => 95],
            ]],
            ['name' => 'DevOps & Tools', 'icon' => '🚀', 'type' => 'bars', 'sort_order' => 2, 'skills' => [
                ['name' => 'Git / GitHub', 'percentage' => 92],
                ['name' => 'Docker', 'percentage' => 80],
                ['name' => 'Linux / CLI', 'percentage' => 85],
                ['name' => 'CI/CD Pipelines', 'percentage' => 75],
            ]],
            ['name' => 'Frontend & Other', 'icon' => '🎨', 'type' => 'tags', 'sort_order' => 3, 'skills' => [
                ['name' => 'JavaScript', 'percentage' => null],
                ['name' => 'Vue.js', 'percentage' => null],
                ['name' => 'HTML/CSS', 'percentage' => null],
                ['name' => 'Blade', 'percentage' => null],
                ['name' => 'Livewire', 'percentage' => null],
                ['name' => 'Alpine.js', 'percentage' => null],
                ['name' => 'Postman', 'percentage' => null],
                ['name' => 'Nginx', 'percentage' => null],
                ['name' => 'AWS', 'percentage' => null],
                ['name' => 'PHPUnit', 'percentage' => null],
                ['name' => 'Swagger', 'percentage' => null],
                ['name' => 'GraphQL', 'percentage' => null],
            ]],
        ];

        foreach ($categories as $i => $cat) {
            $skills = $cat['skills'];
            unset($cat['skills']);
            $category = SkillCategory::create($cat);
            foreach ($skills as $j => $skill) {
                Skill::create(array_merge($skill, ['skill_category_id' => $category->id, 'sort_order' => $j]));
            }
        }
    }
}
