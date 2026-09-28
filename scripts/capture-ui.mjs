import { spawn } from 'node:child_process';
import { mkdir, rm, writeFile } from 'node:fs/promises';
import path from 'node:path';

const chromePath = process.env.CHROME_PATH ?? 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const baseUrl = process.env.DERMAVERA_URL ?? 'http://localhost:8080';
const adminEmail = process.env.DERMAVERA_ADMIN_EMAIL;
const adminPassword = process.env.DERMAVERA_ADMIN_PASSWORD;
const savedResultPath = process.env.DERMAVERA_RESULT_PATH;
const outputDir = path.resolve('artifacts/screenshots');
const profileDir = path.resolve('artifacts/.chrome-capture-profile');
const port = 9333;

await mkdir(outputDir, { recursive: true });
await rm(profileDir, { recursive: true, force: true });

const chrome = spawn(chromePath, [
    '--headless=new',
    '--disable-gpu',
    '--hide-scrollbars',
    '--no-first-run',
    '--no-default-browser-check',
    '--disable-background-networking',
    '--remote-allow-origins=*',
    `--remote-debugging-port=${port}`,
    `--user-data-dir=${profileDir}`,
    'about:blank',
], { stdio: 'ignore', windowsHide: true });

const sleep = (milliseconds) => new Promise((resolve) => setTimeout(resolve, milliseconds));

async function waitForDebugger() {
    for (let attempt = 0; attempt < 60; attempt += 1) {
        try {
            const response = await fetch(`http://127.0.0.1:${port}/json/version`);
            if (response.ok) return;
        } catch {}
        await sleep(100);
    }
    throw new Error('Chrome DevTools endpoint did not become ready.');
}

await waitForDebugger();
const target = await fetch(`http://127.0.0.1:${port}/json/new?about:blank`, { method: 'PUT' }).then((response) => response.json());
const socket = new WebSocket(target.webSocketDebuggerUrl);
await new Promise((resolve, reject) => {
    socket.addEventListener('open', resolve, { once: true });
    socket.addEventListener('error', reject, { once: true });
});

let commandId = 0;
const pending = new Map();
const browserErrors = [];

socket.addEventListener('message', (event) => {
    const message = JSON.parse(event.data);
    if (message.id && pending.has(message.id)) {
        const { resolve, reject } = pending.get(message.id);
        pending.delete(message.id);
        return message.error ? reject(new Error(message.error.message)) : resolve(message.result);
    }
    if (message.method === 'Runtime.exceptionThrown') browserErrors.push(message.params.exceptionDetails.text);
    if (message.method === 'Log.entryAdded' && message.params.entry.level === 'error') browserErrors.push(message.params.entry.text);
});

function send(method, params = {}) {
    const id = ++commandId;
    socket.send(JSON.stringify({ id, method, params }));
    return new Promise((resolve, reject) => pending.set(id, { resolve, reject }));
}

