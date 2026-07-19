@extends('admin.layout')
@section('title', $experience ? __('Experience') : __('Add Experience'))

@section('styles')
<style>
.bi-tabs { display:flex; gap:0; border:1px solid var(--border); border-radius:8px; overflow:hidden; margin-bottom:1.5rem; }
.bi-tab  { flex:1; padding:0.6rem 1rem; background:transparent; border:none; font-family:inherit; font-size:0.82rem; font-weight:600; color:var(--muted); cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:0.4rem; }
.bi-tab.active { background:var(--cyan-dim); color:var(--cyan); }
.bi-tab:not(.active):hover { background:rgba(255,255,255,0.04); color:var(--text); }
.bi-panel { display:none; }
.bi-panel.active { display:block; }
.lang-badge { display:inline-block; padding:0.15rem 0.45rem; border-radius:4px; font-size:0.68rem; font-weight:700; margin-inline-start:0.25rem; }
.lang-badge.en { background:rgba(59,130,246,0.15); color:#60a5fa; }
.lang-badge.ar { background:rgba(0,212,212,0.12); color:var(--cyan); }
</style>
@endsection

@section('topbar-actions')
    <a href="{{ route('admin.experience') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="card" style="max-width:780px">
    <div class="card-header">
        <span class="card-title">{{ $experience ? __('Edit:').' '.$experience->title : __('Add Experience Entry') }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $experience ? route('admin.experience.update', $experience->id) : route('admin.experience.store') }}">
            @csrf
            @if($experience) @method('PUT') @endif

            @if($errors->any())
                <div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif

            {{-- Shared fields --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الفترة الزمنية *' : 'Date Range *' }}</label>
                    <input type="text" name="date_range" class="form-control" value="{{ old('date_range', $experience?->date_range) }}" placeholder="2022 – Present" required>
                    <div class="form-hint">Same in both languages</div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Sort Order') }}</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $experience?->sort_order ?? 0) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الوسوم / التقنيات' : 'Tags / Tech Used' }}</label>
                <input type="text" name="tags" class="form-control" value="{{ old('tags', $experience ? implode(', ', $experience->tags ?? []) : '') }}" placeholder="Laravel, PHP, MySQL">
                <div class="form-hint">{{ app()->getLocale() === 'ar' ? 'مفصولة بفواصل · نفس القيمة بكلتا اللغتين' : 'Comma-separated · same in both languages' }}</div>
            </div>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="visible" {{ old('visible', $experience === null || $experience?->visible) ? 'checked' : '' }}>
                    <span>{{ app()->getLocale() === 'ar' ? 'مرئي في المحفظة' : 'Visible on portfolio' }}</span>
                </label>
            </div>

            {{-- Language Tabs --}}
            <div class="bi-tabs">
                <button type="button" class="bi-tab active" onclick="switchLang('en',this)">🇬🇧 English <span class="lang-badge en">EN</span></button>
                <button type="button" class="bi-tab"        onclick="switchLang('ar',this)">🇸🇦 العربية <span class="lang-badge ar">AR</span></button>
            </div>

            {{-- EN Panel --}}
            <div class="bi-panel active" id="panel-en">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Job Title * <span class="lang-badge en">EN</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $experience?->title) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company * <span class="lang-badge en">EN</span></label>
                        <input type="text" name="company" class="form-control" value="{{ old('company', $experience?->company) }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description * <span class="lang-badge en">EN</span></label>
                    <textarea name="description" class="form-control" required>{{ old('description', $experience?->description) }}</textarea>
                </div>
            </div>

            {{-- AR Panel --}}
            <div class="bi-panel" id="panel-ar">
                <div style="background:rgba(0,212,212,0.04);border:1px solid rgba(0,212,212,0.12);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1rem;font-size:0.8rem;color:var(--muted)">
                    <i class="fas fa-circle-info" style="color:var(--cyan)"></i>
                    Arabic fields are optional — if left empty, English content will be shown.
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">المسمى الوظيفي <span class="lang-badge ar">AR</span></label>
                        <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $experience?->title_ar) }}" placeholder="مثال: مهندس برمجيات أول" dir="rtl" style="font-family:'Cairo',sans-serif">
                    </div>
                    <div class="form-group">
                        <label class="form-label">الشركة <span class="lang-badge ar">AR</span></label>
                        <input type="text" name="company_ar" class="form-control" value="{{ old('company_ar', $experience?->company_ar) }}" placeholder="اسم الشركة بالعربية" dir="rtl" style="font-family:'Cairo',sans-serif">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">الوصف <span class="lang-badge ar">AR</span></label>
                    <textarea name="description_ar" class="form-control" placeholder="وصف المهام والإنجازات..." dir="rtl" style="font-family:'Cairo',sans-serif">{{ old('description_ar', $experience?->description_ar) }}</textarea>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $experience ? __('Save') : __('Add Entry') }}</button>
                <a href="{{ route('admin.experience') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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
