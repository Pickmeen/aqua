/* Plongée Carpentras — interactions front (menu, popups, calendrier, immersion) */
document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initMobileMenu();
    initBackToTop();
    initLegalPopup();
    initCalendar();
    initFormationPanel();
    initDoclessNotice();
    initDepthExperience();
});

/* ---------- Header : fond opaque au scroll ---------- */
function initHeader() {
    const header = document.getElementById('site-header');
    if (!header) return;

    const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 30);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

/* ---------- Menu mobile ---------- */
function initMobileMenu() {
    const toggle = document.getElementById('menu-toggle');
    const links = document.getElementById('nav-links');
    if (!toggle || !links) return;

    const close = () => {
        toggle.classList.remove('open');
        links.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', () => {
        const isOpen = links.classList.toggle('open');
        toggle.classList.toggle('open', isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    links.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
}

/* ---------- Bouton retour en haut ---------- */
function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 500);
    }, { passive: true });

    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

/* ---------- Popups génériques (fermeture, overlay, Échap) ---------- */
function openPopup(popup) {
    popup.classList.add('visible');
    document.body.style.overflow = 'hidden';
}

function closePopup(popup) {
    popup.classList.remove('visible');
    document.body.style.overflow = '';
}

function wireClosablePopup(popup) {
    popup.querySelectorAll('.close-popup').forEach((btn) => btn.addEventListener('click', () => closePopup(popup)));
    popup.addEventListener('click', (e) => { if (e.target === popup) closePopup(popup); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && popup.classList.contains('visible')) closePopup(popup);
    });
}

/* ---------- Popup mentions légales ---------- */
function initLegalPopup() {
    const popup = document.getElementById('mentions-legales-popup');
    const link = document.getElementById('mentions-legales-link');
    if (!popup || !link) return;

    link.addEventListener('click', (e) => {
        e.preventDefault();
        openPopup(popup);
    });

    wireClosablePopup(popup);
}

/* ---------- Petit message quand un document n'est pas encore disponible ---------- */
function initDoclessNotice() {
    document.querySelectorAll('.doc-unavailable').forEach((el) => {
        el.setAttribute('title', 'Document pas encore mis en ligne — contactez le club.');
    });
}

/* ---------- Panneau latéral "détail formation" ---------- */
function initFormationPanel() {
    const cards = document.querySelectorAll('.formation-card');
    const overlay = document.querySelector('.panel-overlay');
    const panel = document.querySelector('.formation-panel');
    if (!cards.length || !panel || !overlay) return;

    const iconEl = panel.querySelector('.formation-icon');
    const titleEl = panel.querySelector('#panel-title');
    const depthEl = panel.querySelector('#panel-depth');
    const descEl = panel.querySelector('#panel-description');

    function open(card) {
        titleEl.textContent = card.dataset.title || '';
        depthEl.textContent = card.dataset.depth || '';
        descEl.textContent = card.dataset.details || '';
        const sourceIcon = card.querySelector('.formation-icon');
        if (sourceIcon) iconEl.innerHTML = sourceIcon.innerHTML;
        overlay.classList.add('visible');
        panel.classList.add('visible');
        document.body.style.overflow = 'hidden';
        panel.querySelector('.close-panel')?.focus();
    }

    function close() {
        overlay.classList.remove('visible');
        panel.classList.remove('visible');
        document.body.style.overflow = '';
    }

    cards.forEach((card) => {
        card.addEventListener('click', () => open(card));
        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(card); }
        });
    });

    overlay.addEventListener('click', close);
    panel.querySelector('.close-panel')?.addEventListener('click', close);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
}

