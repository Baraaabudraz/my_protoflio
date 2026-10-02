{{--
    Shared design tokens (4 colour themes + fonts) for the public site.
    Include inside a <style> tag; expects $locale.
--}}
        /* ═════════ THEMES ═════════ */
        :root,
        [data-theme="light"] {
            --cyan: #00759c;
            --cyan-light: #00a8d6;
            --cyan-dark: #006d8f;
            --bg-primary: #f5f7fb;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #16202e;
            --text-secondary: #4b5567;
            --text-muted: #5c6a7e;
            --border: #e2e8f0;
            --gradient: linear-gradient(135deg, #0379a8, #2563eb);
            --on-accent: #ffffff;
            --nav-bg: rgba(255,255,255,0.8);
            --mobile-nav-bg: rgba(255,255,255,0.98);
            --shadow-sm: 0 4px 14px rgba(15,23,42,0.06);
            --shadow-md: 0 14px 34px rgba(15,23,42,0.09);
            --shadow-lg: 0 28px 60px rgba(15,23,42,0.13);
            --heading-gradient: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        }
        [data-theme="dark"] {
            --cyan: #00d4d4;
            --cyan-light: #00f5f5;
            --cyan-dark: #009999;
            --bg-primary: #0a0e17;
            --bg-secondary: #0e1522;
            --bg-card: #131c2e;
            --text-primary: #e8eaf0;
            --text-secondary: #a3acbd;
            --text-muted: #7c8799;
            --border: #1e2d45;
            --gradient: linear-gradient(135deg, #00d4d4, #0088cc);
            --on-accent: #051018;
            --nav-bg: rgba(10,14,23,0.8);
            --mobile-nav-bg: rgba(10,14,23,0.98);
            --shadow-sm: 0 4px 14px rgba(0,0,0,0.3);
            --shadow-md: 0 14px 34px rgba(0,0,0,0.35);
            --shadow-lg: 0 28px 60px rgba(0,0,0,0.5);
            --heading-gradient: linear-gradient(135deg, #fff 0%, var(--text-primary) 100%);
        }
        [data-theme="ocean"] {
            --cyan: #20e3c2;
            --cyan-light: #4dfcdb;
            --cyan-dark: #0fae95;
            --bg-primary: #031824;
            --bg-secondary: #04202d;
            --bg-card: #062c3c;
            --text-primary: #e6f7fa;
            --text-secondary: #9cc6d2;
            --text-muted: #6d9aa7;
            --border: #0d3a4b;
            --gradient: linear-gradient(135deg, #20e3c2, #2b8cff);
            --on-accent: #031824;
            --nav-bg: rgba(3,24,36,0.8);
            --mobile-nav-bg: rgba(3,24,36,0.98);
            --shadow-sm: 0 4px 14px rgba(0,0,0,0.3);
            --shadow-md: 0 14px 34px rgba(0,0,0,0.35);
            --shadow-lg: 0 28px 60px rgba(0,0,0,0.5);
            --heading-gradient: linear-gradient(135deg, #fff 0%, var(--text-primary) 100%);
        }
        [data-theme="sunset"] {
            --cyan: #ff7a59;
            --cyan-light: #ff9d7a;
            --cyan-dark: #e0552f;
            --bg-primary: #1a0f1a;
            --bg-secondary: #211224;
            --bg-card: #2c1830;
            --text-primary: #f5e6f0;
            --text-secondary: #c9adbf;
            --text-muted: #a088a5;
            --border: #3d2440;
            --gradient: linear-gradient(135deg, #ff7a59, #ff4d94);
            --on-accent: #1a0f1a;
            --nav-bg: rgba(26,15,26,0.8);
            --mobile-nav-bg: rgba(26,15,26,0.98);
            --shadow-sm: 0 4px 14px rgba(0,0,0,0.3);
            --shadow-md: 0 14px 34px rgba(0,0,0,0.35);
            --shadow-lg: 0 28px 60px rgba(0,0,0,0.5);
            --heading-gradient: linear-gradient(135deg, #fff 0%, var(--text-primary) 100%);
        }
        :root {
            --font-body: {!! $locale === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" !!};
            --font-head: {!! $locale === 'ar' ? "'Cairo', sans-serif" : "'Plus Jakarta Sans', 'Inter', sans-serif" !!};
            --font-mono: 'JetBrains Mono', monospace;
            --accent-soft: color-mix(in srgb, var(--cyan) 10%, transparent);
            --accent-line: color-mix(in srgb, var(--cyan) 28%, transparent);
            --glass: color-mix(in srgb, var(--bg-card) 78%, transparent);
            --radius-lg: 28px;
            --radius-md: 20px;
            --radius-sm: 14px;
            --ease: cubic-bezier(.2,.7,.2,1);
        }

