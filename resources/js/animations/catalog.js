import { animate, stagger } from 'animejs';
import { motion } from './tokens';

export function mount(root, reduced) {
    if (reduced) return () => {};

    const cards = [...root.querySelectorAll('.product-card')];
    const observer = new IntersectionObserver((entries) => {
        const visible = entries.filter((entry) => entry.isIntersecting).map((entry) => entry.target);
        if (!visible.length) return;
        visible.forEach((card) => observer.unobserve(card));
        animate(visible, {
            opacity: { from: 0 },
            y: { from: motion.distanceSmall },
            delay: stagger(45),
            duration: 460,
            ease: 'out(3)',
        });
    }, { rootMargin: '0px 0px -6% 0px', threshold: .08 });

    cards.forEach((card) => observer.observe(card));

    return () => observer.disconnect();
}
