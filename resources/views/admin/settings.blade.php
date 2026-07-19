@extends('admin.layout')
@section('title', __('Settings'))

@section('styles')
<style>
.bi-tabs { display:flex; gap:0; border:1px solid var(--border); border-radius:8px; overflow:hidden; margin-bottom:1.5rem; }
.bi-tab  { flex:1; padding:0.6rem 1rem; background:transparent; border:none; font-family:inherit; font-size:0.82rem; font-weight:600; color:var(--muted); cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:0.4rem; }
.bi-tab.active { background:var(--cyan-dim); color:var(--cyan); }
.bi-tab:not(.active):hover { background:rgba(255,255,255,0.04); color:var(--text); }
.lang-badge { display:inline-block; padding:0.15rem 0.45rem; border-radius:4px; font-size:0.68rem; font-weight:700; margin-inline-start:0.25rem; }
.lang-badge.en { background:rgba(59,130,246,0.15); color:#60a5fa; }
.lang-badge.ar { background:rgba(0,212,212,0.12); color:var(--cyan); }
.ar-input { direction:rtl; font-family:'Cairo',sans-serif; }
</style>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    {{-- Language Tabs --}}
    <div class="bi-tabs" style="max-width:300px;margin-bottom:1.5rem">
        <button type="button" class="bi-tab active" onclick="switchSettings('en',this)">🇬🇧 English</button>
        <button type="button" class="bi-tab"        onclick="switchSettings('ar',this)">🇸🇦 العربية</button>
    </div>

    {{-- EN Settings --}}
    <div id="settings-en" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">

        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-house" style="color:var(--cyan)"></i> &nbsp;{{ __('Hero Section') }} <span class="lang-badge en">EN</span></span></div>
            <div class="card-body">
                <div class="form-group"><label class="form-label">{{ __('Full Name') }}</label><input type="text" name="hero_name" class="form-control" value="{{ $settings['hero_name'] ?? '' }}"></div>
                <div class="form-group"><label class="form-label">{{ __('Tagline / Role') }}</label><input type="text" name="hero_tagline" class="form-control" value="{{ $settings['hero_tagline'] ?? '' }}" placeholder="Backend Engineer"></div>
                <div class="form-group"><label class="form-label">{{ __('Subtitle') }}</label><textarea name="hero_subtitle" class="form-control" style="min-height:80px">{{ $settings['hero_subtitle'] ?? '' }}</textarea></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">{{ __('Years of Exp.') }}</label><input type="text" name="hero_stat_years" class="form-control" value="{{ $settings['hero_stat_years'] ?? '5+' }}"></div>
                    <div class="form-group"><label class="form-label">{{ __('Projects Stat') }}</label><input type="text" name="hero_stat_projects" class="form-control" value="{{ $settings['hero_stat_projects'] ?? '30+' }}"></div>
                </div>
                <div class="form-group"><label class="form-label">{{ __('Clients Stat') }}</label><input type="text" name="hero_stat_clients" class="form-control" value="{{ $settings['hero_stat_clients'] ?? '15+' }}"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-user" style="color:var(--cyan)"></i> &nbsp;{{ __('About Section') }} <span class="lang-badge en">EN</span></span></div>
            <div class="card-body">
                <div class="form-group"><label class="form-label">{{ __('Section Heading') }}</label><input type="text" name="about_heading" class="form-control" value="{{ $settings['about_heading'] ?? '' }}"></div>
                <div class="form-group"><label class="form-label">{{ __('Paragraph 1') }}</label><textarea name="about_p1" class="form-control">{{ $settings['about_p1'] ?? '' }}</textarea></div>
                <div class="form-group"><label class="form-label">{{ __('Paragraph 2') }}</label><textarea name="about_p2" class="form-control">{{ $settings['about_p2'] ?? '' }}</textarea></div>
                <div class="form-group"><label class="form-label">{{ __('Paragraph 3') }}</label><textarea name="about_p3" class="form-control">{{ $settings['about_p3'] ?? '' }}</textarea></div>
                <div class="form-group"><label class="form-label">{{ __('Tags (comma-separated)') }}</label><input type="text" name="about_tags" class="form-control" value="{{ $settings['about_tags'] ?? '' }}" placeholder="Laravel, PHP, MySQL"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-link" style="color:var(--cyan)"></i> &nbsp;{{ __('Contact & Links') }}</span></div>
            <div class="card-body">
                <div class="form-hint" style="margin-bottom:1rem"><i class="fas fa-circle-info" style="color:var(--cyan)"></i> These are shared between both languages.</div>
                <div class="form-group"><label class="form-label">{{ __('Email Address') }}</label><input type="email" name="email" class="form-control" value="{{ $settings['email'] ?? '' }}"></div>
                <div class="form-group"><label class="form-label">{{ __('GitHub URL') }}</label><input type="url" name="github_url" class="form-control" value="{{ $settings['github_url'] ?? '' }}"></div>
                <div class="form-group"><label class="form-label">{{ __('LinkedIn URL') }}</label><input type="url" name="linkedin_url" class="form-control" value="{{ $settings['linkedin_url'] ?? '' }}"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-ellipsis" style="color:var(--cyan)"></i> &nbsp;{{ __('Footer') }} <span class="lang-badge en">EN</span></span></div>
            <div class="card-body">
                <div class="form-group"><label class="form-label">{{ __('Footer Text') }}</label><input type="text" name="footer_text" class="form-control" value="{{ $settings['footer_text'] ?? '' }}"></div>
            </div>
        </div>
    </div>

    {{-- AR Settings --}}
    <div id="settings-ar" style="display:none;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">

        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-house" style="color:var(--cyan)"></i> &nbsp;قسم الهيرو <span class="lang-badge ar">AR</span></span></div>
            <div class="card-body">
                <div style="background:rgba(0,212,212,0.04);border:1px solid rgba(0,212,212,0.12);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1rem;font-size:0.8rem;color:var(--muted)"><i class="fas fa-circle-info" style="color:var(--cyan)"></i> الحقول الفارغة ستستخدم النص الإنجليزي تلقائياً.</div>
                <div class="form-group"><label class="form-label">الاسم الكامل <span class="lang-badge ar">AR</span></label><input type="text" name="hero_name_ar" class="form-control ar-input" value="{{ $settings['hero_name_ar'] ?? '' }}" placeholder="براء م. أبو دراز"></div>
                <div class="form-group"><label class="form-label">اللقب / الدور <span class="lang-badge ar">AR</span></label><input type="text" name="hero_tagline_ar" class="form-control ar-input" value="{{ $settings['hero_tagline_ar'] ?? '' }}" placeholder="مهندس خلفية"></div>
                <div class="form-group"><label class="form-label">العنوان الفرعي <span class="lang-badge ar">AR</span></label><textarea name="hero_subtitle_ar" class="form-control ar-input" style="min-height:80px">{{ $settings['hero_subtitle_ar'] ?? '' }}</textarea></div>
                <div class="form-hint" style="margin-bottom:0.5rem">إحصائيات الأرقام (سنوات / مشاريع / عملاء) تُعرض كأرقام، لا حاجة لترجمتها.</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-user" style="color:var(--cyan)"></i> &nbsp;قسم عني <span class="lang-badge ar">AR</span></span></div>
            <div class="card-body">
                <div class="form-group"><label class="form-label">عنوان القسم <span class="lang-badge ar">AR</span></label><input type="text" name="about_heading_ar" class="form-control ar-input" value="{{ $settings['about_heading_ar'] ?? '' }}" placeholder="عن براء"></div>
                <div class="form-group"><label class="form-label">الفقرة الأولى <span class="lang-badge ar">AR</span></label><textarea name="about_p1_ar" class="form-control ar-input">{{ $settings['about_p1_ar'] ?? '' }}</textarea></div>
                <div class="form-group"><label class="form-label">الفقرة الثانية <span class="lang-badge ar">AR</span></label><textarea name="about_p2_ar" class="form-control ar-input">{{ $settings['about_p2_ar'] ?? '' }}</textarea></div>
                <div class="form-group"><label class="form-label">الفقرة الثالثة <span class="lang-badge ar">AR</span></label><textarea name="about_p3_ar" class="form-control ar-input">{{ $settings['about_p3_ar'] ?? '' }}</textarea></div>
                <div class="form-group"><label class="form-label">الوسوم (مفصولة بفواصل) <span class="lang-badge ar">AR</span></label><input type="text" name="about_tags_ar" class="form-control ar-input" value="{{ $settings['about_tags_ar'] ?? '' }}" placeholder="لارافيل، PHP، MySQL"></div>
            </div>
        </div>

        <div class="card" style="grid-column:1/-1">
            <div class="card-header"><span class="card-title"><i class="fas fa-ellipsis" style="color:var(--cyan)"></i> &nbsp;التذييل <span class="lang-badge ar">AR</span></span></div>
            <div class="card-body">
                <div class="form-group"><label class="form-label">نص التذييل <span class="lang-badge ar">AR</span></label><input type="text" name="footer_text_ar" class="form-control ar-input" value="{{ $settings['footer_text_ar'] ?? '' }}"></div>
            </div>
        </div>
    </div>

    <div style="margin-top:1.5rem">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> &nbsp;{{ __('Save All Settings') }}</button>
    </div>
</form>

<script>
function switchSettings(lang, btn) {
    document.querySelectorAll('.bi-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('settings-en').style.display = lang === 'en' ? 'grid' : 'none';
    document.getElementById('settings-ar').style.display = lang === 'ar' ? 'grid' : 'none';
}
</script>
@endsection
