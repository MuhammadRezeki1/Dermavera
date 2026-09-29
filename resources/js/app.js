import { bootMotion } from './animations/boot';

document.addEventListener('click', (event) => {
    const link = event.target.closest?.('.wizard-header .back-link');
    if (!link) return;
    event.preventDefault();
    window.dispatchEvent(new CustomEvent('open-exit-modal'));
}, true);

document.addEventListener('DOMContentLoaded', bootMotion, { once: true });
document.addEventListener('livewire:navigated', bootMotion);
document.addEventListener('livewire:initialized', () => {
    let frame;
    window.Livewire.hook('morph.updated', () => {
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(bootMotion);
    });
});
