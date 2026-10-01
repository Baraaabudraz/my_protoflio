@extends('admin.layout')
@section('title', __('FAQ'))

@section('topbar-actions')
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> {{ __('Add Question') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">{{ __('FAQ') }} <span class="badge badge-cyan">{{ count($faqs) }}</span></span>
    </div>
    @if(empty($faqs))
        <div class="card-body" style="text-align:center;color:var(--muted);padding:3rem;">
            <i class="fas fa-circle-question" style="font-size:2rem;margin-bottom:1rem;display:block"></i>
            {{ __('No questions yet.') }} <a href="{{ route('admin.faqs.create') }}" style="color:var(--cyan)">{{ __('Add one') }}</a>.
        </div>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('Question') }}</th>
                <th>{{ __('Visible') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faqs as $faq)
            <tr>
                <td>
                    <div style="font-weight:600">{{ t($faq, 'question') }}</div>
                    <div style="color:var(--muted);font-size:.82rem;margin-top:.2rem">{{ \Illuminate\Support\Str::limit(t($faq, 'answer'), 110) }}</div>
                </td>
                <td>
                    @if($faq->visible)
                        <span class="badge badge-success">{{ __('Visible') }}</span>
                    @else
                        <span class="badge badge-muted">{{ __('Hidden') }}</span>
                    @endif
                </td>
                <td style="color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem">{{ $faq->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:0.4rem">
                        <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-secondary btn-sm" aria-label="{{ __('Edit') }}"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.faqs.delete', $faq->id) }}" onsubmit="return confirm('{{ __('Delete this question?') }}')">
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
