import { animate } from 'animejs';
import { motion } from './tokens';

export function mount(root, reduced) {
    const panel = root.querySelector('[data-step-panel]');
    if (panel && !reduced) animate(panel, { opacity: { from: 0 }, x: { from: motion.distancePage }, duration: motion.base, ease: 'out(3)' });
    const error = root.querySelector('[data-validation-error]');
    if (error) { error.focus(); if (!reduced) animate(error, { x: [0, -5, 5, -4, 4, 0], duration: 220, ease: 'inOut(3)' }); }
    return () => {};
}
