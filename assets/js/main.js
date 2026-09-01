/* =====================================================================
   Plongée Carpentras — CSCV
   Comportements de l'interface. Vanilla, zéro dépendance, chargé en defer.
   Tout ce qui est ici est un « plus » : la page reste utilisable et
   lisible si ce fichier ne se charge pas.
   ===================================================================== */
(() => {
    'use strict';

    const $  = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
    const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* -----------------------------------------------------------------
       Thème clair / sombre
       L'état initial est posé par un script en ligne dans le <head> pour
       éviter le flash ; ici on ne gère que la bascule manuelle.
       ----------------------------------------------------------------- */
    function initTheme() {
        const toggle = $('#theme-toggle');
        if (!toggle) return;

        const systemDark = window.matchMedia('(prefers-color-scheme: dark)');
        const currentIsDark = () => {
            const forced = document.documentElement.dataset.theme;
            return forced ? forced === 'dark' : systemDark.matches;
        };

        const syncLabel = () => {
            const dark = currentIsDark();
            toggle.setAttribute('aria-label', dark ? 'Passer en thème clair' : 'Passer en thème sombre');
            toggle.setAttribute('aria-pressed', String(dark));
        };

        toggle.addEventListener('click', () => {
            const next = currentIsDark() ? 'light' : 'dark';
            document.documentElement.dataset.theme = next;
            try { localStorage.setItem('cscv-theme', next); } catch (e) { /* navigation privée */ }
            syncLabel();
        });

        systemDark.addEventListener('change', syncLabel);
        syncLabel();
    }

    /* -----------------------------------------------------------------
       En-tête : ombre au scroll + menu mobile
       ----------------------------------------------------------------- */
    /* Sentinelle placée tout en haut du document : elle sert à la fois à
       l'ombre de l'en-tête et au bouton « retour en haut ». */
    let topSentinel = null;
    function getTopSentinel() {
        if (topSentinel) return topSentinel;
        topSentinel = document.createElement('div');
        topSentinel.setAttribute('aria-hidden', 'true');
        topSentinel.style.cssText = 'position:absolute;top:0;height:1px;width:1px;pointer-events:none';
        document.body.prepend(topSentinel);
        return topSentinel;
    }

    function initHeader() {
        const header = $('#site-header');
        const toggle = $('#menu-toggle');

        if (header && 'IntersectionObserver' in window) {
            new IntersectionObserver(
                ([entry]) => header.classList.toggle('is-stuck', !entry.isIntersecting),
                { threshold: 1 }
            ).observe(getTopSentinel());
        }

        if (!toggle) return;
        const close = () => toggle.setAttribute('aria-expanded', 'false');

        toggle.addEventListener('click', () => {
            const open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!open));
            toggle.setAttribute('aria-label', open ? 'Ouvrir le menu' : 'Fermer le menu');
        });

        // On referme dès qu'on part quelque part, ou avec Échap.
        $$('#nav-links a').forEach((link) => link.addEventListener('click', close));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        window.addEventListener('resize', () => { if (window.innerWidth > 900) close(); });
    }

    /* -----------------------------------------------------------------
       Apparitions au scroll — uniquement si le navigateur ne sait pas
       encore faire `animation-timeline: view()` tout seul.
       ----------------------------------------------------------------- */
    function initReveal() {
        const root = document.documentElement;
        const nativeScrollAnimations = window.CSS && CSS.supports('animation-timeline', 'view()');
        const targets = $$('[data-reveal]');

        if (nativeScrollAnimations) return;   // le navigateur s'en charge seul

        if (reducedMotion() || !('IntersectionObserver' in window)) {
            root.classList.remove('reveal-js');
            targets.forEach((el) => el.classList.add('is-in'));
            return;
        }

        // Confirme au script du <head> que le repli est bien pris en charge
        // (sans quoi il redonne la visibilité à tout au bout de 3 secondes).
        root.dataset.revealReady = '1';
        root.classList.add('reveal-js');

        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-in');
                io.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -12% 0px', threshold: 0.1 });

        targets.forEach((el) => io.observe(el));
    }

    /* -----------------------------------------------------------------
       Filtres des formations
       ----------------------------------------------------------------- */
    function initFilters() {
        const bar = $('#formation-filters');
        const grid = $('#formations-grid');
        if (!bar || !grid) return;

        const cards = $$('.formation', grid);
        const empty = $('#formations-empty');

        bar.addEventListener('click', (e) => {
            const chip = e.target.closest('[data-filter]');
            if (!chip) return;

            const filter = chip.dataset.filter;
            $$('[data-filter]', bar).forEach((c) => c.setAttribute('aria-pressed', String(c === chip)));

            let shown = 0;
            cards.forEach((card) => {
                const tags = (card.dataset.tags || '').split(' ');
                const match = filter === 'all' || tags.includes(filter);
                card.hidden = !match;
                if (match) shown++;
            });

            if (empty) empty.hidden = shown > 0;
            grid.setAttribute('aria-live', 'polite');
        });
    }

    /* -----------------------------------------------------------------
       Détail d'une formation dans un <dialog> natif
       ----------------------------------------------------------------- */
    function initFormationDialog() {
        const dialog = $('#formation-dialog');
        if (!dialog || typeof dialog.showModal !== 'function') return;

        const fields = {
            title: $('#fd-title', dialog),
            depth: $('#fd-depth', dialog),
            text:  $('#fd-text', dialog),
            icon:  $('#fd-icon', dialog),
        };

        $$('.formation-open').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                const card = trigger.closest('.formation');
                if (!card) return;
                fields.title.textContent = card.dataset.title || '';
                fields.depth.textContent = card.dataset.depth || '';
                fields.text.textContent  = card.dataset.details || '';
                const svg = card.querySelector('.formation-icon svg');
                if (svg && fields.icon) fields.icon.innerHTML = svg.outerHTML;
                dialog.showModal();
            });
        });

        bindDialogClose(dialog);
    }

    /* Ferme un <dialog> au clic sur le fond ou sur le bouton de fermeture. */
    function bindDialogClose(dialog) {
        dialog.addEventListener('click', (e) => {
            if (e.target.closest('[data-close]')) { dialog.close(); return; }
            // Clic en dehors de la boîte : on compare aux limites réelles.
            const box = dialog.getBoundingClientRect();
            const outside = e.clientX < box.left || e.clientX > box.right ||
                            e.clientY < box.top  || e.clientY > box.bottom;
            if (outside && e.target === dialog) dialog.close();
        });
    }

    function initSimpleDialogs() {
        $$('[data-dialog-open]').forEach((trigger) => {
            const dialog = document.getElementById(trigger.dataset.dialogOpen);
            if (!dialog || typeof dialog.showModal !== 'function') return;
            trigger.addEventListener('click', (e) => { e.preventDefault(); dialog.showModal(); });
            bindDialogClose(dialog);
        });
    }

    /* -----------------------------------------------------------------
       Inclinaison 3D des cartes au survol (pointeur fin uniquement)
       ----------------------------------------------------------------- */
    function initTilt() {
        if (reducedMotion() || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;

        $$('[data-tilt]').forEach((card) => {
            let frame = null;

            const move = (e) => {
                if (frame) return;
                frame = requestAnimationFrame(() => {
                    frame = null;
                    const r = card.getBoundingClientRect();
                    const px = (e.clientX - r.left) / r.width - 0.5;
                    const py = (e.clientY - r.top) / r.height - 0.5;
                    card.style.setProperty('--tilt-y', `${(px * 7).toFixed(2)}deg`);
                    card.style.setProperty('--tilt-x', `${(-py * 7).toFixed(2)}deg`);
                });
            };
            const reset = () => {
                if (frame) { cancelAnimationFrame(frame); frame = null; }
                card.style.setProperty('--tilt-y', '0deg');
                card.style.setProperty('--tilt-x', '0deg');
            };

            card.addEventListener('pointermove', move);
            card.addEventListener('pointerleave', reset);
            card.addEventListener('focusout', reset);
        });
    }

    /* -----------------------------------------------------------------
       HUD « ordinateur de plongée » : profondeur simulée depuis la
       position de scroll, section courante, prochain rendez-vous.
       ----------------------------------------------------------------- */
    function initHud() {
        const hud = $('#dive-hud');
        if (!hud) return;

        const depthOut = $('#hud-depth', hud);
        const bar      = $('#hud-bar', hud);
        const label    = $('#hud-section', hud);
        const maxDepth = Number(hud.dataset.maxDepth || 60);
        const closeBtn = $('#hud-close', hud);

        let ticking = false;
        const update = () => {
            ticking = false;
            const scrollable = document.documentElement.scrollHeight - window.innerHeight;
            const progress = scrollable > 0 ? Math.min(1, Math.max(0, window.scrollY / scrollable)) : 0;
            const depth = Math.round(progress * maxDepth);

            depthOut.firstChild.nodeValue = String(depth);
            bar.style.setProperty('--hud-fill', `${(progress * 100).toFixed(1)}%`);
            hud.classList.toggle('is-visible', window.scrollY > 260 && !hud.dataset.dismissed);
        };

        window.addEventListener('scroll', () => {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(update);
        }, { passive: true });

        // Section courante : la dernière traversée vers le haut de l'écran.
        const sections = $$('[data-hud-label]');
        if (sections.length && label) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) label.textContent = entry.target.dataset.hudLabel;
                });
            }, { rootMargin: '-45% 0px -45% 0px' });
            sections.forEach((s) => io.observe(s));
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                hud.dataset.dismissed = '1';
                hud.classList.remove('is-visible');
            });
        }

        update();
    }

    /* Compte à rebours du prochain rendez-vous (hero + HUD). */
    function initCountdown() {
        const targets = $$('[data-countdown]');
        if (!targets.length) return;

        targets.forEach((el) => {
            const date = new Date(`${el.dataset.countdown}T00:00:00`);
            if (Number.isNaN(date.getTime())) return;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const days = Math.round((date - today) / 86400000);

            el.textContent = days === 0 ? "aujourd'hui"
                : days === 1 ? 'demain'
                : days > 1 ? `dans ${days} jours`
                : 'passé';
        });
    }

    /* -----------------------------------------------------------------
       Bouton « retour en haut »
       ----------------------------------------------------------------- */
    function initToTop() {
        const btn = $('#to-top');
        if (!btn) return;

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(([entry]) => {
                btn.classList.toggle('is-visible', !entry.isIntersecting);
            }, { rootMargin: '400px 0px 0px 0px' }).observe(getTopSentinel());
        }

        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: reducedMotion() ? 'auto' : 'smooth' });
        });
    }

    /* -----------------------------------------------------------------
       Calendrier mensuel (page calendrier.php)
       ----------------------------------------------------------------- */
    function initCalendar() {
        const body = $('#calendar-body');
        if (!body) return;

        const events = Array.isArray(window.CSCV_EVENTS) ? window.CSCV_EVENTS : [];
        const byDate = new Map(events.map((ev) => [ev.date, ev]));
        const label  = $('#calendar-label');
        const dialog = $('#event-dialog');

        const monthNames = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin',
                            'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

        const today = new Date();
        const todayKey = toKey(today);
        let view = new Date(today.getFullYear(), today.getMonth(), 1);

        function toKey(d) {
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        }

        function render() {
            label.textContent = `${monthNames[view.getMonth()]} ${view.getFullYear()}`;
            body.textContent = '';

            const year = view.getFullYear();
            const month = view.getMonth();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            // getDay() : 0 = dimanche. On veut une semaine commençant le lundi.
            const offset = (new Date(year, month, 1).getDay() + 6) % 7;

            let row = document.createElement('tr');
            for (let i = 0; i < offset; i++) row.appendChild(emptyCell());

            for (let day = 1; day <= daysInMonth; day++) {
                if (row.children.length === 7) { body.appendChild(row); row = document.createElement('tr'); }

                const key = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const ev = byDate.get(key);
                const td = document.createElement('td');
                const cell = document.createElement(ev ? 'button' : 'div');

                cell.className = 'day';
                cell.textContent = String(day);
                if (key === todayKey) cell.classList.add('is-today');

                if (ev) {
                    cell.classList.add('has-event');
                    cell.type = 'button';
                    cell.setAttribute('aria-label', `${day} ${monthNames[month]} — ${ev.title}`);
                    cell.addEventListener('click', () => openEvent(ev));
                }

                td.appendChild(cell);
                row.appendChild(td);
            }

            while (row.children.length < 7) row.appendChild(emptyCell());
            body.appendChild(row);
        }

        function emptyCell() {
            const td = document.createElement('td');
            const div = document.createElement('div');
            div.className = 'day is-empty';
            td.appendChild(div);
            return td;
        }

        function openEvent(ev) {
            if (!dialog || typeof dialog.showModal !== 'function') return;
            $('#ev-title', dialog).textContent = ev.title;
            $('#ev-meta', dialog).textContent = formatDate(ev.date) + (ev.time ? ` · ${ev.time.slice(0, 5)}` : '');
            $('#ev-text', dialog).textContent = ev.description || '';
            dialog.showModal();
        }

        function formatDate(iso) {
            const d = new Date(`${iso}T00:00:00`);
            const days = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            return `${days[d.getDay()]} ${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;
        }

        $('#cal-prev').addEventListener('click', () => { view.setMonth(view.getMonth() - 1); render(); });
        $('#cal-next').addEventListener('click', () => { view.setMonth(view.getMonth() + 1); render(); });
        if (dialog) bindDialogClose(dialog);

        render();
    }

    /* -----------------------------------------------------------------
       Démarrage
       ----------------------------------------------------------------- */
    const boot = () => {
        initTheme();
        initHeader();
        initReveal();
        initFilters();
        initFormationDialog();
        initSimpleDialogs();
        initTilt();
        initHud();
        initCountdown();
        initToTop();
        initCalendar();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
