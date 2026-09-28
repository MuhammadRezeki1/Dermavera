import { bootMotion } from './animations/boot';

document.addEventListener('DOMContentLoaded', bootMotion, { once: true });
document.addEventListener('livewire:navigated', bootMotion);
document.addEventListener('livewire:initialized', () => {
    let frame;
    window.Livewire.hook('morph.updated', () => {
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(bootMotion);
    });
});
