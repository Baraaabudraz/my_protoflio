/* ==========================================================================
   Kinetic Editorial — public site behaviour (no dependencies)
   Every effect is skipped under prefers-reduced-motion and only runs when its
   elements exist on the page.
   ========================================================================== */
(function () {
    'use strict';

    const root = document.documentElement;
    const isRtl = root.dir === 'rtl';
    const isArabic = root.lang === 'ar';
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const i18n = (() => { try { return JSON.parse(document.getElementById('site-i18n').textContent); } catch (e) { return {}; } })();
    const $ = (s, c = document) => c.querySelector(s);
    const $$ = (s, c = document) => [...c.querySelectorAll(s)];
    const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
    const lerp = (a, b, t) => a + (b - a) * t;

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
        $$('[data-theme-cycle]').forEach(b => b.addEventListener('click', () => {
            const keys = ['light', 'dark', 'ocean', 'sunset'];
            apply(keys[(keys.indexOf(root.getAttribute('data-theme')) + 1) % keys.length]);
        }));
    })();

    /* ── Header: hide on scroll down, active section, progress ──────────── */
    (function header() {
        const head = $('.site-header');
        const bar = $('.progress-bar');
        let last = 0;
        if (head) {
            onScroll(y => {
                head.classList.toggle('is-scrolled', y > 20);
                const menuOpen = $('.menu-overlay')?.classList.contains('is-open');
                head.classList.toggle('is-hidden', !menuOpen && y > 400 && y > last);
                last = y;
            });
        }
        if (bar) {
            onScroll(y => {
                const max = document.documentElement.scrollHeight - innerHeight;
                bar.style.transform = 'scaleX(' + (max > 0 ? y / max : 0) + ')';
            });
        }
        const links = $$('.nav a[href*="#"]');
        const sections = links.map(a => document.getElementById(a.hash.slice(1))).filter(Boolean);
        if (sections.length) {
            onScroll(y => {
                let current = '';
                sections.forEach(s => { if (y >= s.offsetTop - innerHeight * .4) current = s.id; });
                links.forEach(a => a.classList.toggle('is-active', a.hash === '#' + current));
            });
        }
    })();

    /* ── Mobile menu ───────────────────────────────────────────────────── */
    (function menu() {
        const toggle = $('#menuToggle');
        const overlay = $('#menuOverlay');
        if (!toggle || !overlay) return;
        const set = open => {
            overlay.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', String(open));
            toggle.querySelector('.label').textContent = open ? i18n.close : i18n.menu;
            overlay.inert = !open;
            root.style.overflow = open ? 'hidden' : '';
        };
        overlay.inert = true;
        toggle.addEventListener('click', () => set(!overlay.classList.contains('is-open')));
        $$('a', overlay).forEach(a => a.addEventListener('click', () => set(false)));
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && overlay.classList.contains('is-open')) { set(false); toggle.focus(); } });
    })();

    /* ── Split text (letters for Latin, words for Arabic) ──────────────── */
    function split(el) {
        if (el.dataset.splitDone) return;
        el.dataset.splitDone = '1';
        const byWord = isArabic || el.dataset.split === 'words';
        let i = 0;
        const walk = node => {
            [...node.childNodes].forEach(child => {
                if (child.nodeType === Node.TEXT_NODE) {
                    const frag = document.createDocumentFragment();
                    child.textContent.split(/(\s+)/).forEach(part => {
                        if (!part) return;
                        if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
                        const wd = document.createElement('span');
                        wd.className = 'wd';
                        if (byWord) {
                            const w = document.createElement('span');
                            w.className = 'word';
                            w.style.setProperty('--i', i++);
                            w.textContent = part;
                            wd.appendChild(w);
                        } else {
                            [...part].forEach(ch => {
                                const c = document.createElement('span');
                                c.className = 'char';
                                c.style.setProperty('--i', i++);
                                c.textContent = ch;
                                wd.appendChild(c);
                            });
                        }
                        frag.appendChild(wd);
                    });
                    child.replaceWith(frag);
                } else if (child.nodeType === Node.ELEMENT_NODE && !child.matches('img, svg, .no-split')) {
                    walk(child);
                }
            });
        };
        walk(el);
        el.setAttribute('aria-label', el.textContent.replace(/\s+/g, ' ').trim());
        $$('.wd', el).forEach(w => w.setAttribute('aria-hidden', 'true'));
    }
    if (!reduced) $$('[data-split]').forEach(split);

    /* ── Reveal on view ────────────────────────────────────────────────── */
    const io = new IntersectionObserver(entries => entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.classList.add('is-in');
        io.unobserve(e.target);
    }), { threshold: .15, rootMargin: '0px 0px -8% 0px' });
    $$('.reveal, .split, [data-split], .line-grow, .p-cover').forEach(el => io.observe(el));
    window.addEventListener('load', () => { $$('.hero, .hero [data-split], .p-hero [data-split]').forEach(el => el.classList.add('is-in')); });
    if (document.readyState === 'complete') $$('.hero, .hero [data-split]').forEach(el => el.classList.add('is-in'));

    /* ── Scroll-scrubbed statement words ───────────────────────────────── */
    $$('[data-scrub]').forEach(el => {
        if (reduced) return;
        const words = [];
        const walk = node => [...node.childNodes].forEach(child => {
            if (child.nodeType === Node.TEXT_NODE) {
                const frag = document.createDocumentFragment();
                child.textContent.split(/(\s+)/).forEach(p => {
                    if (!p) return;
                    if (/^\s+$/.test(p)) { frag.appendChild(document.createTextNode(p)); return; }
                    const s = document.createElement('span');
                    s.className = 'w';
                    s.textContent = p;
                    words.push(s);
                    frag.appendChild(s);
                });
                child.replaceWith(frag);
            } else if (child.nodeType === Node.ELEMENT_NODE) walk(child);
        });
        walk(el);
        onScroll(() => {
            const r = el.getBoundingClientRect();
            const progress = clamp((innerHeight * .85 - r.top) / (r.height + innerHeight * .35), 0, 1);
            const lit = Math.round(progress * words.length);
            words.forEach((w, idx) => w.classList.toggle('is-lit', idx < lit));
        });
    });

    /* ── Count-up numbers ──────────────────────────────────────────────── */
    $$('[data-count]').forEach(el => {
        const match = el.textContent.trim().match(/^(\D*)(\d+)(.*)$/);
        if (!match || reduced) return;
        const [, pre, num, post] = match;
        const target = parseInt(num, 10);
        const render = v => { el.innerHTML = pre + v + (post ? '<sup>' + post + '</sup>' : ''); };
        render(0);
        const obs = new IntersectionObserver(([e]) => {
            if (!e.isIntersecting) return;
            obs.disconnect();
            const start = performance.now();
            const step = now => {
                const t = clamp((now - start) / 1400, 0, 1);
                render(Math.round(target * (1 - Math.pow(1 - t, 4))));
                if (t < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        }, { threshold: .6 });
        obs.observe(el);
    });

    /* ── Marquees (speed reacts to scroll velocity) ────────────────────── */
    (function marquees() {
        const tracks = $$('.marquee-track');
        if (!tracks.length || reduced) return;
        let velocity = 0;
        let lastY = window.scrollY;
        onScroll(y => { velocity = clamp((y - lastY) * .08, -12, 12); lastY = y; });
        const state = tracks.map(t => ({ el: t, x: 0, dir: parseFloat(t.dataset.dir || '-1'), speed: parseFloat(t.dataset.speed || '0.6') }));
        const loop = () => {
            velocity *= .92;
            state.forEach(s => {
                const half = s.el.scrollWidth / 2;
                if (!half) return;
                s.x += s.dir * (s.speed + Math.abs(velocity));
                if (s.x <= -half) s.x += half;
                if (s.x > 0) s.x -= half;
                s.el.style.transform = 'translate3d(' + s.x + 'px,0,0)';
            });
            requestAnimationFrame(loop);
        };
        requestAnimationFrame(loop);
    })();

    /* ── Parallax images ───────────────────────────────────────────────── */
    if (!reduced) {
        $$('[data-parallax]').forEach(img => {
            const amount = parseFloat(img.dataset.parallax || '0.12');
            onScroll(() => {
                const r = img.parentElement.getBoundingClientRect();
                if (r.bottom < 0 || r.top > innerHeight) return;
                const p = (r.top + r.height / 2 - innerHeight / 2) / innerHeight;
                img.style.transform = 'translate3d(0,' + (p * amount * -100) + '%,0)';
            });
        });
    }

    /* ── Horizontal process (pinned) ───────────────────────────────────── */
    (function process() {
        const section = $('.process');
        const track = $('.process-track');
        const fill = $('.process-progress span');
        if (!section || !track) return;
        const enabled = () => !reduced && innerWidth > 900;
        const layout = () => {
            if (!enabled()) { section.style.height = ''; track.style.transform = ''; return; }
            const distance = track.scrollWidth - innerWidth;
            section.style.height = (distance + innerHeight) + 'px';
        };
        layout();
        window.addEventListener('resize', layout);
        window.addEventListener('load', layout);
        onScroll(() => {
            if (!enabled()) return;
            const distance = track.scrollWidth - innerWidth;
            const p = clamp(-section.getBoundingClientRect().top / (section.offsetHeight - innerHeight), 0, 1);
            track.style.transform = 'translate3d(' + (isRtl ? 1 : -1) * p * distance + 'px,0,0)';
            if (fill) fill.style.transform = 'scaleX(' + p + ')';
        });
    })();

    /* ── Work list floating preview ────────────────────────────────────── */
    (function workPreview() {
        const preview = $('.work-preview');
        const rows = $$('.work-row[data-preview]');
        if (!preview || !rows.length || !finePointer || reduced) return;
        const panes = rows.map(row => {
            const pane = document.createElement('div');
            pane.className = 'pane';
            if (row.dataset.img) {
                const img = new Image();
                img.src = row.dataset.img;
                img.alt = '';
                pane.appendChild(img);
            } else {
                pane.textContent = row.dataset.emoji || '';
            }
            preview.appendChild(pane);
            return pane;
        });
        let mx = 0, my = 0, x = 0, y = 0, active = false;
        document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });
        rows.forEach((row, i) => {
            row.addEventListener('mouseenter', () => { active = true; preview.classList.add('is-on'); panes.forEach((p, j) => p.classList.toggle('is-on', j === i)); });
            row.addEventListener('mouseleave', () => { active = false; preview.classList.remove('is-on'); });
        });
        const loop = () => {
            x = lerp(x, mx, .14);
            y = lerp(y, my, .14);
            if (active || preview.classList.contains('is-on')) preview.style.left = x + 'px', preview.style.top = y + 'px';
            requestAnimationFrame(loop);
        };
        requestAnimationFrame(loop);
    })();

    /* ── Magnetic buttons ──────────────────────────────────────────────── */
    if (finePointer && !reduced) {
        $$('[data-magnetic]').forEach(el => {
            el.addEventListener('mousemove', e => {
                const r = el.getBoundingClientRect();
                const dx = (e.clientX - r.left - r.width / 2) * .25;
                const dy = (e.clientY - r.top - r.height / 2) * .35;
                el.style.transform = 'translate(' + dx + 'px,' + dy + 'px)';
            });
            el.addEventListener('mouseleave', () => { el.style.transform = ''; });
        });
    }

    /* ── Custom cursor ─────────────────────────────────────────────────── */
    (function cursor() {
        const c = $('.cursor');
        if (!c || !finePointer || reduced) return;
        root.classList.add('has-cursor');
        const label = $('.cursor-label', c);
        let mx = -100, my = -100, x = -100, y = -100;
        document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });
        document.addEventListener('mousedown', () => c.classList.add('is-down'));
        document.addEventListener('mouseup', () => c.classList.remove('is-down'));
        document.addEventListener('mouseover', e => {
            const target = e.target.closest('[data-cursor], a, button, summary, label');
            c.classList.toggle('is-label', !!(target && target.dataset.cursor));
            c.classList.toggle('is-hover', !!target && !target.dataset.cursor);
            if (target && target.dataset.cursor) label.textContent = target.dataset.cursor;
        });
        document.addEventListener('mouseleave', () => { mx = my = -100; });
        const loop = () => {
            x = lerp(x, mx, .22);
            y = lerp(y, my, .22);
            c.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0)';
            requestAnimationFrame(loop);
        };
        requestAnimationFrame(loop);
    })();

    /* ── Local clock ───────────────────────────────────────────────────── */
    $$('[data-clock]').forEach(el => {
        const fmt = new Intl.DateTimeFormat(isArabic ? 'ar' : 'en-GB', { hour: '2-digit', minute: '2-digit', timeZone: el.dataset.clock || 'Asia/Gaza' });
        const tick = () => { el.textContent = fmt.format(new Date()); };
        tick();
        setInterval(tick, 30000);
    });

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
    $$('[data-service]').forEach(a => a.addEventListener('click', () => {
        const select = $('#cf-service');
        if (select) select.value = a.dataset.service;
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
