/* =====================================================================
   Plongée Carpentras — CSCV
   Décor du hero : relief sous-marin en fil de fer, projeté en 3D et
   dessiné dans un <canvas> 2D. Pas de WebGL, pas de librairie — la
   projection perspective tient en quelques lignes, donc le fichier pèse
   quelques kilo-octets au lieu de plusieurs centaines.

   Le canvas est purement décoratif : s'il ne se lance pas, le hero garde
   son dégradé de fond et reste parfaitement lisible.
   ===================================================================== */
(() => {
    'use strict';

    const canvas = document.getElementById('hero-canvas');
    if (!canvas || !canvas.getContext) return;

    const ctx = canvas.getContext('2d', { alpha: true });
    if (!ctx) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

    /* --- Paramètres de la scène --- */
    const GRID_HALF_WIDTH = 11;   // étendue latérale, en unités de scène
    const STEP            = 1.25; // écart entre deux lignes
    const NEAR            = 3.6;  // plan le plus proche de la caméra
    const FAR             = 34;   // plan le plus lointain (fondu)
    const CAM_HEIGHT      = 2.2;  // hauteur de la caméra au-dessus du relief
    const HORIZON         = 0.58; // position de la ligne d'horizon (0 = haut)

    // Reliefs « sonar » repérés sur le fond : de simples marqueurs animés.
    const CONTACTS = [
        { x: -6.5, z: 12,   phase: 0 },
        { x:  5.2, z: 19,   phase: 1.9 },
        { x: -2.0, z: 26.5, phase: 3.4 },
    ];

    let width = 0, height = 0, focal = 0, dpr = 1;
    let running = false, rafId = 0, startedAt = 0;

    /* Couleur de trait reprise du thème (variable CSS), pour rester
       cohérent en clair comme en sombre. On lit la variable une seule
       fois par thème et on mémorise les teintes déjà calculées : la
       boucle de rendu ne fait plus aucun accès au style calculé. */
    let brand = '#0d7b8e';
    let useColorMix = false;
    const tints = new Map();

    function refreshColors() {
        brand = getComputedStyle(document.documentElement)
            .getPropertyValue('--c-brand').trim() || '#0d7b8e';
        // color-mix nous évite de parser la couleur nous-mêmes (hex, oklch...).
        useColorMix = window.CSS && CSS.supports
            && CSS.supports('color', `color-mix(in srgb, ${brand} 50%, transparent)`);
        tints.clear();
    }

    function cachedStroke(alpha) {
        const pct = Math.max(0, Math.min(100, Math.round(alpha * 100)));
        let tint = tints.get(pct);
        if (tint === undefined) {
            tint = useColorMix
                ? `color-mix(in srgb, ${brand} ${pct}%, transparent)`
                : `rgba(13, 123, 142, ${(pct / 100).toFixed(2)})`;
            tints.set(pct, tint);
        }
        return tint;
    }

    function resize() {
        const rect = canvas.getBoundingClientRect();
        if (!rect.width || !rect.height) return false;

        dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = rect.width;
        height = rect.height;
        canvas.width = Math.round(width * dpr);
        canvas.height = Math.round(height * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        // Réglé pour que la rangée la plus proche arrive en bas du cadre et
        // que les rangées lointaines convergent vers la ligne d'horizon.
        focal = Math.max(width, 560) * 0.34;
        return true;
    }

    /* Relief du fond : somme de deux ondes, comme une étendue de sable
       ridée par le courant. */
    function seabed(x, z, t) {
        return Math.sin(x * 0.42 + t * 0.35) * 0.28
             + Math.cos(z * 0.31 - t * 0.22) * 0.4
             + Math.sin((x + z) * 0.17) * 0.22;
    }

    /* Projection perspective : caméra à l'origine, regardant vers +z. */
    function project(x, y, z) {
        const scale = focal / z;
        return {
            sx: width * 0.5 + x * scale,
            sy: height * HORIZON + (CAM_HEIGHT - y) * scale,
            scale,
        };
    }

    function depthAlpha(z) {
        const k = 1 - (z - NEAR) / (FAR - NEAR);
        return Math.max(0, Math.min(1, k)) * 0.5 + 0.04;
    }

    function draw(t) {
        ctx.clearRect(0, 0, width, height);
        ctx.lineWidth = 1;
        ctx.lineJoin = 'round';

        const drift = (t * 1.15) % STEP; // défilement continu vers la caméra

        // Lignes transversales (parallèles à l'horizon)
        for (let z = NEAR + drift; z < FAR; z += STEP) {
            ctx.beginPath();
            for (let x = -GRID_HALF_WIDTH; x <= GRID_HALF_WIDTH; x += STEP) {
                const p = project(x, seabed(x, z, t), z);
                if (x === -GRID_HALF_WIDTH) ctx.moveTo(p.sx, p.sy); else ctx.lineTo(p.sx, p.sy);
            }
            ctx.strokeStyle = cachedStroke(depthAlpha(z));
            ctx.stroke();
        }

        // Lignes de fuite (vers l'horizon)
        for (let x = -GRID_HALF_WIDTH; x <= GRID_HALF_WIDTH; x += STEP) {
            ctx.beginPath();
            let started = false;
            for (let z = NEAR + drift; z < FAR; z += STEP) {
                const p = project(x, seabed(x, z, t), z);
                if (!started) { ctx.moveTo(p.sx, p.sy); started = true; } else ctx.lineTo(p.sx, p.sy);
            }
            ctx.strokeStyle = cachedStroke(0.2);
            ctx.stroke();
        }

        drawContacts(t);
    }

    /* Marqueurs pulsants façon écho sonar. */
    function drawContacts(t) {
        CONTACTS.forEach((c) => {
            const z = c.z - (t * 1.15) % (FAR - NEAR);
            if (z < NEAR + 1 || z > FAR - 2) return;

            const p = project(c.x, seabed(c.x, z, t) + 0.35, z);
            const pulse = (Math.sin(t * 1.6 + c.phase) + 1) / 2;
            const r = (4 + pulse * 9) * (p.scale / focal) * 60;

            ctx.beginPath();
            ctx.arc(p.sx, p.sy, Math.max(1.5, r), 0, Math.PI * 2);
            ctx.strokeStyle = cachedStroke(0.32 * (1 - pulse));
            ctx.stroke();

            ctx.beginPath();
            ctx.arc(p.sx, p.sy, 2, 0, Math.PI * 2);
            ctx.fillStyle = cachedStroke(0.5);
            ctx.fill();
        });
    }

    function frame(now) {
        if (!running) return;
        draw((now - startedAt) / 1000);
        rafId = requestAnimationFrame(frame);
    }

    function start() {
        if (running || reduced.matches) return;
        running = true;
        startedAt = performance.now();
        rafId = requestAnimationFrame(frame);
    }

    function stop() {
        running = false;
        if (rafId) cancelAnimationFrame(rafId);
        rafId = 0;
    }

    function init() {
        refreshColors();
        if (!resize()) return;

        if (reduced.matches) { draw(0); return; }   // image fixe, pas d'animation
        start();
    }

    // On n'anime que si le hero est réellement à l'écran, et jamais dans
    // un onglet en arrière-plan : batterie et CPU préservés.
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting && !reduced.matches) start(); else stop();
        }, { threshold: 0 }).observe(canvas);
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) stop();
        else if (!reduced.matches) start();
    });

    let resizeTimer = 0;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => { if (resize() && reduced.matches) draw(0); }, 150);
    });

    reduced.addEventListener('change', () => { stop(); init(); });

    // Le thème peut changer en cours de route : on relit la couleur.
    new MutationObserver(refreshColors).observe(document.documentElement, {
        attributes: true, attributeFilter: ['data-theme'],
    });

    init();
})();
