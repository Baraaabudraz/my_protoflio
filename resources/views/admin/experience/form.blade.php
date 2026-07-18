@extends('admin.layout')
@section('title', $experience ? 'Edit Experience' : 'Add Experience')

@section('topbar-actions')
    <a href="{{ route('admin.experience') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
@endsection

@section('content')
<div class="card" style="max-width:760px">
    <div class="card-header">
        <span class="card-title">{{ $experience ? 'Edit: '.$experience->title : 'Add Experience Entry' }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $experience ? route('admin.experience.update', $experience->id) : route('admin.experience.store') }}">
            @csrf
            @if($experience) @method('PUT') @endif

            @if($errors->any())
                <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;margin-bottom:1rem;padding:0.75rem 1rem;border-radius:8px;">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Job Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $experience?->title) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Company *</label>
                    <input type="text" name="company" class="form-control" value="{{ old('company', $experience?->company) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date Range *</label>
                    <input type="text" name="date_range" class="form-control" value="{{ old('date_range', $experience?->date_range) }}" placeholder="2022 – Present" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $experience?->sort_order ?? 0) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" required>{{ old('description', $experience?->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Tags / Tech Used</label>
                <input type="text" name="tags" class="form-control" value="{{ old('tags', $experience ? implode(', ', $experience->tags ?? []) : '') }}" placeholder="Laravel, PHP, MySQL">
                <div class="form-hint">Comma-separated</div>
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="visible" {{ old('visible', $experience === null || $experience?->visible) ? 'checked' : '' }}>
                    <span>Visible on portfolio</span>
                </label>
            </div>

            <div style="display:flex;gap:0.75rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $experience ? 'Update' : 'Create' }}</button>
                <a href="{{ route('admin.experience') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
