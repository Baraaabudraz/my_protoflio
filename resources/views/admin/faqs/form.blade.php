@extends('admin.layout')
@section('title', $faq ? __('Edit Question') : __('Add Question'))

@section('styles')
@include('admin.partials.bilingual-tabs-style')
@endsection

@section('topbar-actions')
    <a href="{{ route('admin.faqs') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="card" style="max-width:780px">
    <div class="card-header">
        <span class="card-title">{{ $faq ? __('Edit Question') : __('Add Question') }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $faq ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}">
            @csrf
            @if($faq) @method('PUT') @endif

            @if($errors->any())
                <div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('Sort Order') }}</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $faq?->sort_order ?? 0) }}">
                </div>
                <div class="form-group" style="align-self:end">
                    <label class="form-check">
                        <input type="checkbox" name="visible" {{ old('visible', $faq === null || $faq?->visible) ? 'checked' : '' }}>
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
                    <label class="form-label">Question * <span class="lang-badge en">EN</span></label>
                    <input type="text" name="question" class="form-control" maxlength="255" value="{{ old('question', $faq?->question) }}" placeholder="How much will my project cost?" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Answer * <span class="lang-badge en">EN</span></label>
                    <textarea name="answer" class="form-control" style="min-height:120px" placeholder="A short, honest answer that removes the doubt." required>{{ old('answer', $faq?->answer) }}</textarea>
                </div>
            </div>

            <div class="bi-panel" id="panel-ar">
                <div class="bi-note">
                    <i class="fas fa-circle-info" style="color:var(--cyan)"></i>
                    الحقول العربية اختيارية — إذا تُركت فارغة سيُعرض المحتوى الإنجليزي.
                </div>
                <div class="form-group">
                    <label class="form-label">السؤال <span class="lang-badge ar">AR</span></label>
                    <input type="text" name="question_ar" class="form-control" maxlength="255" value="{{ old('question_ar', $faq?->question_ar) }}" placeholder="مثال: كم ستكلّف فكرتي أو مشروعي؟" dir="rtl" style="font-family:'Cairo',sans-serif">
                </div>
                <div class="form-group">
                    <label class="form-label">الإجابة <span class="lang-badge ar">AR</span></label>
                    <textarea name="answer_ar" class="form-control" dir="rtl" style="min-height:120px;font-family:'Cairo',sans-serif">{{ old('answer_ar', $faq?->answer_ar) }}</textarea>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $faq ? __('Save') : __('Add Question') }}</button>
                <a href="{{ route('admin.faqs') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@include('admin.partials.bilingual-tabs-script')
@endsection
