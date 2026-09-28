import { createScope } from 'animejs';
import { reduceMotion } from './motion-preferences';
import { mount as mountHome } from './home';
import { mount as mountConsultation } from './consultation';
import { mount as mountResults } from './results';
import { mount as mountCatalog } from './catalog';

const mounted = new Map();
const modules = { home: mountHome, consultation: mountConsultation, results: mountResults, catalog: mountCatalog };

export function bootMotion() {
    for (const [root, cleanup] of mounted) if (!root.isConnected) { cleanup(); mounted.delete(root); }
    document.querySelectorAll('[data-motion-root]').forEach((root) => {
        mounted.get(root)?.();
        const setup = modules[root.dataset.motionRoot];
        if (!setup) return;
        if (reduceMotion()) { mounted.set(root, setup(root, true)); return; }
        const scope = createScope({ root }).add(() => setup(root, false));
        mounted.set(root, () => { scope.revert(); mounted.delete(root); });
    });
}
