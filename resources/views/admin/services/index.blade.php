@extends('admin.layout')
@section('title', __('Services'))

@section('topbar-actions')
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> {{ __('Add Service') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">{{ __('Services') }} <span class="badge badge-cyan">{{ count($services) }}</span></span>
    </div>
    @if(empty($services))
        <div class="card-body" style="text-align:center;color:var(--muted);padding:3rem;">
            <i class="fas fa-handshake" style="font-size:2rem;margin-bottom:1rem;display:block"></i>
            {{ __('No services yet.') }} <a href="{{ route('admin.services.create') }}" style="color:var(--cyan)">{{ __('Add one') }}</a>.
        </div>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('Icon') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Deliverables') }}</th>
                <th>{{ __('Visible') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $service)
            <tr>
                <td><i class="{{ $service->icon }}" style="color:var(--cyan);font-size:1.1rem"></i></td>
                <td><div style="font-weight:600">{{ t($service, 'title') }}</div></td>
                <td><span class="badge badge-muted">{{ count($service->deliverables ?? []) }}</span></td>
                <td>
                    @if($service->visible)
                        <span class="badge badge-success">{{ __('Visible') }}</span>
                    @else
                        <span class="badge badge-muted">{{ __('Hidden') }}</span>
                    @endif
                </td>
                <td style="color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem">{{ $service->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:0.4rem">
                        <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.services.delete', $service->id) }}" onsubmit="return confirm('{{ __('Delete this service?') }}')">
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
