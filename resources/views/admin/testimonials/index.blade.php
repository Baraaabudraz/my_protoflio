@extends('admin.layout')
@section('title', __('Testimonials'))

@section('topbar-actions')
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> {{ __('Add Testimonial') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">{{ __('Testimonials') }} <span class="badge badge-cyan">{{ count($testimonials) }}</span></span>
    </div>
    @if(empty($testimonials))
        <div class="card-body" style="text-align:center;color:var(--muted);padding:3rem;">
            <i class="fas fa-quote-left" style="font-size:2rem;margin-bottom:1rem;display:block"></i>
            {{ __('No testimonials yet. The section stays hidden on the portfolio until you add one.') }}
            <a href="{{ route('admin.testimonials.create') }}" style="color:var(--cyan)">{{ __('Add one') }}</a>.
        </div>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('Client') }}</th>
                <th>{{ __('Quote') }}</th>
                <th>{{ __('Project') }}</th>
                <th>{{ __('Visible') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($testimonials as $testimonial)
            <tr>
                <td>
                    <div style="font-weight:600">{{ $testimonial->name }}</div>
                    @if($testimonial->role)<div style="color:var(--muted);font-size:.82rem">{{ t($testimonial, 'role') }}</div>@endif
                </td>
                <td style="max-width:360px;color:var(--text-2);font-size:.88rem">“{{ \Illuminate\Support\Str::limit(t($testimonial, 'quote'), 100) }}”</td>
                <td style="font-size:.85rem">{{ $testimonial->project_title ?? '—' }}</td>
                <td>
                    @if($testimonial->visible)
                        <span class="badge badge-success">{{ __('Visible') }}</span>
                    @else
                        <span class="badge badge-muted">{{ __('Hidden') }}</span>
                    @endif
                </td>
                <td style="color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem">{{ $testimonial->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:0.4rem">
                        <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="btn btn-secondary btn-sm" aria-label="{{ __('Edit') }}"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.testimonials.delete', $testimonial->id) }}" onsubmit="return confirm('{{ __('Delete this testimonial?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" aria-label="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
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