async function evaluate(expression) {
    const response = await send('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });
    if (response.exceptionDetails) throw new Error(response.exceptionDetails.text);
    return response.result.value;
}

async function navigate(url) {
    await send('Page.navigate', { url });
    await sleep(1800);
    await evaluate('document.fonts.ready.then(() => true)');
    await sleep(400);
}

async function emulate(width, height) {
    await send('Emulation.setDeviceMetricsOverride', {
        width,
        height,
        deviceScaleFactor: 1,
        mobile: width <= 480,
        screenWidth: width,
        screenHeight: height,
    });
}

async function screenshot(name, url, width, height) {
    await emulate(width, height);
    await navigate(url);
    const dimensions = await evaluate('({ width: innerWidth, height: innerHeight, scrollWidth: document.documentElement.scrollWidth })');
    if (dimensions.width !== width || dimensions.scrollWidth > width + 1) {
        throw new Error(`${name}: viewport ${JSON.stringify(dimensions)} tidak sesuai.`);
    }
    const image = await send('Page.captureScreenshot', { format: 'png', fromSurface: true, captureBeyondViewport: false });
    await writeFile(path.join(outputDir, name), Buffer.from(image.data, 'base64'));
}

async function clickButton(label) {
    const clicked = await evaluate(`(() => {
        const button = [...document.querySelectorAll('button')].find((item) => item.textContent.includes(${JSON.stringify(label)}));
        if (!button) return false;
        button.click();
        return true;
    })()`);
    if (!clicked) throw new Error(`Tombol tidak ditemukan: ${label}`);
    await sleep(1800);
}

async function clickModel(model, value = null) {
    const clicked = await evaluate(`(() => {
        const input = [...document.querySelectorAll('input')].find((item) => item.getAttribute('wire:model') === ${JSON.stringify(model)} && (${JSON.stringify(value)} === null || item.value === ${JSON.stringify(value)}));
        if (!input) return false;
        input.click();
        return true;
    })()`);
    if (!clicked) throw new Error(`Input Livewire tidak ditemukan: ${model}`);
    await sleep(250);
}

async function validateStaticKahfHero() {
    await emulate(1440, 1000);
    await navigate(`${baseUrl}/`);
    const setup = await evaluate(`(() => {
        const hero = document.querySelector('[data-kahf-hero]');
        const products = [...document.querySelectorAll('[data-kahf-product]')];
        const logo = document.querySelector('.site-header .brand-logo');
        return {
            heroReady: Boolean(hero),
            codes: products.map((product) => product.dataset.code),
            imagesReady: products.length === 2 && products.every((product) => {
                const image = product.querySelector('img');
                return image?.complete && image.naturalWidth > 0 && image.src.includes('/images/product-cutout/');
            }),
            noMotionControls: !document.querySelector('[data-showcase-next], [data-showcase-previous], [data-showcase-data]'),
            noSafetyGate: !document.body.textContent.includes('SAFETY GATE') && !document.querySelector('.safety-float'),
            logoReady: Boolean(logo?.complete && logo.naturalWidth > 0 && logo.src.includes('/images/branding/')),
        };
    })()`);
    if (!setup.heroReady || !setup.imagesReady) throw new Error('Hero dua produk Kahf tidak termuat dengan benar.');
    if (setup.codes.join(',') !== 'K07,K03') throw new Error(`Urutan produk hero tidak sesuai: ${setup.codes.join(',')}.`);
    if (!setup.noMotionControls || !setup.noSafetyGate || !setup.logoReady) throw new Error('Hero statis masih memuat elemen lama.');
}

async function validateScrollReveals() {
    await emulate(1440, 1000);
    await navigate(`${baseUrl}/`);

    const groups = ['metrics', 'concern-heading', 'cards', 'process-heading', 'steps', 'catalog-heading', 'products', 'transparency'];
    for (const group of groups) {
        await evaluate(`(() => {
            const target = document.querySelector('[data-reveal-group="${group}"]');
            target?.scrollIntoView({ block: 'center', behavior: 'auto' });
        })()`);
        await sleep(850);
    }

    const status = await evaluate(`(() => {
        const root = document.querySelector('[data-motion-root="home"]');
        const groups = ${JSON.stringify(groups)};
        return {
            motionReady: root?.classList.contains('motion-ready'),
            complete: groups.filter((group) => [...document.querySelectorAll('[data-reveal-group="' + group + '"]')].every((element) => element.classList.contains('is-revealed'))),
        };
    })()`);

    if (!status.motionReady || status.complete.length !== groups.length) {
        throw new Error(`Efek scroll reveal tidak lengkap: ${JSON.stringify(status)}.`);
    }
}

try {
    await send('Page.enable');
    await send('Runtime.enable');
    await send('Log.enable');

    const publicPages = [
        ['home', '/'],
        ['catalog', '/katalog'],
        ['consultation', '/konsultasi/mulai'],
    ];

    for (const [name, route] of publicPages) {
        await screenshot(`${name}-desktop.png`, `${baseUrl}${route}`, 1440, 1000);
        await screenshot(`${name}-mobile.png`, `${baseUrl}${route}`, 375, name === 'home' ? 1200 : 812);
    }

    await screenshot('home-tablet.png', `${baseUrl}/`, 768, 1024);

    await validateStaticKahfHero();
    await validateScrollReveals();

    if (!adminEmail || !adminPassword) throw new Error('Kredensial admin untuk validasi screenshot belum diberikan.');
    await emulate(1440, 1000);
    await navigate(`${baseUrl}/admin/login`);
    const loginResult = await evaluate(`(() => {
        const setValue = (id, value) => {
            const element = document.getElementById(id);
            if (!element) throw new Error('Input login tidak ditemukan: ' + id);
            const setter = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set;
            setter.call(element, value);
            element.dispatchEvent(new Event('input', { bubbles: true }));
            element.dispatchEvent(new Event('change', { bubbles: true }));
        };
        setValue('form.email', ${JSON.stringify(adminEmail)});
        setValue('form.password', ${JSON.stringify(adminPassword)});
        document.querySelector('button[type="submit"]').click();
        return true;
    })()`);
    if (!loginResult) throw new Error('Form login admin tidak dapat dikirim.');
    await sleep(3000);
    const loggedInUrl = await evaluate('location.href');
    if (loggedInUrl.includes('/admin/login')) throw new Error('Login admin gagal saat validasi screenshot.');

    await screenshot('admin-desktop.png', `${baseUrl}/admin`, 1440, 1000);
    await screenshot('admin-mobile.png', `${baseUrl}/admin`, 375, 812);

    let resultUrl = savedResultPath ? new URL(savedResultPath, baseUrl).href : null;
    if (!resultUrl) {
        await emulate(1440, 1000);
        await navigate(`${baseUrl}/konsultasi/mulai`);
        await clickModel('consent');
        await clickButton('Setuju');
        await evaluate(`(() => {
            const age = document.getElementById('age');
            const setter = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set;
            setter.call(age, '24');
            age.dispatchEvent(new Event('input', { bubbles: true }));
            age.dispatchEvent(new Event('change', { bubbles: true }));
        })()`);
        await clickModel('primaryComplaint', 'jerawat_ringan');
        await clickModel('skinType', 'berminyak');
        await clickButton('Lanjutkan');
        await clickModel('sensitive', '0');
        await clickModel('barrierImpaired', '0');
        await clickModel('acneTherapy', '0');
        await clickButton('Lanjutkan');
        await clickButton('Lihat rekomendasi');
        await sleep(3000);
        resultUrl = await evaluate('location.href');
    }
    if (!resultUrl.includes('/hasil/')) throw new Error(`Alur konsultasi tidak mencapai hasil: ${resultUrl}`);
    await screenshot('result-desktop.png', resultUrl, 1440, 1000);
    await screenshot('result-mobile.png', resultUrl, 375, 812);

    if (browserErrors.length) {
        throw new Error(`Browser console memuat error:\n${browserErrors.join('\n')}`);
    }

    process.stdout.write('11 screenshot selesai; hero statis dua produk Kahf dan efek scroll tervalidasi.\n');
} finally {
    socket.close();
    chrome.kill();
    await sleep(300);
    await rm(profileDir, { recursive: true, force: true });
}
