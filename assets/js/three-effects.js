/**
 * Accents 3D (Three.js) — bulles de verre dans le hero, gemmes facettées
 * qui tournent sur les cartes formations. Amélioration progressive :
 * ne s'active que si WebGL est disponible et si l'utilisateur n'a pas
 * demandé de réduire les animations (prefers-reduced-motion). Sans ça,
 * les icônes plates existantes restent affichées normalement.
 */
import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js';

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function supportsWebGL() {
    try {
        const canvas = document.createElement('canvas');
        return !!(window.WebGLRenderingContext && (canvas.getContext('webgl2') || canvas.getContext('webgl')));
    } catch (e) {
        return false;
    }
}

function start() {
    if (prefersReducedMotion || !supportsWebGL()) return;
    try {
        initHeroBubbles();
    } catch (e) {
        console.warn('Effet 3D du hero indisponible :', e);
    }
    try {
        initFormationGems();
    } catch (e) {
        console.warn('Effet 3D des formations indisponible :', e);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
} else {
    start();
}

/* ---------- Bulles de verre du hero ---------- */
function initHeroBubbles() {
    const canvas = document.getElementById('hero-3d');
    const hero = document.querySelector('.hero');
    if (!canvas || !hero) return;

    const isMobile = window.innerWidth < 700;
    const bubbleCount = isMobile ? 9 : 20;

    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setClearColor(0x000000, 0);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(45, 1, 0.1, 100);
    camera.position.set(0, 0, 9);

    scene.add(new THREE.AmbientLight(0xbfe9ff, 0.7));
    const key = new THREE.DirectionalLight(0xffffff, 1.1);
    key.position.set(3, 5, 6);
    scene.add(key);
    const rim = new THREE.PointLight(0x7ee9e0, 1.3, 24);
    rim.position.set(-4, -2, 4);
    scene.add(rim);

    const geometry = new THREE.IcosahedronGeometry(1, 2);
    const bubbles = [];

    function placeBubble(mesh, initial) {
        mesh.position.x = (Math.random() - 0.5) * 9;
        mesh.position.z = (Math.random() - 0.5) * 6;
        mesh.position.y = initial ? (Math.random() - 0.5) * 8 : -5.5 - Math.random() * 1.5;
    }

    for (let i = 0; i < bubbleCount; i++) {
        const material = new THREE.MeshPhysicalMaterial({
            color: 0xffffff,
            transparent: true,
            opacity: 0.2 + Math.random() * 0.14,
            roughness: 0.05,
            metalness: 0,
            clearcoat: 1,
            clearcoatRoughness: 0.08,
            iridescence: 1,
            iridescenceIOR: 1.3,
            iridescenceThicknessRange: [120, 420],
        });
        const mesh = new THREE.Mesh(geometry, material);
        const scale = 0.12 + Math.random() * 0.34;
        mesh.scale.setScalar(scale);
        placeBubble(mesh, true);
        scene.add(mesh);
        bubbles.push({
            mesh,
            speed: 0.15 + Math.random() * 0.25,
            drift: (Math.random() - 0.5) * 0.15,
            spin: (Math.random() - 0.5) * 0.6,
        });
    }

    let mouseX = 0;
    let mouseY = 0;
    window.addEventListener('pointermove', (e) => {
        mouseX = e.clientX / window.innerWidth - 0.5;
        mouseY = e.clientY / window.innerHeight - 0.5;
    }, { passive: true });

    function resize() {
        const w = hero.clientWidth || 1;
        const h = hero.clientHeight || 1;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h, false);
    }
    resize();
    window.addEventListener('resize', resize);

    let running = false;
    const clock = new THREE.Clock();

    function animate() {
        if (!running) return;
        const delta = Math.min(clock.getDelta(), 0.05);

        bubbles.forEach(({ mesh, speed, drift, spin }) => {
            mesh.position.y += speed * delta * 2.2;
            mesh.position.x += drift * delta;
            mesh.rotation.x += spin * delta;
            mesh.rotation.y += spin * 0.7 * delta;
            if (mesh.position.y > 5.5) {
                placeBubble(mesh, false);
            }
        });

        camera.position.x += (mouseX * 1.2 - camera.position.x) * 0.02;
        camera.position.y += (-mouseY * 0.8 - camera.position.y) * 0.02;
        camera.lookAt(0, 0, 0);

        renderer.render(scene, camera);
        requestAnimationFrame(animate);
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting && !running) {
                running = true;
                clock.getDelta(); // purge le delta accumulé pendant la pause
                requestAnimationFrame(animate);
            } else if (!entry.isIntersecting) {
                running = false;
            }
        });
    }, { threshold: 0.05 });
    observer.observe(hero);
}

/* ---------- Gemmes 3D des cartes formations ---------- */
function initFormationGems() {
    const canvases = document.querySelectorAll('.formation-icon-3d');
    if (!canvases.length) return;

    canvases.forEach((canvas) => {
        try {
            initOneGem(canvas);
        } catch (e) {
            console.warn('Gemme 3D ignorée :', e);
        }
    });
}

function initOneGem(canvas) {
    const card = canvas.closest('.formation-card');
    if (!card) return;

    const size = 54;
    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
    renderer.setClearColor(0x000000, 0);
    renderer.setSize(size, size, false);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(40, 1, 0.1, 10);
    camera.position.set(0, 0, 3.1);

    scene.add(new THREE.AmbientLight(0xffffff, 0.6));
    const key = new THREE.DirectionalLight(0xffffff, 1.1);
    key.position.set(2, 3, 4);
    scene.add(key);
    const rim = new THREE.PointLight(0x3ddad0, 1.4, 12);
    rim.position.set(-2, -1, 2);
    scene.add(rim);

    const geometry = new THREE.IcosahedronGeometry(1, 0);
    const material = new THREE.MeshPhysicalMaterial({
        color: 0x0a3d5c,
        roughness: 0.25,
        metalness: 0.1,
        clearcoat: 1,
        clearcoatRoughness: 0.15,
        iridescence: 0.6,
        iridescenceIOR: 1.25,
    });
    const gem = new THREE.Mesh(geometry, material);
    scene.add(gem);

    let speed = 0.35;
    let targetSpeed = speed;
    card.addEventListener('mouseenter', () => { targetSpeed = 2.2; });
    card.addEventListener('mouseleave', () => { targetSpeed = 0.35; });

    let running = false;
    let rafId = null;
    const clock = new THREE.Clock();

    function animate() {
        if (!running) return;
        const delta = Math.min(clock.getDelta(), 0.05);
        speed += (targetSpeed - speed) * 0.06;
        gem.rotation.y += speed * delta;
        gem.rotation.x += speed * 0.5 * delta;
        renderer.render(scene, camera);
        rafId = requestAnimationFrame(animate);
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting && !running) {
                running = true;
                clock.getDelta();
                animate();
            } else if (!entry.isIntersecting) {
                running = false;
                if (rafId) cancelAnimationFrame(rafId);
            }
        });
    }, { threshold: 0.1 });
    observer.observe(card);
}
