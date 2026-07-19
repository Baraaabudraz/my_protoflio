@extends('admin.layout')
@section('title', $project ? __('Edit Project') : __('New Project'))

@section('styles')
<style>
.bi-tabs { display:flex; gap:0; border:1px solid var(--border); border-radius:8px; overflow:hidden; margin-bottom:1.5rem; }
.bi-tab  { flex:1; padding:0.6rem 1rem; background:transparent; border:none; font-family:inherit; font-size:0.82rem; font-weight:600; color:var(--muted); cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:0.4rem; }
.bi-tab.active { background:var(--cyan-dim); color:var(--cyan); }
.bi-tab:not(.active):hover { background:rgba(255,255,255,0.04); color:var(--text); }
.bi-panel { display:none; }
.bi-panel.active { display:block; }
.bi-panel[data-lang="ar"] .form-control { direction:rtl; font-family:'Cairo',sans-serif; }
.lang-badge { display:inline-block; padding:0.15rem 0.45rem; border-radius:4px; font-size:0.68rem; font-weight:700; margin-inline-start:0.25rem; }
.lang-badge.en { background:rgba(59,130,246,0.15); color:#60a5fa; }
.lang-badge.ar { background:rgba(0,212,212,0.12); color:var(--cyan); }
</style>
@endsection

@section('topbar-actions')
    <a href="{{ route('admin.projects') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="card" style="max-width:900px">
    <div class="card-header">
        <span class="card-title">{{ $project ? __('Edit Project').': '.$project->title : __('Add New Project') }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $project ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}">
            @csrf
            @if($project) @method('PUT') @endif

            @if($errors->any())
                <div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif

            {{-- ── BASIC INFO ── --}}
            <div class="section-divider">{{ __('Basic Info') }}</div>

            <div class="form-row" style="margin-bottom:1.25rem">
                <div class="form-group">
                    <label class="form-label">{{ __('Icon') }} (emoji)</label>
                    <input type="text" name="icon" class="form-control" value="{{ old('icon', $project?->icon ?? '🚀') }}" maxlength="5">
                </div>
                <div class="form-group" style="display:flex;gap:1.5rem;align-items:center;padding-top:1.5rem">
                    <label class="form-check"><input type="checkbox" name="featured" {{ old('featured', $project?->featured) ? 'checked' : '' }}><span>{{ __('Featured') }}</span></label>
                    <label class="form-check"><input type="checkbox" name="visible"  {{ old('visible', $project === null || $project?->visible) ? 'checked' : '' }}><span>{{ __('Visible') }}</span></label>
                </div>
            </div>

            {{-- Language Tabs --}}
            <div class="bi-tabs">
                <button type="button" class="bi-tab active" onclick="switchLang('en',this)">🇬🇧 English <span class="lang-badge en">EN</span></button>
                <button type="button" class="bi-tab"        onclick="switchLang('ar',this)">🇸🇦 العربية <span class="lang-badge ar">AR</span></button>
            </div>

            {{-- EN Panel --}}
            <div class="bi-panel active" id="panel-en" data-lang="en">
                <div class="form-group">
                    <label class="form-label">Title * <span class="lang-badge en">EN</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $project?->title) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Short Description * <span class="lang-badge en">EN</span></label>
                    <textarea name="description" class="form-control" rows="3" required>{{ old('description', $project?->description) }}</textarea>
                    <div class="form-hint">Shown on the project card in the portfolio grid.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Overview (Full Detail) <span class="lang-badge en">EN</span></label>
                    <textarea name="overview" class="form-control" rows="5" placeholder="Detailed project overview shown on the detail page...">{{ old('overview', $project?->overview) }}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Client <span class="lang-badge en">EN</span></label>
                        <input type="text" name="client" class="form-control" value="{{ old('client', $project?->client) }}" placeholder="e.g. Acme Corp.">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Duration <span class="lang-badge en">EN</span></label>
                        <input type="text" name="duration" class="form-control" value="{{ old('duration', $project?->duration) }}" placeholder="e.g. 3 months">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Category <span class="lang-badge en">EN</span></label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $project?->category) }}" placeholder="e.g. Web App, API, E-Commerce">
                    <div class="form-hint">Used to group related projects.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Work Stages (one per line) <span class="lang-badge en">EN</span></label>
                    <textarea name="work_stages" class="form-control" rows="6" placeholder="Requirements Analysis&#10;System Architecture Design&#10;Backend API Development&#10;Testing & QA&#10;Deployment & Handover">{{ old('work_stages', $project ? implode("\n", (array)($project->work_stages ?? [])) : '') }}</textarea>
                    <div class="form-hint">Each line becomes a numbered stage on the detail page.</div>
                </div>
            </div>

            {{-- AR Panel --}}
            <div class="bi-panel" id="panel-ar" data-lang="ar">
                <div style="background:rgba(0,212,212,0.04);border:1px solid rgba(0,212,212,0.12);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1rem;font-size:0.8rem;color:var(--muted)">
                    <i class="fas fa-circle-info" style="color:var(--cyan)"></i>
                    Arabic fields are optional — if left empty, the English content will be shown to Arabic visitors.
                </div>
                <div class="form-group">
                    <label class="form-label">العنوان <span class="lang-badge ar">AR</span></label>
                    <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $project?->title_ar) }}" placeholder="عنوان المشروع بالعربية" dir="rtl" style="font-family:'Cairo',sans-serif">
                </div>
                <div class="form-group">
                    <label class="form-label">وصف مختصر <span class="lang-badge ar">AR</span></label>
                    <textarea name="description_ar" class="form-control" rows="3" placeholder="وصف مختصر يظهر على بطاقة المشروع..." dir="rtl" style="font-family:'Cairo',sans-serif">{{ old('description_ar', $project?->description_ar) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">نظرة عامة تفصيلية <span class="lang-badge ar">AR</span></label>
                    <textarea name="overview_ar" class="form-control" rows="5" placeholder="نظرة عامة تفصيلية تظهر في صفحة المشروع..." dir="rtl" style="font-family:'Cairo',sans-serif">{{ old('overview_ar', $project?->overview_ar) }}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">العميل <span class="lang-badge ar">AR</span></label>
                        <input type="text" name="client_ar" class="form-control" value="{{ old('client_ar', $project?->client_ar) }}" placeholder="مثال: شركة أكمي" dir="rtl" style="font-family:'Cairo',sans-serif">
                    </div>
                    <div class="form-group">
                        <label class="form-label">المدة <span class="lang-badge ar">AR</span></label>
                        <input type="text" name="duration_ar" class="form-control" value="{{ old('duration_ar', $project?->duration_ar) }}" placeholder="مثال: 3 أشهر" dir="rtl" style="font-family:'Cairo',sans-serif">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">الفئة <span class="lang-badge ar">AR</span></label>
                    <input type="text" name="category_ar" class="form-control" value="{{ old('category_ar', $project?->category_ar) }}" placeholder="مثال: تطبيق ويب، API، تجارة إلكترونية" dir="rtl" style="font-family:'Cairo',sans-serif">
                </div>
                <div class="form-group">
                    <label class="form-label">مراحل العمل (مرحلة في كل سطر) <span class="lang-badge ar">AR</span></label>
                    <textarea name="work_stages_ar" class="form-control" rows="6" placeholder="تحليل المتطلبات&#10;تصميم المعمارية&#10;تطوير الواجهة الخلفية&#10;الاختبار والتقييم&#10;النشر والتسليم" dir="rtl" style="font-family:'Cairo',sans-serif">{{ old('work_stages_ar', $project ? implode("\n", (array)($project->work_stages_ar ?? [])) : '') }}</textarea>
                    <div class="form-hint" dir="rtl">كل سطر يصبح مرحلة مرقّمة في صفحة المشروع.</div>
                </div>
            </div>

            {{-- ── TECH & LINKS ── --}}
            <div class="section-divider">{{ __('Tech Stack & Links') }}</div>
            <div class="form-group">
                <label class="form-label">{{ __('Stack') }}</label>
                <input type="text" name="stack" class="form-control" value="{{ old('stack', $project ? implode(', ', (array)($project->stack ?? [])) : '') }}" placeholder="Laravel, MySQL, Redis, Vue.js">
                <div class="form-hint">Comma-separated · same in both languages</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">GitHub URL</label>
                    <input type="url" name="github_url" class="form-control" value="{{ old('github_url', $project?->github_url) }}" placeholder="https://github.com/...">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Live URL') }}</label>
                    <input type="url" name="live_url" class="form-control" value="{{ old('live_url', $project?->live_url) }}" placeholder="https://...">
                </div>
            </div>

            {{-- ── ORDER ── --}}
            <div class="section-divider">{{ __('Sort Order') }}</div>
            <div class="form-group" style="max-width:180px">
                <label class="form-label">{{ __('Sort Order') }}</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $project?->sort_order ?? 0) }}">
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $project ? __('Update Project') : __('Create Project') }}</button>
                <a href="{{ route('admin.projects') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
function switchLang(lang, btn) {
    document.querySelectorAll('.bi-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.bi-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + lang).classList.add('active');
}
</script>
@endsection
