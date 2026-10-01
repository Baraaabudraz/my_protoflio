@extends('admin.layout')
@section('title', $testimonial ? __('Edit Testimonial') : __('Add Testimonial'))

@section('styles')
@include('admin.partials.bilingual-tabs-style')
@endsection

@section('topbar-actions')
    <a href="{{ route('admin.testimonials') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="card" style="max-width:780px">
    <div class="card-header">
        <span class="card-title">{{ $testimonial ? __('Edit:').' '.$testimonial->name : __('Add Testimonial') }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $testimonial ? route('admin.testimonials.update', $testimonial->id) : route('admin.testimonials.store') }}">
            @csrf
            @if($testimonial) @method('PUT') @endif

            @if($errors->any())
                <div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('Client name') }} *</label>
                    <input type="text" name="name" class="form-control" maxlength="255" value="{{ old('name', $testimonial?->name) }}" required>
                    <div class="form-hint">{{ __('Only publish quotes the client agreed to share.') }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Related project') }}</label>
                    <select name="project_id" class="form-control">
                        <option value="">—</option>
                        @foreach($projects as $option)
                            <option value="{{ $option->id }}" @selected((string) old('project_id', $testimonial?->project_id) === (string) $option->id)>{{ t($option, 'title') }}</option>
                        @endforeach
                    </select>
                    <div class="form-hint">{{ __('The quote is also shown on that project’s page.') }}</div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('Sort Order') }}</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $testimonial?->sort_order ?? 0) }}">
                </div>
                <div class="form-group" style="align-self:end">
                    <label class="form-check">
                        <input type="checkbox" name="visible" {{ old('visible', $testimonial === null || $testimonial?->visible) ? 'checked' : '' }}>
                        <span>{{ __('Visible on portfolio') }}</span>
                    </label>
                </div>
            </div>

            <div class="bi-tabs">
                <button type="button" class="bi-tab active" onclick="switchLang('en',this)">🇬🇧 English <span class="lang-badge en">EN</span></button>
                <button type="button" class="bi-tab"        onclick="switchLang('ar',this)">🇸🇦 العربية <span class="lang-badge ar">AR</span></button>
            </div>

            <div class="bi-panel active" id="panel-en">
                <div class="form-group">
                    <label class="form-label">Role / company <span class="lang-badge en">EN</span></label>
                    <input type="text" name="role" class="form-control" maxlength="255" value="{{ old('role', $testimonial?->role) }}" placeholder="Operations Manager, Acme Logistics">
                </div>
                <div class="form-group">
                    <label class="form-label">Quote * <span class="lang-badge en">EN</span></label>
                    <textarea name="quote" class="form-control" style="min-height:120px" required>{{ old('quote', $testimonial?->quote) }}</textarea>
                </div>
            </div>

            <div class="bi-panel" id="panel-ar">
                <div class="bi-note">
                    <i class="fas fa-circle-info" style="color:var(--cyan)"></i>
                    الحقول العربية اختيارية — إذا تُركت فارغة سيُعرض المحتوى الإنجليزي.
                </div>
                <div class="form-group">
                    <label class="form-label">المنصب / الجهة <span class="lang-badge ar">AR</span></label>
                    <input type="text" name="role_ar" class="form-control" maxlength="255" value="{{ old('role_ar', $testimonial?->role_ar) }}" dir="rtl" style="font-family:'Cairo',sans-serif">
                </div>
                <div class="form-group">
                    <label class="form-label">الاقتباس <span class="lang-badge ar">AR</span></label>
                    <textarea name="quote_ar" class="form-control" dir="rtl" style="min-height:120px;font-family:'Cairo',sans-serif">{{ old('quote_ar', $testimonial?->quote_ar) }}</textarea>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $testimonial ? __('Save') : __('Add Testimonial') }}</button>
                <a href="{{ route('admin.testimonials') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@include('admin.partials.bilingual-tabs-script')
@endsection
