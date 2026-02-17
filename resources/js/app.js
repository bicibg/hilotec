import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);
window.Alpine = Alpine;

// === Scroll Reveal (Intersection Observer) ===
document.addEventListener('DOMContentLoaded', () => {
    const revealElements = document.querySelectorAll('.reveal');

    if (revealElements.length === 0) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
    );

    revealElements.forEach((el) => observer.observe(el));
});

// === Animated Counter (Alpine.js component) ===
Alpine.data('animatedCounter', (targetValue, duration = 2000) => ({
    current: 0,
    target: targetValue,
    started: false,

    startCounting() {
        if (this.started) return;
        this.started = true;

        const startTime = performance.now();
        const step = (now) => {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease-out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            this.current = Math.round(eased * this.target);

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };
        requestAnimationFrame(step);
    },
}));

Alpine.start();
