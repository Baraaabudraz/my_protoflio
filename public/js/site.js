/* ==========================================================================
   System Health — public site behaviour (no dependencies)
   Every module only runs when its elements exist; motion respects
   prefers-reduced-motion.
   ========================================================================== */
(function () {
    'use strict';

    const root = document.documentElement;
    const isRtl = root.dir === 'rtl';
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const i18n = (() => { try { return JSON.parse(document.getElementById('site-i18n').textContent); } catch (e) { return {}; } })();
    const $ = (s, c = document) => c.querySelector(s);
    const $$ = (s, c = document) => [...c.querySelectorAll(s)];

    // One shared rAF-throttled scroll loop
    const scrollHandlers = [];
    let ticking = false;
    const onScroll = fn => { scrollHandlers.push(fn); };
    const runScroll = () => { scrollHandlers.forEach(fn => fn(window.scrollY)); ticking = false; };
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(runScroll); } }, { passive: true });
    window.addEventListener('resize', () => requestAnimationFrame(runScroll));

    /* ── Theme ─────────────────────────────────────────────────────────── */
    (function theme() {
        const btn = $('#themeToggle');
        const menu = $('#themeMenu');
        if (!btn || !menu) return;
        const options = $$('[data-theme-choice]');
        const apply = t => {
            root.setAttribute('data-theme', t);
            try { localStorage.setItem('theme', t); } catch (e) {}
            options.forEach(o => o.setAttribute('aria-checked', String(o.dataset.themeChoice === t)));
        };
        const close = focus => { menu.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); if (focus) btn.focus(); };
        apply(root.getAttribute('data-theme') || 'light');
        btn.addEventListener('click', e => { e.stopPropagation(); btn.setAttribute('aria-expanded', String(menu.classList.toggle('is-open'))); });
        options.forEach(o => o.addEventListener('click', () => { apply(o.dataset.themeChoice); close(true); }));
        document.addEventListener('click', e => { if (!menu.contains(e.target)) close(false); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && menu.classList.contains('is-open')) close(true); });
    })();

    /* ── Header: shadow on scroll + active section ─────────────────────── */
    (function header() {
        const head = $('.site-header');
        if (head) onScroll(y => head.classList.toggle('is-scrolled', y > 8));
        const links = $$('.nav a[href*="#"]');
        const sections = links.map(a => document.getElementById(a.hash.slice(1))).filter(Boolean);
        if (!sections.length) return;
        onScroll(y => {
            let current = '';
            sections.forEach(s => { if (y >= s.offsetTop - innerHeight * .35) current = s.id; });
            links.forEach(a => a.classList.toggle('is-active', a.hash === '#' + current));
        });
    })();

    /* ── Mobile menu ───────────────────────────────────────────────────── */
    (function menu() {
        const toggle = $('#menuToggle');
        const panel = $('#menuPanel');
        if (!toggle || !panel) return;
        const icon = $('i', toggle);
        const set = open => {
            panel.hidden = !open;
            toggle.setAttribute('aria-expanded', String(open));
            icon.className = open ? 'fas fa-xmark' : 'fas fa-bars';
        };
        toggle.addEventListener('click', () => set(panel.hidden));
        $$('a', panel).forEach(a => a.addEventListener('click', () => set(false)));
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !panel.hidden) { set(false); toggle.focus(); } });
        window.addEventListener('resize', () => { if (innerWidth > 980 && !panel.hidden) set(false); });
    })();

    /* ── Reveal on view ────────────────────────────────────────────────── */
    (function reveal() {
        const items = $$('.reveal');
        if (reduced || !('IntersectionObserver' in window)) { items.forEach(el => el.classList.add('is-in')); return; }
        const io = new IntersectionObserver(entries => entries.forEach(e => {
            if (!e.isIntersecting) return;
            e.target.classList.add('is-in');
            io.unobserve(e.target);
        }), { threshold: .12, rootMargin: '0px 0px -6% 0px' });
        items.forEach(el => io.observe(el));
    })();

    /* ── Count-up numbers ("30+" → animates 0 → 30, keeps the suffix) ─── */
    $$('[data-count]').forEach(el => {
        const match = el.textContent.trim().match(/^(\D*)(\d+)(.*)$/);
        if (!match || reduced || !('IntersectionObserver' in window)) return;
        const [, pre, num, post] = match;
        const target = parseInt(num, 10);
        const render = v => { el.textContent = pre + v + post; };
        render(0);
        const obs = new IntersectionObserver(([e]) => {
            if (!e.isIntersecting) return;
            obs.disconnect();
            const start = performance.now();
            const step = now => {
                const t = Math.min(1, (now - start) / 1300);
                render(Math.round(target * (1 - Math.pow(1 - t, 3))));
                if (t < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        }, { threshold: .5 });
        obs.observe(el);
    });

    /* ── Scroll progress bar ───────────────────────────────────────────── */
    (function progress() {
        const bar = $('.scroll-progress');
        if (!bar) return;
        onScroll(y => {
            const max = document.documentElement.scrollHeight - innerHeight;
            bar.style.transform = 'scaleX(' + (max > 0 ? y / max : 0) + ')';
        });
    })();

    /* ── Cursor spotlight on glass cards ───────────────────────────────── */
    if (window.matchMedia('(hover: hover)').matches) {
        $$('[data-spotlight]').forEach(card => card.addEventListener('pointermove', e => {
            const r = card.getBoundingClientRect();
            card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
            card.style.setProperty('--my', (e.clientY - r.top) + 'px');
        }));
    }

    /* ── Diagnose: symptom tabs ────────────────────────────────────────── */
    (function diagnose() {
        const tabs = $$('.symptom[role="tab"]');
        if (!tabs.length) return;
        const select = (tab, focus) => {
            tabs.forEach(t => {
                const on = t === tab;
                t.classList.toggle('is-active', on);
                t.setAttribute('aria-selected', String(on));
                t.tabIndex = on ? 0 : -1;
                document.getElementById(t.getAttribute('aria-controls')).classList.toggle('is-active', on);
            });
            if (focus) tab.focus();
        };
        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => select(tab, false));
            tab.addEventListener('keydown', e => {
                const keys = { ArrowDown: 1, ArrowRight: isRtl ? -1 : 1, ArrowUp: -1, ArrowLeft: isRtl ? 1 : -1 };
                if (e.key in keys) { e.preventDefault(); select(tabs[(i + keys[e.key] + tabs.length) % tabs.length], true); }
                if (e.key === 'Home') { e.preventDefault(); select(tabs[0], true); }
                if (e.key === 'End') { e.preventDefault(); select(tabs[tabs.length - 1], true); }
            });
        });
    })();

    /* ── Process pipeline fills as you scroll ──────────────────────────── */
    (function pipeline() {
        const list = $('[data-pipeline]');
        if (!list) return;
        const fill = $('.pipeline-line', list);
        const stages = $$('.stage', list);
        onScroll(() => {
            const r = list.getBoundingClientRect();
            const p = Math.min(1, Math.max(0, (innerHeight * .6 - r.top) / r.height));
            fill.style.setProperty('--progress', reduced ? 1 : p);
            stages.forEach(s => s.classList.toggle('is-done', reduced || s.getBoundingClientRect().top < innerHeight * .6));
        });
    })();

    /* ── Sticky consultation bar ───────────────────────────────────────── */
    (function consultBar() {
        const bar = $('#consultBar');
        if (!bar) return;
        let dismissed = false;
        try { dismissed = sessionStorage.getItem('consultBarClosed') === '1'; } catch (e) {}
        if (dismissed) return;
        bar.hidden = false;
        const contact = $('#contact');
        const hero = $('.hero, .p-hero');
        const set = on => { bar.classList.toggle('is-visible', on); document.body.classList.toggle('consult-on', on); bar.inert = !on; };
        let closed = false;
        onScroll(y => {
            if (closed) return;
            const pastHero = hero ? y > hero.offsetTop + hero.offsetHeight * .7 : y > innerHeight;
            const r = contact ? contact.getBoundingClientRect() : null;
            const atContact = r ? r.top < innerHeight && r.bottom > 0 : false;
            const nearEnd = y + innerHeight > document.documentElement.scrollHeight - 160;
            set(pastHero && !atContact && !nearEnd);
        });
        $('#consultClose').addEventListener('click', () => {
            closed = true;
            set(false);
            try { sessionStorage.setItem('consultBarClosed', '1'); } catch (e) {}
            setTimeout(() => { bar.hidden = true; }, 500);
        });
    })();

    /* ── CV viewer ─────────────────────────────────────────────────────── */
    (function cv() {
        const modal = $('#cvModal');
        if (!modal || typeof modal.showModal !== 'function') return;
        const frame = $('iframe', modal);
        const inline = () => navigator.pdfViewerEnabled !== false && innerWidth > 768;
        $$('[data-cv-open]').forEach(a => a.addEventListener('click', e => {
            if (!inline()) return;
            e.preventDefault();
            if (!frame.getAttribute('src')) frame.setAttribute('src', frame.dataset.src);
            modal.showModal();
            root.style.overflow = 'hidden';
        }));
        modal.addEventListener('close', () => { root.style.overflow = ''; });
        $('[data-cv-close]', modal).addEventListener('click', () => modal.close());
        modal.addEventListener('click', e => { if (e.target === modal) modal.close(); });
    })();

    /* ── Service CTA → preselect in form ───────────────────────────────── */
    $$('[data-service], [data-message]').forEach(a => a.addEventListener('click', () => {
        const select = $('#cf-service');
        const message = $('#cf-message');
        if (select && a.dataset.service) select.value = a.dataset.service;
        if (message && a.dataset.message && !message.value.trim()) message.value = a.dataset.message;
    }));

    /* ── Contact form (server) + WhatsApp alternative ──────────────────── */
    (function contact() {
        const form = $('#contactForm');
        if (!form || !window.fetch) return;
        const submit = $('#contactSubmit');
        const submitLabel = $('.btn-label', submit);
        const ok = $('#contactStatus');
        const err = $('#contactError');
        const show = (box, msg) => { $('span', box).textContent = msg; box.hidden = false; };
        const clearErrors = () => {
            err.hidden = true;
            $$('.is-invalid', form).forEach(el => { el.classList.remove('is-invalid'); el.removeAttribute('aria-invalid'); });
            $$('.field-error', form).forEach(el => { el.textContent = ''; });
        };
        form.addEventListener('submit', async e => {
            e.preventDefault();
            clearErrors();
            ok.hidden = true;
            submit.disabled = true;
            submit.setAttribute('aria-busy', 'true');
            submitLabel.textContent = i18n.sending;
            try {
                const res = await fetch(form.action, { method: 'POST', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(form) });
                const data = await res.json().catch(() => ({}));
                if (res.ok) {
                    form.reset();
                    show(ok, data.message);
                    ok.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'center' });
                } else if (res.status === 422 && data.errors) {
                    let first = null;
                    Object.entries(data.errors).forEach(([field, messages]) => {
                        const input = form.querySelector('[name="' + field + '"]');
                        const holder = document.getElementById('cf-' + field + '-error');
                        if (input) { input.classList.add('is-invalid'); input.setAttribute('aria-invalid', 'true'); first = first || input; }
                        if (holder) holder.textContent = messages[0];
                    });
                    show(err, i18n.checkFields);
                    if (first) first.focus();
                } else {
                    show(err, data.message || i18n.network);
                }
            } catch (x) {
                show(err, i18n.network);
            } finally {
                submit.disabled = false;
                submit.removeAttribute('aria-busy');
                submitLabel.textContent = i18n.send;
            }
        });
        const wa = $('#contactWhatsapp');
        if (wa && form.dataset.whatsapp) {
            wa.addEventListener('click', () => {
                const d = new FormData(form);
                const lines = [i18n.greeting, ''];
                if (d.get('name')) lines.push(i18n.name + ': ' + d.get('name'));
                if (d.get('service')) lines.push(i18n.service + ': ' + d.get('service'));
                if (d.get('budget')) lines.push(i18n.budget + ': ' + d.get('budget'));
                if (d.get('message')) lines.push('', i18n.message + ':', d.get('message'));
                window.open('https://wa.me/' + form.dataset.whatsapp + '?text=' + encodeURIComponent(lines.join('\n')), '_blank', 'noopener');
            });
        }
    })();

    /* ── Copy link ─────────────────────────────────────────────────────── */
    (function copyLink() {
        const btn = $('#copyLink');
        const fb = $('#copyFeedback');
        if (!btn) return;
        btn.addEventListener('click', async () => {
            try { await navigator.clipboard.writeText(btn.dataset.url); fb.textContent = i18n.copied; }
            catch (e) { window.prompt(i18n.copy, btn.dataset.url); }
            setTimeout(() => { fb.textContent = ''; }, 2500);
        });
    })();

    /* ── Gallery lightbox ──────────────────────────────────────────────── */
    (function lightbox() {
        const dialog = $('#lightbox');
        if (!dialog || typeof dialog.showModal !== 'function') return;
        const items = $$('.gallery-item');
        const img = $('#lightboxImage');
        const cap = $('#lightboxCaption');
        const counter = $('#lightboxCounter');
        const openLink = $('#lightboxOpen');
        let index = 0, opener = null;
        const show = i => {
            index = (i + items.length) % items.length;
            const item = items[index];
            img.src = item.dataset.full;
            img.alt = $('img', item).alt;
            cap.textContent = item.dataset.caption || dialog.dataset.title || '';
            counter.textContent = (index + 1) + ' / ' + items.length;
            openLink.href = item.dataset.full;
            [index + 1, index - 1].forEach(n => { const nb = items[(n + items.length) % items.length]; if (nb) new Image().src = nb.dataset.full; });
        };
        const next = () => show(index + 1);
        const prev = () => show(index - 1);
        items.forEach((item, i) => item.addEventListener('click', e => { e.preventDefault(); opener = item; show(i); dialog.showModal(); root.style.overflow = 'hidden'; }));
        dialog.addEventListener('close', () => { root.style.overflow = ''; if (opener) opener.focus(); });
        $('#lightboxClose').addEventListener('click', () => dialog.close());
        $('#lightboxNext').addEventListener('click', next);
        $('#lightboxPrev').addEventListener('click', prev);
        dialog.addEventListener('click', e => { if (e.target === dialog || e.target.id === 'lightboxStage') dialog.close(); });
        dialog.addEventListener('keydown', e => {
            if (e.key === 'ArrowRight') isRtl ? prev() : next();
            if (e.key === 'ArrowLeft') isRtl ? next() : prev();
        });
        if (items.length < 2) { $('#lightboxNext').hidden = true; $('#lightboxPrev').hidden = true; }
        let sx = null;
        const stage = $('#lightboxStage');
        stage.addEventListener('touchstart', e => { sx = e.touches[0].clientX; }, { passive: true });
        stage.addEventListener('touchend', e => {
            if (sx === null) return;
            const d = e.changedTouches[0].clientX - sx;
            if (Math.abs(d) > 50) ((d < 0) !== isRtl ? next() : prev());
            sx = null;
        });
    })();

    runScroll();
})();
