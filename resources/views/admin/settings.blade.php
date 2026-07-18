@extends('admin.layout')
@section('title', 'Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">

        {{-- Hero Section --}}
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-house" style="color:var(--cyan)"></i> &nbsp;Hero Section</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="hero_name" class="form-control" value="{{ $settings['hero_name'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tagline / Role</label>
                    <input type="text" name="hero_tagline" class="form-control" value="{{ $settings['hero_tagline'] ?? '' }}" placeholder="Backend Engineer">
                </div>
                <div class="form-group">
                    <label class="form-label">Subtitle (supports &lt;strong&gt; tags)</label>
                    <textarea name="hero_subtitle" class="form-control" style="min-height:90px">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Years of Exp.</label>
                        <input type="text" name="hero_stat_years" class="form-control" value="{{ $settings['hero_stat_years'] ?? '5+' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Projects Stat</label>
                        <input type="text" name="hero_stat_projects" class="form-control" value="{{ $settings['hero_stat_projects'] ?? '30+' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Clients Stat</label>
                    <input type="text" name="hero_stat_clients" class="form-control" value="{{ $settings['hero_stat_clients'] ?? '15+' }}">
                </div>
            </div>
        </div>

        {{-- About Section --}}
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-user" style="color:var(--cyan)"></i> &nbsp;About Section</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Section Heading</label>
                    <input type="text" name="about_heading" class="form-control" value="{{ $settings['about_heading'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Paragraph 1</label>
                    <textarea name="about_p1" class="form-control">{{ $settings['about_p1'] ?? '' }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Paragraph 2</label>
                    <textarea name="about_p2" class="form-control">{{ $settings['about_p2'] ?? '' }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Paragraph 3</label>
                    <textarea name="about_p3" class="form-control">{{ $settings['about_p3'] ?? '' }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Tags (comma-separated)</label>
                    <input type="text" name="about_tags" class="form-control" value="{{ $settings['about_tags'] ?? '' }}" placeholder="Laravel, PHP, MySQL">
                </div>
            </div>
        </div>

        {{-- Contact & Links --}}
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-link" style="color:var(--cyan)"></i> &nbsp;Contact & Links</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ $settings['email'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">GitHub URL</label>
                    <input type="url" name="github_url" class="form-control" value="{{ $settings['github_url'] ?? '' }}" placeholder="https://github.com/yourusername">
                </div>
                <div class="form-group">
                    <label class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" class="form-control" value="{{ $settings['linkedin_url'] ?? '' }}" placeholder="https://linkedin.com/in/yourusername">
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-ellipsis" style="color:var(--cyan)"></i> &nbsp;Footer</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Footer Text</label>
                    <input type="text" name="footer_text" class="form-control" value="{{ $settings['footer_text'] ?? '' }}">
                </div>
            </div>
        </div>

    </div>

    <div style="margin-top:1.5rem">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> &nbsp;Save All Settings</button>
    </div>
</form>
@endsection
