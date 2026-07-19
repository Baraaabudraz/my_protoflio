@extends('admin.layout')
@section('title', __('Experience'))

@section('topbar-actions')
    <a href="{{ route('admin.experience.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> {{ __('Add Entry') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">{{ __('Career Timeline') }} <span class="badge badge-cyan">{{ count($experiences) }}</span></span>
    </div>
    @if(empty($experiences))
        <div class="card-body" style="text-align:center;color:var(--muted);padding:3rem;">
            <i class="fas fa-briefcase" style="font-size:2rem;margin-bottom:1rem;display:block"></i>
            {{ __('No experience entries.') }} <a href="{{ route('admin.experience.create') }}" style="color:var(--cyan)">{{ __('Add one') }}</a>.
        </div>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Company') }}</th>
                <th>{{ __('Period') }}</th>
                <th>{{ __('Visible') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($experiences as $exp)
            <tr>
                <td><div style="font-weight:600">{{ $exp->title }}</div></td>
                <td style="color:var(--cyan)">{{ $exp->company }}</td>
                <td><span class="badge badge-muted" style="font-family:'JetBrains Mono',monospace">{{ $exp->date_range }}</span></td>
                <td>
                    @if($exp->visible)
                        <span class="badge badge-success">{{ __('Visible') }}</span>
                    @else
                        <span class="badge badge-muted">{{ __('Hidden') }}</span>
                    @endif
                </td>
                <td style="color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem">{{ $exp->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:0.4rem">
                        <a href="{{ route('admin.experience.edit', $exp->id) }}" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.experience.delete', $exp->id) }}" onsubmit="return confirm('{{ __('Delete this entry?') }}')">
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
