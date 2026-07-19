@extends('admin.layout')
@section('title', __('Dashboard'))

@section('topbar-actions')
    <a href="{{ url('/') }}" target="_blank" class="btn btn-view btn-sm"><i class="fas fa-eye"></i> {{ __('View Portfolio') }}</a>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-number">{{ $stats['projects'] }}</div>
        <div class="stat-card-label"><i class="fas fa-rocket" style="color:var(--cyan)"></i> &nbsp;{{ __('Projects') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number">{{ $stats['experiences'] }}</div>
        <div class="stat-card-label"><i class="fas fa-briefcase" style="color:var(--cyan)"></i> &nbsp;{{ __('Experience Entries') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number">{{ $stats['skills'] }}</div>
        <div class="stat-card-label"><i class="fas fa-code-branch" style="color:var(--cyan)"></i> &nbsp;{{ __('Skills Tracked') }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ __('Quick Actions') }}</span>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:0.75rem;">
            <a href="{{ route('admin.projects.create') }}" class="btn btn-secondary"><i class="fas fa-plus"></i> {{ __('Add New Project') }}</a>
            <a href="{{ route('admin.experience.create') }}" class="btn btn-secondary"><i class="fas fa-plus"></i> {{ __('Add Experience') }}</a>
            <a href="{{ route('admin.skills') }}" class="btn btn-secondary"><i class="fas fa-code-branch"></i> {{ __('Manage Skills') }}</a>
            <a href="{{ route('admin.settings') }}" class="btn btn-secondary"><i class="fas fa-sliders"></i> {{ __('Edit Profile & Settings') }}</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ __('CMS Sections') }}</span>
        </div>
        <div class="card-body">
            <p style="font-size:0.875rem;color:var(--muted);line-height:1.8;">
                @if(app()->getLocale() === 'ar')
                    استخدم القائمة الجانبية للتنقل بين الأقسام. جميع التغييرات تُحدِّث المحفظة فوراً.
                @else
                    Use the sidebar to navigate between sections. All changes update the portfolio in real-time.
                @endif
            </p>
            <ul style="margin-top:1rem;font-size:0.875rem;color:var(--muted);list-style:none;display:flex;flex-direction:column;gap:0.6rem;">
                <li><i class="fas fa-check" style="color:var(--cyan);margin-inline-end:0.5rem"></i><strong style="color:var(--text)">{{ __('Projects') }}</strong> — {{ app()->getLocale() === 'ar' ? 'إضافة، تعديل، إعادة ترتيب، إخفاء' : 'Add, edit, reorder, toggle visibility' }}</li>
                <li><i class="fas fa-check" style="color:var(--cyan);margin-inline-end:0.5rem"></i><strong style="color:var(--text)">{{ __('Experience') }}</strong> — {{ app()->getLocale() === 'ar' ? 'إدارة المسيرة المهنية' : 'Career timeline management' }}</li>
                <li><i class="fas fa-check" style="color:var(--cyan);margin-inline-end:0.5rem"></i><strong style="color:var(--text)">{{ __('Skills') }}</strong> — {{ app()->getLocale() === 'ar' ? 'فئات + أشرطة تقدم / وسوم' : 'Categories + progress bars / tags' }}</li>
                <li><i class="fas fa-check" style="color:var(--cyan);margin-inline-end:0.5rem"></i><strong style="color:var(--text)">{{ __('Settings') }}</strong> — {{ app()->getLocale() === 'ar' ? 'الهيرو، عني، معلومات التواصل' : 'Hero, About, contact info' }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
