@extends('admin.layout')
@section('title', __('Projects'))

@section('topbar-actions')
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> {{ __('Add Project') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">{{ __('All Projects') }} <span class="badge badge-cyan">{{ count($projects) }}</span></span>
    </div>
    @if(empty($projects))
        <div class="card-body" style="text-align:center;color:var(--muted);padding:3rem;">
            <i class="fas fa-rocket" style="font-size:2rem;margin-bottom:1rem;display:block"></i>
            {{ __('No projects yet.') }} <a href="{{ route('admin.projects.create') }}" style="color:var(--cyan)">{{ __('Add your first project') }}</a>.
        </div>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('Preview') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Stack') }}</th>
                <th>{{ __('Featured') }}</th>
                <th>{{ __('Visible') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            <tr>
                <td>
                    @if($project->image)
                        <img src="{{ project_image_url($project->image) }}" loading="lazy" alt="" style="width:64px;height:44px;object-fit:cover;border-radius:10px;border:1px solid var(--border)" onerror="this.style.display='none'">
                    @else
                        <span style="font-size:1.5rem">{{ $project->icon }}</span>
                    @endif
                </td>
                <td>
                    <div style="font-weight:600">{{ t($project, 'title') }}</div>
                    <div style="font-size:0.78rem;color:var(--muted);margin-top:0.2rem">{{ Str::limit(t($project, 'description'), 60) }}</div>
                </td>
                <td>
                    <div style="display:flex;flex-wrap:wrap;gap:0.25rem;">
                        @foreach(array_slice($project->stack ?? [], 0, 3) as $tech)
                            <span class="badge badge-muted">{{ $tech }}</span>
                        @endforeach
                        @if(count($project->stack ?? []) > 3)
                            <span class="badge badge-muted">+{{ count($project->stack) - 3 }}</span>
                        @endif
                    </div>
                </td>
                <td>
                    @if($project->featured)
                        <span class="badge badge-cyan"><i class="fas fa-star"></i> {{ __('Yes') }}</span>
                    @else
                        <span class="badge badge-muted">{{ __('No') }}</span>
                    @endif
                </td>
                <td>
                    @if($project->visible)
                        <span class="badge badge-success">{{ __('Visible') }}</span>
                    @else
                        <span class="badge badge-muted">{{ __('Hidden') }}</span>
                    @endif
                </td>
                <td style="color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem">{{ $project->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:0.4rem">
                        <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.projects.delete', $project->id) }}" onsubmit="return confirm('{{ __('Delete this project?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
