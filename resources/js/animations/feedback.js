import { animate } from 'animejs';
export function flash(element, reduced = false) { if (element && !reduced) animate(element, { opacity: { from: 0 }, x: { from: 12 }, duration: 220, ease: 'out(3)' }); }
