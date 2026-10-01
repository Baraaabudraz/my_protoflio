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
        <form method="POST" enctype="multipart/form-data" action="{{ $project ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}">
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
            <div class="form-group">
                <label class="form-label">{{ __('Project Image') }}</label>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-upload"></i> {{ __('Upload from device') }}</label>
                        <input type="file" name="image_file" id="projectImageFile" class="form-control" accept="image/jpeg,image/png,image/webp">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fab fa-google-drive"></i> {{ __('Google Drive share link') }}</label>
                        <input type="text" name="image_url" id="projectImageUrl" class="form-control" value="{{ old('image_url', $project?->image) }}" placeholder="https://drive.google.com/file/d/...">
                    </div>
                </div>
                <div class="form-hint">{{ __('Choose a file from your device, or paste a Google Drive share link. Drive files must be shared as Anyone with the link.') }}</div>
                @if($project?->image)
                    <label class="form-check" style="margin-top:0.75rem">
                        <input type="checkbox" name="remove_image" value="1">
                        <span>{{ __('Remove current image') }}</span>
                    </label>
                @endif
                @php
                    $existingProjectImage = old('image_url', $project?->image ? project_image_url($project->image) : '');
                @endphp
                <div id="projectImagePreview" style="display:{{ $existingProjectImage ? 'block' : 'none' }};margin-top:0.75rem;border:1px solid var(--border);border-radius:10px;overflow:hidden;max-width:360px;background:var(--surface)">
                    <img src="{{ $existingProjectImage }}" alt="" style="width:100%;height:140px;object-fit:cover;display:block" onerror="this.parentElement.style.display='none'">
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
                <div class="form-group">
                    <label class="form-label">Result <span class="lang-badge en">EN</span></label>
                    <input type="text" name="result" class="form-control" maxlength="255" value="{{ old('result', $project?->result) }}" placeholder="e.g. Page load cut from 6s to 1.2s">
                    <div class="form-hint">One measurable outcome. Highlighted on the case-study card and project page — leave empty if you don't have a real number.</div>
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
                <div class="form-group">
                    <label class="form-label">النتيجة <span class="lang-badge ar">AR</span></label>
                    <input type="text" name="result_ar" class="form-control" maxlength="255" value="{{ old('result_ar', $project?->result_ar) }}" placeholder="مثال: تقليل زمن التحميل من 6 ثوانٍ إلى 1.2 ثانية" dir="rtl" style="font-family:'Cairo',sans-serif">
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

{{-- ── GALLERY ── --}}
<div class="card" id="gallery" style="max-width:900px;margin-top:1.5rem;scroll-margin-top:90px">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-images" style="color:var(--cyan)"></i> {{ __('Gallery') }}
            @if($project)<span class="badge badge-cyan">{{ count($galleryImages) }}</span>@endif
        </span>
    </div>
    <div class="card-body">
        @if(! $project)
            <p class="form-hint" style="margin:0"><i class="fas fa-circle-info" style="color:var(--cyan)"></i> {{ __('Save the project first, then you can add gallery images.') }}</p>
        @else
            <form method="POST" action="{{ route('admin.projects.gallery.upload', $project->id) }}" enctype="multipart/form-data" class="gallery-upload" id="galleryUploadForm">
                @csrf
                <label class="gallery-drop" for="galleryFiles">
                    <i class="fas fa-cloud-arrow-up"></i>
                    <strong>{{ __('Choose images or drag them here') }}</strong>
                    <span>{{ __('JPG, PNG or WebP · up to 12 at a time · 8 MB each. Images are resized and converted to WebP automatically.') }}</span>
                    <input type="file" name="images[]" id="galleryFiles" accept="image/jpeg,image/png,image/webp" multiple required>
                </label>
                <div class="gallery-selected" id="gallerySelected" hidden></div>
                <button type="submit" class="btn btn-primary" id="galleryUploadBtn" disabled><i class="fas fa-upload"></i> {{ __('Upload images') }}</button>
            </form>

            @if(count($galleryImages))
                <form method="POST" action="{{ route('admin.projects.gallery.update', $project->id) }}" style="margin-top:1.5rem">
                    @csrf @method('PUT')
                    <div class="gallery-admin-grid">
                        @foreach($galleryImages as $image)
                            <div class="gallery-admin-item">
                                <a href="{{ asset('uploads/'.$image->path) }}" target="_blank" rel="noopener" class="gallery-admin-thumb">
                                    <img src="{{ asset('uploads/'.$image->thumb_path) }}" alt="" loading="lazy">
                                    @if($loop->first && ! $project->image)<span class="badge badge-cyan gallery-cover-badge">{{ __('Used as cover') }}</span>@endif
                                </a>
                                <div class="gallery-admin-fields">
                                    <input type="text" name="caption[{{ $image->id }}]" class="form-control" value="{{ $image->caption }}" placeholder="{{ __('Caption') }} (EN)" maxlength="255">
                                    <input type="text" name="caption_ar[{{ $image->id }}]" class="form-control" value="{{ $image->caption_ar }}" placeholder="الوصف (AR)" maxlength="255" dir="rtl">
                                    <div style="display:flex;gap:.5rem;align-items:center">
                                        <label class="form-hint" style="margin:0" for="order-{{ $image->id }}">{{ __('Order') }}</label>
                                        <input type="number" id="order-{{ $image->id }}" name="sort_order[{{ $image->id }}]" class="form-control" value="{{ $image->sort_order }}" style="max-width:90px">
                                        <button type="submit" form="delete-image-{{ $image->id }}" class="btn btn-danger btn-sm" style="margin-inline-start:auto" aria-label="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-secondary" style="margin-top:1rem"><i class="fas fa-save"></i> {{ __('Save captions & order') }}</button>
                </form>
                @foreach($galleryImages as $image)
                    <form method="POST" action="{{ route('admin.projects.gallery.delete', $image->id) }}" id="delete-image-{{ $image->id }}" onsubmit="return confirm('{{ __('Delete this image?') }}')">
                        @csrf @method('DELETE')
                    </form>
                @endforeach
            @endif
        @endif
    </div>
