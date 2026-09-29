@extends('admin.layout')
@section('title', $service ? __('Edit Service') : __('Add Service'))

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
.icon-preview { width:42px; height:42px; border-radius:10px; background:var(--cyan-dim); color:var(--cyan); display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
</style>
@endsection

@section('topbar-actions')
    <a href="{{ route('admin.services') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="card" style="max-width:780px">
    <div class="card-header">
        <span class="card-title">{{ $service ? __('Edit:').' '.$service->title : __('Add Service') }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $service ? route('admin.services.update', $service->id) : route('admin.services.store') }}">
            @csrf
            @if($service) @method('PUT') @endif

            @if($errors->any())
                <div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif

            {{-- Shared fields --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('Icon') }}</label>
                    <div style="display:flex;gap:0.6rem;align-items:center">
                        <div class="icon-preview"><i id="iconPreview" class="{{ old('icon', $service?->icon ?? 'fa-solid fa-code') }}"></i></div>
                        <input type="text" name="icon" id="iconInput" class="form-control" value="{{ old('icon', $service?->icon ?? 'fa-solid fa-code') }}" placeholder="fa-solid fa-gauge-high" dir="ltr">
                    </div>
                    <div class="form-hint">{{ __('Font Awesome class, e.g. fa-solid fa-gauge-high') }} · <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" style="color:var(--cyan)">fontawesome.com</a></div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Sort Order') }}</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $service?->sort_order ?? 0) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="visible" {{ old('visible', $service === null || $service?->visible) ? 'checked' : '' }}>
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
                <div class="form-group">
                    <label class="form-label">Service Title * <span class="lang-badge en">EN</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $service?->title) }}" placeholder="Performance & Database Optimization" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Summary * <span class="lang-badge en">EN</span></label>
                    <textarea name="summary" class="form-control" style="min-height:90px" placeholder="What problem does it solve for the client, and what result do they get?" required>{{ old('summary', $service?->summary) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">What's included <span class="lang-badge en">EN</span></label>
                    <textarea name="deliverables" class="form-control" placeholder="One deliverable per line">{{ old('deliverables', $service ? implode("\n", $service->deliverables ?? []) : '') }}</textarea>
                    <div class="form-hint">One item per line</div>
                </div>
            </div>

            {{-- AR Panel --}}
            <div class="bi-panel" id="panel-ar">
                <div style="background:rgba(0,212,212,0.04);border:1px solid rgba(0,212,212,0.12);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1rem;font-size:0.8rem;color:var(--muted)">
                    <i class="fas fa-circle-info" style="color:var(--cyan)"></i>
                    الحقول العربية اختيارية — إذا تُركت فارغة سيُعرض المحتوى الإنجليزي.
                </div>
                <div class="form-group">
                    <label class="form-label">اسم الخدمة <span class="lang-badge ar">AR</span></label>
                    <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $service?->title_ar) }}" placeholder="مثال: تحسين الأداء وقواعد البيانات" dir="rtl" style="font-family:'Cairo',sans-serif">
                </div>
                <div class="form-group">
                    <label class="form-label">الوصف المختصر <span class="lang-badge ar">AR</span></label>
                    <textarea name="summary_ar" class="form-control" placeholder="ما المشكلة التي تحلها للعميل، وما النتيجة التي يحصل عليها؟" dir="rtl" style="min-height:90px;font-family:'Cairo',sans-serif">{{ old('summary_ar', $service?->summary_ar) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">ماذا تتضمن الخدمة <span class="lang-badge ar">AR</span></label>
                    <textarea name="deliverables_ar" class="form-control" placeholder="عنصر واحد في كل سطر" dir="rtl" style="font-family:'Cairo',sans-serif">{{ old('deliverables_ar', $service ? implode("\n", $service->deliverables_ar ?? []) : '') }}</textarea>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $service ? __('Save') : __('Add Service') }}</button>
                <a href="{{ route('admin.services') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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
document.getElementById('iconInput').addEventListener('input', e => {
    document.getElementById('iconPreview').className = e.target.value;
});
</script>
@endsection
