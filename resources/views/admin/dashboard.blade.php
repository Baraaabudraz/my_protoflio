@extends('admin.layout')
@section('title', 'Dashboard')

@section('topbar-actions')
    <a href="{{ url('/') }}" target="_blank" class="btn btn-view btn-sm"><i class="fas fa-eye"></i> View Portfolio</a>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-number">{{ $stats['projects'] }}</div>
        <div class="stat-card-label"><i class="fas fa-rocket" style="color:var(--cyan)"></i> &nbsp;Projects</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number">{{ $stats['experiences'] }}</div>
        <div class="stat-card-label"><i class="fas fa-briefcase" style="color:var(--cyan)"></i> &nbsp;Experience Entries</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number">{{ $stats['skills'] }}</div>
        <div class="stat-card-label"><i class="fas fa-code-branch" style="color:var(--cyan)"></i> &nbsp;Skills Tracked</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Quick Actions</span>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:0.75rem;">
            <a href="{{ route('admin.projects.create') }}" class="btn btn-secondary"><i class="fas fa-plus"></i> Add New Project</a>
            <a href="{{ route('admin.experience.create') }}" class="btn btn-secondary"><i class="fas fa-plus"></i> Add Experience</a>
            <a href="{{ route('admin.skills') }}" class="btn btn-secondary"><i class="fas fa-plus"></i> Manage Skills</a>
            <a href="{{ route('admin.settings') }}" class="btn btn-secondary"><i class="fas fa-sliders"></i> Edit Profile & Settings</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">CMS Sections</span>
        </div>
        <div class="card-body">
            <p style="font-size:0.875rem;color:var(--muted);line-height:1.7;">
                Use the sidebar to navigate between sections. All changes update the portfolio in real-time.
            </p>
            <ul style="margin-top:1rem;font-size:0.875rem;color:var(--muted);list-style:none;display:flex;flex-direction:column;gap:0.5rem;">
                <li><i class="fas fa-check" style="color:var(--cyan);margin-right:0.5rem"></i><strong style="color:var(--text)">Projects</strong> — Add, edit, reorder, toggle visibility</li>
                <li><i class="fas fa-check" style="color:var(--cyan);margin-right:0.5rem"></i><strong style="color:var(--text)">Experience</strong> — Career timeline management</li>
                <li><i class="fas fa-check" style="color:var(--cyan);margin-right:0.5rem"></i><strong style="color:var(--text)">Skills</strong> — Categories + progress bars / tags</li>
                <li><i class="fas fa-check" style="color:var(--cyan);margin-right:0.5rem"></i><strong style="color:var(--text)">Settings</strong> — Hero, About, contact info</li>
            </ul>
        </div>
    </div>
</div>
@endsection
