/* Plongée Carpentras — interactions front (menu, popups, calendrier, animations) */
document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initMobileMenu();
    initBackToTop();
    initReveal();
    initPopups();
    initLegalPopup();
    initCalendar();
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

/* ---------- Apparition au scroll ---------- */
function initReveal() {
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

/* ---------- Popup "Plus d'infos" (formations) ---------- */
function initPopups() {
    const popup = document.getElementById('info-popup');
    const buttons = document.querySelectorAll('.info-btn');
    if (!popup || !buttons.length) return;

    const title = document.getElementById('popup-title');
    const description = document.getElementById('popup-description');

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            title.textContent = btn.dataset.title || '';
            description.textContent = btn.dataset.description || '';
            openPopup(popup);
        });
    });

    wireClosablePopup(popup);
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