</div>

<style>
.gallery-drop { position:relative; display:flex; flex-direction:column; align-items:center; gap:.35rem; padding:1.8rem 1rem; border:2px dashed var(--border); border-radius:var(--radius); background:var(--bg); text-align:center; cursor:pointer; transition:border-color .2s, background .2s; }
.gallery-drop:hover, .gallery-drop.dragover { border-color:var(--cyan); background:var(--cyan-dim); }
.gallery-drop i { font-size:1.8rem; color:var(--cyan); }
.gallery-drop span { font-size:.82rem; color:var(--muted); }
.gallery-drop input { position:absolute; inset:0; opacity:0; cursor:pointer; }
.gallery-selected { display:flex; flex-wrap:wrap; gap:.5rem; margin:.9rem 0; }
.gallery-selected img { width:72px; height:54px; object-fit:cover; border-radius:8px; border:1px solid var(--border); }
.gallery-upload .btn { margin-top:.9rem; }
.gallery-admin-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:1rem; }
.gallery-admin-item { border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; background:var(--bg); }
.gallery-admin-thumb { position:relative; display:block; aspect-ratio:16/10; background:var(--bg3); }
.gallery-admin-thumb img { width:100%; height:100%; object-fit:cover; }
.gallery-cover-badge { position:absolute; top:.5rem; inset-inline-start:.5rem; }
.gallery-admin-fields { display:flex; flex-direction:column; gap:.5rem; padding:.75rem; }
.gallery-admin-fields .form-control { min-height:38px; padding:.45rem .7rem; font-size:.85rem; }
</style>

<script>
function switchLang(lang, btn) {
    document.querySelectorAll('.bi-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.bi-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + lang).classList.add('active');
}
const projectImageFile = document.getElementById('projectImageFile');
const projectImageUrl = document.getElementById('projectImageUrl');
const projectImagePreview = document.getElementById('projectImagePreview');
const projectImagePreviewImage = projectImagePreview?.querySelector('img');
function previewProjectImage(value) {
    if (!value) {
        projectImagePreview.style.display = 'none';
        return;
    }
    projectImagePreviewImage.src = value;
    projectImagePreview.style.display = 'block';
}
projectImageFile?.addEventListener('change', () => {
    const file = projectImageFile.files?.[0];
    if (!file) return;
    previewProjectImage(URL.createObjectURL(file));
});
projectImageUrl?.addEventListener('input', () => {
    if (!projectImageFile?.files?.length) previewProjectImage(projectImageUrl.value.trim());
});
projectImagePreviewImage?.addEventListener('error', () => {
    projectImagePreview.style.display = 'none';
});

// Gallery upload: previews, drag & drop highlight, enable the button once files are chosen
(function () {
    const input = document.getElementById('galleryFiles');
    if (!input) return;
    const drop = input.closest('.gallery-drop');
    const selected = document.getElementById('gallerySelected');
    const button = document.getElementById('galleryUploadBtn');
    input.addEventListener('change', () => {
        selected.innerHTML = '';
        [...input.files].forEach(file => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;
            selected.appendChild(img);
        });
        selected.hidden = input.files.length === 0;
        button.disabled = input.files.length === 0;
    });
    ['dragenter', 'dragover'].forEach(type => drop.addEventListener(type, () => drop.classList.add('dragover')));
    ['dragleave', 'drop'].forEach(type => drop.addEventListener(type, () => drop.classList.remove('dragover')));
    document.getElementById('galleryUploadForm').addEventListener('submit', () => {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> ' + @json(__('Uploading…'));
    });
})();
</script>
@endsection
