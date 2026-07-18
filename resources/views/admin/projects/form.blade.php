@extends('admin.layout')
@section('title', $project ? 'Edit Project' : 'New Project')

@section('topbar-actions')
    <a href="{{ route('admin.projects') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
@endsection

@section('content')
<div class="card" style="max-width:760px">
    <div class="card-header">
        <span class="card-title">{{ $project ? 'Edit: '.$project->title : 'Add New Project' }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $project ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}">
            @csrf
            @if($project) @method('PUT') @endif

            @if($errors->any())
                <div class="alert" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;margin-bottom:1rem;padding:0.75rem 1rem;border-radius:8px;">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $project?->title) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Icon (emoji)</label>
                    <input type="text" name="icon" class="form-control" value="{{ old('icon', $project?->icon ?? '🚀') }}" maxlength="5">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" required>{{ old('description', $project?->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Tech Stack</label>
                <input type="text" name="stack" class="form-control" value="{{ old('stack', $project ? implode(', ', $project->stack ?? []) : '') }}" placeholder="Laravel, MySQL, Redis, Vue.js">
                <div class="form-hint">Comma-separated list</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">GitHub URL</label>
                    <input type="url" name="github_url" class="form-control" value="{{ old('github_url', $project?->github_url) }}" placeholder="https://github.com/...">
                </div>
                <div class="form-group">
                    <label class="form-label">Live URL</label>
                    <input type="url" name="live_url" class="form-control" value="{{ old('live_url', $project?->live_url) }}" placeholder="https://...">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $project?->sort_order ?? 0) }}">
                </div>
                <div class="form-group" style="display:flex;gap:1.5rem;align-items:center;padding-top:1.5rem;">
                    <label class="form-check">
                        <input type="checkbox" name="featured" {{ old('featured', $project?->featured) ? 'checked' : '' }}>
                        <span>Featured</span>
                    </label>
                    <label class="form-check">
                        <input type="checkbox" name="visible" {{ old('visible', $project === null || $project?->visible) ? 'checked' : '' }}>
                        <span>Visible</span>
                    </label>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $project ? 'Update Project' : 'Create Project' }}
                </button>
                <a href="{{ route('admin.projects') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
