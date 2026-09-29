@extends('admin.layout')
@section('title', __('Messages'))

@section('styles')
<style>
.msg-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
.msg-filter { display:inline-flex; gap:.25rem; padding:.25rem; background:var(--bg3); border:1px solid var(--border); border-radius:12px; }
.msg-filter a { padding:.45rem .9rem; border-radius:9px; font-size:.85rem; font-weight:600; color:var(--muted); text-decoration:none; }
.msg-filter a.active { background:var(--card); color:var(--text); box-shadow:var(--shadow); }
.msg-list { display:flex; flex-direction:column; gap:1rem; }
.msg { border-inline-start:4px solid transparent; }
.msg.unread { border-inline-start-color:var(--cyan-strong); }
.msg-head { display:flex; align-items:flex-start; gap:1rem; padding:1.1rem 1.4rem; }
.msg-avatar { width:44px; height:44px; border-radius:12px; flex-shrink:0; display:grid; place-items:center; background:var(--cyan-dim); color:var(--cyan); font:800 1.05rem var(--font-head); }
.msg-who { flex:1; min-width:0; }
.msg-who strong { font:700 1rem var(--font-head); }
.msg-who a { color:var(--cyan); text-decoration:none; font-size:.88rem; word-break:break-all; }
.msg-meta { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.5rem; }
.msg-time { font-size:.8rem; color:var(--muted); white-space:nowrap; }
.msg-body { padding:0 1.4rem 1.2rem; }
.msg-text { padding:1rem 1.1rem; border-radius:12px; background:var(--bg); border:1px solid var(--border); white-space:pre-line; line-height:1.75; font-size:.93rem; }
.msg-error { margin-top:.75rem; padding:.7rem .9rem; border-radius:10px; background:var(--warning-dim); color:var(--warning); font-size:.82rem; }
.msg-actions { display:flex; flex-wrap:wrap; gap:.5rem; padding:0 1.4rem 1.2rem; }
.mail-warning { display:flex; gap:.75rem; align-items:flex-start; padding:1rem 1.2rem; margin-bottom:1.25rem; border-radius:var(--radius); background:var(--warning-dim); color:var(--warning); border:1px solid color-mix(in srgb, var(--warning) 30%, transparent); font-size:.9rem; }
.mail-warning code { font-family:var(--font-mono); font-size:.82rem; }
</style>
@endsection

@section('content')
@unless($mailConfigured)
    <div class="mail-warning" role="alert">
        <i class="fas fa-triangle-exclamation" style="margin-top:.2rem"></i>
        <div>
            <strong>{{ __('Email delivery is not configured yet.') }}</strong>
            {{ __('Messages are still saved here, but they are not emailed to you. Set the MAIL_* values in .laravel.env (see README).') }}
        </div>
    </div>
@endunless

<div class="msg-toolbar">
    <div class="msg-filter" role="tablist">
        <a href="{{ route('admin.messages') }}" class="{{ $filter === 'all' ? 'active' : '' }}">{{ __('All') }} ({{ (int) $counts->total }})</a>
        <a href="{{ route('admin.messages', ['filter' => 'unread']) }}" class="{{ $filter === 'unread' ? 'active' : '' }}">{{ __('Unread') }} ({{ (int) $counts->unread }})</a>
    </div>
    @if((int) $counts->failed > 0)
        <span class="badge badge-warning"><i class="fas fa-envelope-circle-check"></i> {{ (int) $counts->failed }} {{ __('not emailed') }}</span>
    @endif
</div>

@if(empty($messages))
    <div class="card"><div class="empty-state"><i class="fas fa-inbox"></i>{{ $filter === 'unread' ? __('No unread messages.') : __('No messages yet. Inquiries from the contact form will appear here.') }}</div></div>
@else
<div class="msg-list">
    @foreach($messages as $message)
    <article class="card msg {{ $message->read_at ? '' : 'unread' }}">
        <div class="msg-head">
            <span class="msg-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
            <div class="msg-who">
                <strong>{{ $message->name }}</strong>
                @unless($message->read_at)<span class="badge badge-cyan" style="margin-inline-start:.4rem">{{ __('New') }}</span>@endunless
                <div><a href="mailto:{{ $message->email }}">{{ $message->email }}</a>@if($message->phone) · <span dir="ltr">{{ $message->phone }}</span>@endif</div>
                <div class="msg-meta">
                    @if($message->service)<span class="badge badge-muted"><i class="fas fa-handshake"></i> {{ $message->service }}</span>@endif
                    @if($message->budget)<span class="badge badge-muted"><i class="fas fa-wallet"></i> {{ $message->budget }}</span>@endif
                    <span class="badge badge-muted">{{ $message->locale === 'ar' ? 'العربية' : 'English' }}</span>
                    @if($message->mailed_at)
                        <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Emailed') }}</span>
                    @elseif($message->mail_error)
                        <span class="badge badge-warning"><i class="fas fa-triangle-exclamation"></i> {{ __('Not emailed') }}</span>
                    @endif
                </div>
            </div>
            <time class="msg-time" datetime="{{ $message->created_at }}" title="{{ $message->created_at }}">{{ \Illuminate\Support\Carbon::parse($message->created_at)->diffForHumans() }}</time>
        </div>
        <div class="msg-body">
            <div class="msg-text" dir="auto">{{ $message->message }}</div>
            @if($message->mail_error)
                <div class="msg-error"><strong>{{ __('Mail error:') }}</strong> {{ $message->mail_error }}</div>
            @endif
        </div>
        <div class="msg-actions">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->service ?: __('Project inquiry'))) }}" class="btn btn-primary btn-sm"><i class="fas fa-reply"></i> {{ __('Reply by email') }}</a>
            @if($message->phone)
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $message->phone) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            @endif
            <form method="POST" action="{{ route('admin.messages.read', $message->id) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-secondary btn-sm"><i class="fas {{ $message->read_at ? 'fa-envelope' : 'fa-envelope-open' }}"></i> {{ $message->read_at ? __('Mark as unread') : __('Mark as read') }}</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.delete', $message->id) }}" onsubmit="return confirm('{{ __('Delete this message?') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" aria-label="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </article>
    @endforeach
</div>
@endif
@endsection
