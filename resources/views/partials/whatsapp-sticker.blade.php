{{--
    Floating WhatsApp "sticker" with the owner's photo. Stays pinned while scrolling.
    Expects: $settings, $locale.
--}}
@php
    $stickerNumber = preg_replace('/\D+/', '', $settings['whatsapp_number'] ?? '');
    $stickerName = explode(' ', ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? ''))[0];
    $stickerMessage = __('Hello :name, I found you through your portfolio and I would like to discuss a project.', ['name' => $stickerName]);
@endphp
@if($stickerNumber)
<style>
    .wa-sticker { position:fixed; bottom:1.5rem; inset-inline-end:1.5rem; z-index:900; display:flex; align-items:flex-end; gap:.75rem; flex-direction:row-reverse; pointer-events:none; font-family:inherit; transition:opacity .35s ease, transform .35s ease; }
    .wa-sticker.is-hidden { opacity:0; transform:translateY(20px) scale(.9); }
    .wa-sticker.is-hidden * { pointer-events:none !important; }
    .wa-sticker-btn { position:relative; width:66px; height:66px; flex-shrink:0; border-radius:50%; pointer-events:auto; text-decoration:none; display:block; transform:rotate(-6deg); transition:transform .35s cubic-bezier(.34,1.56,.64,1); filter:drop-shadow(0 10px 22px rgba(18,140,126,.45)); animation:wa-pop .6s cubic-bezier(.34,1.56,.64,1) both, wa-wiggle 6s ease-in-out 2.5s infinite; }
    .wa-sticker-btn:hover, .wa-sticker-btn:focus-visible { transform:rotate(0) scale(1.08); animation-play-state:paused; }
    .wa-sticker-btn:focus-visible { outline:3px solid #25d366; outline-offset:4px; }
    .wa-sticker-photo { width:100%; height:100%; border-radius:50%; object-fit:cover; object-position:50% 22%; border:4px solid #fff; background:#fff; display:block; }
    .wa-sticker-badge { position:absolute; bottom:-4px; inset-inline-end:-4px; width:30px; height:30px; border-radius:50%; background:#25d366; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.05rem; border:3px solid #fff; box-shadow:0 4px 10px rgba(0,0,0,.2); }
    .wa-sticker-ring { position:absolute; inset:-6px; border-radius:50%; border:2px solid #25d366; opacity:0; animation:wa-ring 2.4s ease-out 1.2s infinite; pointer-events:none; }
    .wa-sticker-online { position:absolute; top:3px; inset-inline-end:4px; width:14px; height:14px; border-radius:50%; background:#22c55e; border:3px solid #fff; }
    .wa-bubble { position:relative; pointer-events:auto; max-width:260px; margin-bottom:.9rem; padding:.85rem 2.1rem .85rem 1rem; border-radius:18px 18px 4px 18px; background:#fff; color:#111b21; box-shadow:0 14px 34px rgba(0,0,0,.18); font-size:.9rem; line-height:1.5; opacity:0; visibility:hidden; transform:translateY(10px) scale(.95); transform-origin:bottom right; transition:all .35s cubic-bezier(.34,1.56,.64,1); }
    [dir="rtl"] .wa-bubble { border-radius:18px 18px 18px 4px; transform-origin:bottom left; padding:.85rem 1rem .85rem 2.1rem; }
    .wa-bubble.show { opacity:1; visibility:visible; transform:none; }
    .wa-bubble strong { display:block; font-size:.92rem; margin-bottom:.1rem; }
    .wa-bubble a { color:#128c7e; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; margin-top:.35rem; }
    .wa-bubble a:hover { text-decoration:underline; }
    .wa-bubble-close { position:absolute; top:.4rem; inset-inline-end:.4rem; width:26px; height:26px; border:none; border-radius:50%; background:transparent; color:#667781; cursor:pointer; font-size:.8rem; }
    .wa-bubble-close:hover { background:#f0f2f5; color:#111b21; }
    body.has-wa-sticker .to-top { bottom:6.5rem; }
    @keyframes wa-pop { from { opacity:0; transform:rotate(-6deg) scale(.3); } }
    @keyframes wa-wiggle { 0%,88%,100% { transform:rotate(-6deg); } 91% { transform:rotate(8deg) scale(1.05); } 94% { transform:rotate(-10deg) scale(1.05); } 97% { transform:rotate(4deg); } }
    @keyframes wa-ring { 0% { transform:scale(.9); opacity:.8; } 100% { transform:scale(1.45); opacity:0; } }
    @media (max-width:768px) {
        .wa-sticker { bottom:1rem; inset-inline-end:1rem; }
        .wa-sticker-btn { width:58px; height:58px; }
        .wa-sticker-badge { width:26px; height:26px; font-size:.9rem; }
        .wa-bubble { max-width:210px; font-size:.85rem; }
        body.has-wa-sticker .to-top { bottom:5.5rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .wa-sticker-btn, .wa-sticker-ring { animation:none !important; }
        .wa-sticker, .wa-bubble, .wa-sticker-btn { transition:none !important; }
    }
    @media print { .wa-sticker { display:none; } }
</style>

<div class="wa-sticker" id="waSticker">
    <a href="https://wa.me/{{ $stickerNumber }}?text={{ rawurlencode($stickerMessage) }}" target="_blank" rel="noopener" class="wa-sticker-btn" aria-label="{{ __('Chat with me on WhatsApp') }}" title="{{ __('Chat with me on WhatsApp') }}">
        <span class="wa-sticker-ring" aria-hidden="true"></span>
        <img src="{{ asset('images/me-thumb.webp') }}" alt="" class="wa-sticker-photo" width="66" height="66">
        <span class="wa-sticker-online" aria-hidden="true"></span>
        <span class="wa-sticker-badge" aria-hidden="true"><i class="fab fa-whatsapp"></i></span>
    </a>
    <div class="wa-bubble" id="waBubble" role="status">
        <button type="button" class="wa-bubble-close" id="waBubbleClose" aria-label="{{ __('Close') }}"><i class="fas fa-xmark"></i></button>
        <strong>{{ __('Hi there!') }} 👋</strong>
        {{ __('Have a question about your project? Chat with me directly.') }}
        <a href="https://wa.me/{{ $stickerNumber }}?text={{ rawurlencode($stickerMessage) }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> {{ __('Start chat') }}</a>
    </div>
</div>

<script>
(function () {
    const sticker = document.getElementById('waSticker');
    const bubble = document.getElementById('waBubble');
    document.body.classList.add('has-wa-sticker');

    // Greeting bubble: once per browsing session, a few seconds after load
    let seen = false;
    try { seen = sessionStorage.getItem('wa-bubble-seen') === '1'; } catch (e) {}
    function hideBubble() {
        bubble.classList.remove('show');
        try { sessionStorage.setItem('wa-bubble-seen', '1'); } catch (e) {}
    }
    if (!seen) {
        setTimeout(() => bubble.classList.add('show'), 4000);
        setTimeout(hideBubble, 14000);
    }
    document.getElementById('waBubbleClose').addEventListener('click', hideBubble);
    bubble.querySelector('a').addEventListener('click', hideBubble);

    // Hide the sticker while the contact section (which already offers WhatsApp) is on screen
    const contact = document.getElementById('contact');
    if (contact && 'IntersectionObserver' in window) {
        new IntersectionObserver(entries => {
            entries.forEach(entry => sticker.classList.toggle('is-hidden', entry.isIntersecting));
        }, { threshold: 0.25 }).observe(contact);
    }
})();
</script>
@endif