/* ---------- Calendrier interactif ---------- */
function initCalendar() {
    const body = document.getElementById('calendarBody');
    if (!body) return;

    const events = Array.isArray(window.CSCV_EVENTS) ? window.CSCV_EVENTS : [];
    const eventsByDate = new Map();
    events.forEach((ev) => {
        if (!eventsByDate.has(ev.date)) eventsByDate.set(ev.date, []);
        eventsByDate.get(ev.date).push(ev);
    });

    const monthYearLabel = document.getElementById('monthYear');
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalEventTitle');
    const modalDate = document.getElementById('modalEventDate');
    const modalDetails = document.getElementById('modalEventDetails');

    const MONTHS = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    const today = new Date();

    const firstUpcoming = events
        .map((ev) => new Date(ev.date + 'T00:00:00'))
        .filter((d) => d >= new Date(today.getFullYear(), today.getMonth(), today.getDate()))
        .sort((a, b) => a - b)[0];

    let viewYear = (firstUpcoming || today).getFullYear();
    let viewMonth = (firstUpcoming || today).getMonth();

    function toDateKey(y, m, d) {
        return `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    }

    function render() {
        body.innerHTML = '';
        monthYearLabel.textContent = `${MONTHS[viewMonth]} ${viewYear}`;

        const firstDayIndex = (new Date(viewYear, viewMonth, 1).getDay() + 6) % 7; // lundi = 0
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        let row = document.createElement('tr');
        for (let i = 0; i < firstDayIndex; i++) {
            row.appendChild(makeCell('', ['day-cell', 'empty']));
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateKey = toDateKey(viewYear, viewMonth, day);
            const dayEvents = eventsByDate.get(dateKey);
            const classes = ['day-cell'];
            const isToday = viewYear === today.getFullYear() && viewMonth === today.getMonth() && day === today.getDate();
            if (isToday) classes.push('today');
            if (dayEvents) classes.push('has-event');

            const cell = makeCell(String(day), classes);
            if (dayEvents) {
                cell.addEventListener('click', () => showEvents(dayEvents, dateKey));
            }

            row.appendChild(cell);
            if ((firstDayIndex + day) % 7 === 0) {
                body.appendChild(row);
                row = document.createElement('tr');
            }
        }

        if (row.children.length) {
            while (row.children.length < 7) row.appendChild(makeCell('', ['day-cell', 'empty']));
            body.appendChild(row);
        }
    }

    function makeCell(text, classes) {
        const td = document.createElement('td');
        const span = document.createElement('span');
        span.className = classes.join(' ');
        span.textContent = text;
        td.appendChild(span);
        return td;
    }

    function showEvents(dayEvents, dateKey) {
        if (!modal) return;
        const date = new Date(dateKey + 'T00:00:00');
        modalTitle.textContent = dayEvents.map((ev) => ev.title).join(', ');
        modalDate.textContent = date.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
            + (dayEvents[0].time ? ` · ${dayEvents[0].time.slice(0, 5)}` : '');
        modalDetails.textContent = dayEvents.map((ev) => ev.description).filter(Boolean).join('\n\n');
        openPopup(modal);
    }

    prevBtn?.addEventListener('click', () => {
        viewMonth -= 1;
        if (viewMonth < 0) { viewMonth = 11; viewYear -= 1; }
        render();
    });

    nextBtn?.addEventListener('click', () => {
        viewMonth += 1;
        if (viewMonth > 11) { viewMonth = 0; viewYear += 1; }
        render();
    });

    if (modal) wireClosablePopup(modal);

    render();
}

/* =====================================================================
   Expérience "descente en profondeur" : Lenis (smooth scroll) + GSAP/
   ScrollTrigger (apparitions en stagger réversibles, parallaxe légère,
   fond qui s'assombrit, indicateur de profondeur). Se dégrade
   proprement si les librairies CDN ne chargent pas (scroll natif +
   apparitions via IntersectionObserver).
   ===================================================================== */
function initDepthExperience() {
    const hasGsap = typeof window.gsap !== 'undefined' && typeof window.ScrollTrigger !== 'undefined';

    if (hasGsap) {
        document.documentElement.classList.add('gsap-ready');
        gsap.registerPlugin(ScrollTrigger);
        initLenis();
        initGsapReveals();
        initGsapParallax();
    } else {
        document.documentElement.classList.add('no-gsap');
        initFallbackReveals();
    }

    initDepthBackdrop(hasGsap);
}

function initLenis() {
    if (typeof window.Lenis === 'undefined') return;

    const lenis = new Lenis({ duration: 1.05, smoothWheel: true });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
}

function initGsapReveals() {
    const groups = new Map();
    document.querySelectorAll('[data-reveal]').forEach((el) => {
        const parent = el.parentElement;
        if (!groups.has(parent)) groups.set(parent, []);
        groups.get(parent).push(el);
    });

    groups.forEach((els) => {
        els.forEach((el, i) => {
            gsap.fromTo(el,
                { opacity: 0, y: 34 },
                {
                    opacity: 1,
                    y: 0,
                    duration: .7,
                    ease: 'power2.out',
                    delay: (i % 8) * 0.08,
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 88%',
                        toggleActions: 'play reverse play reverse',
                    },
                }
            );
        });
    });
}

function initGsapParallax() {
    document.querySelectorAll('[data-parallax]').forEach((el) => {
        gsap.to(el, {
            yPercent: -10,
            ease: 'none',
            scrollTrigger: {
                trigger: el,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true,
            },
        });
    });
}

function initFallbackReveals() {
    const items = document.querySelectorAll('[data-reveal]');
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('in-view'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    items.forEach((el) => observer.observe(el));
}

function initDepthBackdrop(hasGsap) {
    const backdrop = document.querySelector('.ocean-backdrop');
    if (!backdrop) return;

    const rail = document.querySelector('.depth-rail');
    const fill = document.querySelector('.depth-rail-fill');
    const dot = document.querySelector('.depth-rail-dot');
    const diveEnd = document.querySelector('.site-footer');

    const stops = [
        { at: 0, rgb: [10, 95, 119] },
        { at: .55, rgb: [10, 61, 92] },
        { at: 1, rgb: [4, 16, 31] },
    ];

    function lerp(a, b, t) { return a + (b - a) * t; }

    function colorAt(progress) {
        for (let i = 0; i < stops.length - 1; i++) {
            if (progress >= stops[i].at && progress <= stops[i + 1].at) {
                const t = (progress - stops[i].at) / (stops[i + 1].at - stops[i].at);
                const c1 = stops[i].rgb;
                const c2 = stops[i + 1].rgb;
                return `rgb(${Math.round(lerp(c1[0], c2[0], t))}, ${Math.round(lerp(c1[1], c2[1], t))}, ${Math.round(lerp(c1[2], c2[2], t))})`;
            }
        }
        const last = stops[stops.length - 1].rgb;
        return `rgb(${last.join(', ')})`;
    }

    function update(progress) {
        backdrop.style.backgroundColor = colorAt(progress);
        if (rail && fill && dot) {
            rail.classList.toggle('visible', progress > 0.02 && progress < 0.985);
            fill.style.height = `${progress * 100}%`;
            dot.style.top = `${progress * 100}%`;
        }
    }

    if (hasGsap) {
        ScrollTrigger.create({
            trigger: document.body,
            start: 'top top',
            end: () => `${diveEnd ? diveEnd.offsetTop : document.body.scrollHeight} top`,
            scrub: true,
            onUpdate: (self) => update(self.progress),
        });
    } else {
        const onScroll = () => {
            const max = (diveEnd ? diveEnd.offsetTop : document.body.scrollHeight) - window.innerHeight;
            const progress = max > 0 ? Math.min(1, Math.max(0, window.scrollY / max)) : 0;
            update(progress);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        onScroll();
    }
}
