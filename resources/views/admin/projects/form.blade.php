@extends('admin.layout')
@section('title', $project ? 'Edit Project' : 'New Project')

@section('topbar-actions')
    <a href="{{ route('admin.projects') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
@endsection

@section('content')
<div class="card" style="max-width:860px">
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

            {{-- Basic Info --}}
            <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:2px;color:var(--cyan);font-family:'JetBrains Mono',monospace;margin-bottom:0.75rem">Basic Info</div>

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
                <label class="form-label">Short Description *</label>
                <textarea name="description" class="form-control" rows="3" required>{{ old('description', $project?->description) }}</textarea>
                <div class="form-hint">Shown on the project card in the portfolio grid.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Overview (Full Detail)</label>
                <textarea name="overview" class="form-control" rows="5" placeholder="Detailed project overview shown on the project detail page...">{{ old('overview', $project?->overview) }}</textarea>
            </div>

            {{-- Project Meta --}}
            <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:2px;color:var(--cyan);font-family:'JetBrains Mono',monospace;margin:1.5rem 0 0.75rem">Project Details</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Client</label>
                    <input type="text" name="client" class="form-control" value="{{ old('client', $project?->client) }}" placeholder="e.g. Acme Corp.">
                </div>
                <div class="form-group">
                    <label class="form-label">Duration</label>
                    <input type="text" name="duration" class="form-control" value="{{ old('duration', $project?->duration) }}" placeholder="e.g. 3 months">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" value="{{ old('category', $project?->category) }}" placeholder="e.g. Web App, API, Mobile, E-Commerce">
                <div class="form-hint">Used to group related projects.</div>
            </div>

            {{-- Work Stages --}}
            <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:2px;color:var(--cyan);font-family:'JetBrains Mono',monospace;margin:1.5rem 0 0.75rem">Work Stages</div>

            <div class="form-group">
                <label class="form-label">Stages (one per line)</label>
                <textarea name="work_stages" class="form-control" rows="6" placeholder="Requirements Analysis&#10;System Architecture Design&#10;Backend API Development&#10;Frontend Integration&#10;Testing & QA&#10;Deployment & Handover">{{ old('work_stages', $project ? implode("\n", (array)($project->work_stages ?? [])) : '') }}</textarea>
                <div class="form-hint">Each line becomes a numbered stage on the detail page.</div>
            </div>

            {{-- Tech & Links --}}
            <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:2px;color:var(--cyan);font-family:'JetBrains Mono',monospace;margin:1.5rem 0 0.75rem">Tech Stack & Links</div>

            <div class="form-group">
                <label class="form-label">Tech Stack</label>
                <input type="text" name="stack" class="form-control" value="{{ old('stack', $project ? implode(', ', (array)($project->stack ?? [])) : '') }}" placeholder="Laravel, MySQL, Redis, Vue.js">
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

            {{-- Visibility --}}
            <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:2px;color:var(--cyan);font-family:'JetBrains Mono',monospace;margin:1.5rem 0 0.75rem">Settings</div>

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
