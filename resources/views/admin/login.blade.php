<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('Sign in') }} — CMS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('admin-theme'); } catch (e) {}
            var theme = saved === 'light' || saved === 'dark' ? saved : (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root, [data-theme="dark"] { --bg:#0b1020; --card:#111a2e; --border:#1f2b45; --text:#e6ebf5; --muted:#8793ab; --cyan:#22d3ee; --cyan-dim:rgba(34,211,238,.12); --danger:#f87171; --on-accent:#04121a; --gradient:linear-gradient(135deg,#22d3ee,#3b82f6); --shadow:0 30px 70px rgba(0,0,0,.45); }
        [data-theme="light"] { --bg:#f4f6fb; --card:#ffffff; --border:#e3e8f0; --text:#111827; --muted:#5b6678; --cyan:#0e7490; --cyan-dim:rgba(8,145,178,.1); --danger:#dc2626; --on-accent:#ffffff; --gradient:linear-gradient(135deg,#0891b2,#2563eb); --shadow:0 30px 70px rgba(15,23,42,.12); }
        :root { --font-body: {!! $isRtl ? "'IBM Plex Sans Arabic', sans-serif" : "'Inter', sans-serif" !!}; --font-head: {!! $isRtl ? "'IBM Plex Sans Arabic', sans-serif" : "'Plus Jakarta Sans', sans-serif" !!}; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:var(--font-body); background:var(--bg); color:var(--text); min-height:100vh; display:grid; place-items:center; padding:1.5rem; -webkit-font-smoothing:antialiased; position:relative; overflow:hidden; }
        body::before, body::after { content:''; position:fixed; border-radius:50%; filter:blur(80px); opacity:.35; z-index:-1; }
        body::before { width:420px; height:420px; background:#22d3ee; top:-160px; inset-inline-end:-120px; }
        body::after { width:380px; height:380px; background:#3b82f6; bottom:-160px; inset-inline-start:-120px; }
        :where(a, button, input):focus-visible { outline:2px solid var(--cyan); outline-offset:2px; }
        .login-box { width:100%; max-width:420px; }
        .login-card { background:var(--card); border:1px solid var(--border); border-radius:24px; padding:2.4rem 2.2rem; box-shadow:var(--shadow); }
        .login-head { text-align:center; margin-bottom:2rem; }
        .login-head img { width:76px; height:76px; border-radius:22px; object-fit:cover; object-position:50% 22%; border:3px solid var(--cyan-dim); margin:0 auto 1rem; display:block; }
        .login-head h1 { font:800 1.5rem var(--font-head); }
        .login-head p { color:var(--muted); font-size:.92rem; margin-top:.3rem; }
        .form-label { display:block; font-size:.85rem; font-weight:600; margin-bottom:.5rem; }
        .input-wrap { position:relative; }
        .input-wrap > i { position:absolute; top:50%; inset-inline-start:1rem; transform:translateY(-50%); color:var(--muted); font-size:.9rem; pointer-events:none; }
        .form-control { width:100%; height:50px; padding-inline:2.75rem 3rem; background:var(--bg); border:1px solid var(--border); border-radius:12px; color:var(--text); font:1rem var(--font-body); outline:none; transition:border-color .2s, box-shadow .2s; }
        .form-control:focus { border-color:var(--cyan); box-shadow:0 0 0 4px var(--cyan-dim); }
        .toggle-pass { position:absolute; top:50%; inset-inline-end:.4rem; transform:translateY(-50%); width:40px; height:40px; border:none; background:none; color:var(--muted); cursor:pointer; border-radius:10px; }
        .toggle-pass:hover { color:var(--cyan); background:var(--cyan-dim); }
        .btn-login { width:100%; height:50px; margin-top:1.4rem; display:flex; align-items:center; justify-content:center; gap:.6rem; background:var(--gradient); border:none; border-radius:12px; color:var(--on-accent); font:700 1rem var(--font-body); cursor:pointer; transition:filter .2s, transform .2s; }
        .btn-login:hover { filter:brightness(1.07); transform:translateY(-1px); }
        .alert { display:flex; gap:.6rem; align-items:flex-start; padding:.8rem 1rem; border-radius:12px; margin-bottom:1.25rem; font-size:.9rem; color:var(--danger); background:color-mix(in srgb, var(--danger) 10%, transparent); border:1px solid color-mix(in srgb, var(--danger) 30%, transparent); }
        .alert i { margin-top:.2rem; }
        .login-foot { display:flex; justify-content:space-between; align-items:center; margin-top:1.4rem; font-size:.88rem; }
        .login-foot a { color:var(--muted); text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; }
        .login-foot a:hover { color:var(--cyan); }
        .lang-links { display:flex; gap:.35rem; }
        .lang-links a { padding:.3rem .6rem; border-radius:8px; }
        .lang-links a.active { color:var(--cyan); background:var(--cyan-dim); font-weight:700; }
    </style>
</head>
<body>
<main class="login-box">
    <div class="login-card">
        <div class="login-head">
            <img src="{{ asset('images/me-thumb.webp') }}" alt="" width="76" height="76">
            <h1>{{ __('Welcome back') }}</h1>
            <p>{{ __('Sign in to manage your portfolio') }}</p>
        </div>
        @if(session('error'))
            <div class="alert" role="alert"><i class="fas fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <label class="form-label" for="password">{{ __('Admin Password') }}</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" autocomplete="current-password" autofocus required>
                <button type="button" class="toggle-pass" id="togglePass" aria-label="{{ __('Show password') }}" aria-pressed="false"><i class="fas fa-eye"></i></button>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-right-to-bracket"></i> {{ __('Sign in') }}</button>
        </form>
    </div>
    <div class="login-foot">
        <a href="{{ url('/') }}"><i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}"></i> {{ __('Back to Portfolio') }}</a>
        <div class="lang-links">
            <a href="{{ route('lang.switch', 'en') }}" class="{{ $locale === 'en' ? 'active' : '' }}" lang="en">EN</a>
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ $locale === 'ar' ? 'active' : '' }}" lang="ar">عربي</a>
        </div>
    </div>
</main>
<script>
document.getElementById('togglePass').addEventListener('click', function () {
    const input = document.getElementById('password');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    this.setAttribute('aria-pressed', show);
    this.innerHTML = show ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
});
</script>
</body>
</html>
