import { animate, stagger } from 'animejs';
import { motion } from './tokens';

function reveal(elements, delay = 0) {
    const targets = elements.filter((element) => !element.classList.contains('is-revealed'));

    if (!targets.length) return;

    targets.forEach((element) => element.classList.add('is-revealed'));

    animate(targets, {
        opacity: 1,
        translateY: 0,
        delay: stagger(delay),
        duration: 640,
        ease: 'out(4)',
        onComplete: () => {
            targets.forEach((element) => {
                element.style.opacity = '';
                element.style.transform = '';
            });
        },
    });
}

function mountProductRotator(root, reduced) {
    const rotator = root.querySelector('[data-product-rotator]');
    const slides = rotator ? [...rotator.querySelectorAll('[data-showcase-slide]')] : [];
    const toggle = rotator?.querySelector('[data-showcase-toggle]');
    const toggleLabel = toggle?.querySelector('[data-showcase-toggle-label]');

    if (!rotator || slides.length < 2 || reduced) return () => {};

    let current = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
    let timer;
    let animating = false;
    let manualPaused = false;
    let pointerPaused = false;
    let destroyed = false;

    slides.forEach((slide, index) => {
        const active = index === current;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        slide.style.opacity = active ? '1' : '0';
        slide.style.visibility = active ? 'visible' : 'hidden';
    });

    const clearTimer = () => {
        if (!timer) return;
        window.clearTimeout(timer);
        timer = undefined;
    };

    const schedule = (delay = 4400) => {
        clearTimer();

        if (destroyed || manualPaused || pointerPaused || document.hidden) return;

        timer = window.setTimeout(showNext, delay);
    };

    function showNext() {
        if (destroyed || animating || manualPaused || pointerPaused || document.hidden) {
            schedule();
            return;
        }

        animating = true;
        const outgoing = slides[current];
        const next = (current + 1) % slides.length;
        const incoming = slides[next];

        animate(outgoing, {
            opacity: [1, 0],
            translateY: [0, -10],
            duration: 650,
            ease: 'inOut(3)',
            onComplete: () => {
                outgoing.classList.remove('is-active');
                outgoing.setAttribute('aria-hidden', 'true');
                outgoing.style.visibility = 'hidden';
                outgoing.style.transform = '';

                current = next;
                incoming.classList.add('is-active');
                incoming.setAttribute('aria-hidden', 'false');
                incoming.style.visibility = 'visible';

                animate(incoming, {
                    opacity: [0, 1],
                    translateY: [12, 0],
                    duration: 780,
                    ease: 'out(4)',
                    onComplete: () => {
                        incoming.style.opacity = '1';
                        incoming.style.transform = '';
                        animating = false;
                        schedule();
                    },
                });
            },
        });
    }

    const updateToggle = () => {
        if (!toggle || !toggleLabel) return;

        toggle.setAttribute('aria-pressed', manualPaused ? 'true' : 'false');
        toggle.setAttribute('aria-label', manualPaused ? 'Lanjutkan pergantian produk' : 'Jeda pergantian produk');
        toggleLabel.textContent = manualPaused ? 'PUTAR' : 'JEDA';
    };

    const handleToggle = () => {
        manualPaused = !manualPaused;
        updateToggle();
        manualPaused ? clearTimer() : schedule(900);
    };
    const handlePointerEnter = () => {
        pointerPaused = true;
        clearTimer();
    };
    const handlePointerLeave = () => {
        pointerPaused = false;
        schedule(900);
    };
    const handleVisibility = () => {
        document.hidden ? clearTimer() : schedule(900);
    };

    toggle?.addEventListener('click', handleToggle);
    rotator.addEventListener('pointerenter', handlePointerEnter);
    rotator.addEventListener('pointerleave', handlePointerLeave);
    document.addEventListener('visibilitychange', handleVisibility);
    schedule();

    return () => {
        destroyed = true;
        clearTimer();
        toggle?.removeEventListener('click', handleToggle);
        rotator.removeEventListener('pointerenter', handlePointerEnter);
        rotator.removeEventListener('pointerleave', handlePointerLeave);
        document.removeEventListener('visibilitychange', handleVisibility);
    };
}

export function mount(root, reduced) {
    const cleanupRotator = mountProductRotator(root, reduced);

    if (reduced) return cleanupRotator;

    animate(root.querySelectorAll('.hero-copy > *'), {
        opacity: { from: 0 },
        y: { from: motion.distancePage },
        delay: stagger(motion.stagger),
        duration: motion.page,
        ease: 'out(3)',
    });

    root.classList.add('motion-ready');

    const groups = [...root.querySelectorAll('[data-reveal-group]')]
        .reduce((collection, element) => {
            const group = element.dataset.revealGroup;
            collection.set(group, [...(collection.get(group) ?? []), element]);
            return collection;
        }, new Map());

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const group = entry.target.dataset.revealGroup;
            const elements = groups.get(group) ?? [entry.target];

            elements.forEach((element) => observer.unobserve(element));
            reveal(elements, group === 'cards' ? 72 : 105);
        });
    }, { rootMargin: '0px 0px -10% 0px', threshold: .12 });

    groups.forEach((elements) => elements.forEach((element) => observer.observe(element)));

    return () => {
        cleanupRotator();
        observer.disconnect();
        root.classList.remove('motion-ready');
    };
}
