import { animate, stagger } from 'animejs';
import { motion } from './tokens';

export function mount(root, reduced) {
    const cards = root.querySelectorAll('[data-result-card]');
    if (!reduced) animate(cards, { opacity: { from: 0 }, y: { from: motion.distancePage }, delay: stagger(motion.stagger), duration: 480, ease: 'out(3)' });
    root.querySelectorAll('[data-score]').forEach((element) => {
        const finalValue = Number(element.dataset.score);
        if (reduced) { element.textContent = finalValue.toFixed(4); return; }
        const state = { value: 0 };
        animate(state, { value: finalValue, duration: motion.score, ease: 'out(3)', onRender: () => { element.textContent = Number(state.value).toFixed(4); }, onComplete: () => { element.textContent = finalValue.toFixed(4); } });
    });
    if (!reduced) root.querySelectorAll('[data-width]').forEach((element) => { const width = Number(element.dataset.width); element.style.width = '0%'; animate(element, { width: `${width}%`, duration: 800, ease: 'out(3)' }); });
    return () => {};
}
