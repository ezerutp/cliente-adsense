// ==UserScript==
// @name         Gatitas Hot - Capturar videos de Xvideos
// @namespace    https://gatitas-hot.local/
// @version      0.1.0
// @description  Agrega botones para configurar credenciales y enviar el video actual de Xvideos al endpoint de videos.
// @match        https://www.xvideos.com/video*
// @match        https://xvideos.com/video*
// @match        https://www.xvideos.es/video*
// @match        https://xvideos.es/video*
// @grant        GM_addStyle
// @grant        GM_getValue
// @grant        GM_setValue
// @grant        GM_xmlhttpRequest
// @connect      *
// ==/UserScript==

(function () {
    'use strict';

    const STORE = {
        endpoint: 'gatitas_endpoint',
        email: 'gatitas_admin_email',
        password: 'gatitas_admin_password',
    };

    const DEFAULT_ENDPOINT = 'http://localhost:8000/api/video-posts';
    const DEFAULT_ADMIN_EMAIL = '';
    const DEFAULT_ADMIN_PASSWORD = '';

    const state = {
        busy: false,
        modalOpen: false,
    };

    GM_addStyle(`
        .gh-video-tools {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 2147483647;
            display: grid;
            gap: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .gh-video-tools button {
            width: 48px;
            height: 48px;
            border: 0;
            border-radius: 999px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
            color: #fff;
            cursor: pointer;
            font-size: 24px;
            font-weight: 800;
            line-height: 1;
        }

        .gh-video-tools button:disabled {
            cursor: wait;
            opacity: 0.72;
        }

        .gh-video-add {
            background: #e91e63;
        }

        .gh-video-settings {
            background: #222;
            font-size: 20px !important;
        }

        .gh-video-toast {
            position: fixed;
            right: 18px;
            bottom: 130px;
            z-index: 2147483647;
            max-width: min(360px, calc(100vw - 36px));
            border-radius: 10px;
            background: #222;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
            color: #fff;
            font: 13px/1.45 Arial, Helvetica, sans-serif;
            padding: 12px 14px;
        }

        .gh-video-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 2147483646;
            display: grid;
            place-items: center;
            background: rgba(0, 0, 0, 0.58);
            font-family: Arial, Helvetica, sans-serif;
        }

        .gh-video-modal {
            width: min(440px, calc(100vw - 28px));
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
            color: #222;
            padding: 18px;
        }

        .gh-video-modal h2 {
            margin: 0 0 14px;
            color: #222;
            font-size: 18px;
        }

        .gh-video-modal label {
            display: block;
            margin-top: 12px;
            color: #444;
            font-size: 12px;
            font-weight: 700;
        }

        .gh-video-modal input {
            box-sizing: border-box;
            width: 100%;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            color: #222;
            font-size: 14px;
            padding: 10px;
        }

        .gh-video-modal p {
            margin: 12px 0 0;
            color: #666;
            font-size: 12px;
            line-height: 1.45;
        }

        .gh-video-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 16px;
        }

        .gh-video-modal-actions button {
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 14px;
        }

        .gh-video-cancel {
            background: #eee;
            color: #222;
        }

        .gh-video-save {
            background: #e91e63;
            color: #fff;
        }
    `);

    function getConfig() {
        return {
            endpoint: GM_getValue(STORE.endpoint, DEFAULT_ENDPOINT),
            email: GM_getValue(STORE.email, DEFAULT_ADMIN_EMAIL),
            password: GM_getValue(STORE.password, DEFAULT_ADMIN_PASSWORD),
        };
    }

    function saveConfig(config) {
        GM_setValue(STORE.endpoint, config.endpoint.trim());
        GM_setValue(STORE.email, config.email.trim());
        GM_setValue(STORE.password, config.password);
    }

    function decodeHtml(value) {
        const textarea = document.createElement('textarea');
        textarea.innerHTML = value || '';
        return textarea.value;
    }

    function getTitle() {
        const selectors = [
            '#title-auto-tr',
            'meta[property="og:title"]',
            'h2.page-title',
            'title',
        ];

        for (const selector of selectors) {
            const element = document.querySelector(selector);

            if (!element) {
                continue;
            }

            const value = element.tagName === 'META'
                ? element.getAttribute('content')
                : element.textContent;

            const cleaned = (value || '')
                .replace(/\s+\d+\s*min\b.*/i, '')
                .replace(/\s+\d{3,4}p\b.*/i, '')
                .replace(/\s*-\s*XVIDEOS.*$/i, '')
                .trim();

            if (cleaned) {
                return cleaned;
            }
        }

        return 'Video Xvideos';
    }

    function getEmbedIframe() {
        const embedInput = document.querySelector('#copy-video-embed');

        if (embedInput && embedInput.value) {
            return decodeHtml(embedInput.value).trim();
        }

        const encodedId = getEncodedVideoId();

        if (!encodedId) {
            return null;
        }

        return `<iframe src="https://www.xvideos.com/embedframe/${encodedId}" frameborder="0" width="510" height="400" scrolling="no" allowfullscreen="allowfullscreen"></iframe>`;
    }

    function getThumbnailUrl() {
        const candidates = [
            document.querySelector('meta[property="og:image"]')?.getAttribute('content'),
            document.querySelector('link[rel="image_src"]')?.getAttribute('href'),
            matchPlayerValue('setThumbUrl169'),
            matchPlayerValue('setThumbUrl'),
            document.querySelector('#html5video img, .player img')?.getAttribute('src'),
        ].filter(Boolean);

        for (const candidate of candidates) {
            const url = decodeHtml(String(candidate)).trim();

            if (/^https?:\/\//i.test(url)) {
                return url;
            }
        }

        return '';
    }

    function matchPlayerValue(method) {
        const escapedMethod = method.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const match = document.documentElement.innerHTML.match(new RegExp(`${escapedMethod}\\(['"]([^'"]+)['"]\\)`));

        return match?.[1] || '';
    }

    function getEncodedVideoId() {
        const linkInput = document.querySelector('#copy-video-link');
        const candidates = [
            linkInput?.value,
            window.location.pathname,
            document.documentElement.innerHTML.match(/setEncodedIdVideo\(['"]([^'"]+)['"]\)/)?.[1],
        ].filter(Boolean);

        for (const candidate of candidates) {
            const value = String(candidate);
            const fromPath = value.match(/\/video\.([^/?#]+)/i)?.[1];

            if (fromPath) {
                return fromPath;
            }

            const direct = value.match(/^[a-z0-9]+$/i)?.[0];

            if (direct) {
                return direct;
            }
        }

        return null;
    }

    function getPayload() {
        const iframe = getEmbedIframe();

        if (!iframe) {
            throw new Error('No pude detectar el iframe del video.');
        }

        const config = getConfig();

        if (!config.endpoint || !config.email || !config.password) {
            throw new Error('Completa endpoint, correo y contraseña en configuración.');
        }

        return {
            endpoint: config.endpoint,
            body: {
                admin_email: config.email,
                admin_password: config.password,
                title: getTitle(),
                description: '',
                iframe,
                thumbnail_url: getThumbnailUrl(),
                is_active: true,
            },
        };
    }

    function sendCurrentVideo(button) {
        if (state.busy) {
            return;
        }

        let payload;

        try {
            payload = getPayload();
        } catch (error) {
            toast(error.message, true);
            openSettings();
            return;
        }

        state.busy = true;
        button.disabled = true;
        button.textContent = '...';
        toast('Enviando video...');

        GM_xmlhttpRequest({
            method: 'POST',
            url: payload.endpoint,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            data: JSON.stringify(payload.body),
            timeout: 30000,
            onload(response) {
                state.busy = false;
                button.disabled = false;
                button.textContent = '+';

                if (response.status >= 200 && response.status < 300) {
                    toast(`Video agregado: ${payload.body.title}`);
                    return;
                }

                toast(readError(response), true);
            },
            onerror() {
                state.busy = false;
                button.disabled = false;
                button.textContent = '+';
                toast('No se pudo conectar con el endpoint.', true);
            },
            ontimeout() {
                state.busy = false;
                button.disabled = false;
                button.textContent = '+';
                toast('Timeout llamando al endpoint.', true);
            },
        });
    }

    function readError(response) {
        try {
            const json = JSON.parse(response.responseText || '{}');
            const errors = json.errors ? Object.values(json.errors).flat().join(' ') : '';
            return errors || json.message || `Error ${response.status}`;
        } catch {
            return response.responseText || `Error ${response.status}`;
        }
    }

    function toast(message, isError = false) {
        document.querySelector('.gh-video-toast')?.remove();

        const element = document.createElement('div');
        element.className = 'gh-video-toast';
        element.style.background = isError ? '#b91c1c' : '#222';
        element.textContent = message;
        document.body.appendChild(element);

        window.setTimeout(() => {
            element.remove();
        }, isError ? 7000 : 3500);
    }

    function openSettings() {
        if (state.modalOpen) {
            return;
        }

        state.modalOpen = true;
        const config = getConfig();
        const backdrop = document.createElement('div');
        backdrop.className = 'gh-video-modal-backdrop';
        backdrop.innerHTML = `
            <form class="gh-video-modal">
                <h2>Configurar envío</h2>
                <label>
                    Endpoint
                    <input name="endpoint" type="url" required value="${escapeAttr(config.endpoint)}" placeholder="https://tu-dominio.com/api/video-posts">
                </label>
                <label>
                    Correo admin
                    <input name="email" type="email" required value="${escapeAttr(config.email)}" placeholder="admin@test.com">
                </label>
                <label>
                    Contraseña admin
                    <input name="password" type="password" required value="${escapeAttr(config.password)}">
                </label>
                <p>POC: Tampermonkey guardará estos datos en su storage local del navegador. Más adelante conviene cambiarlo por token o API key.</p>
                <div class="gh-video-modal-actions">
                    <button class="gh-video-cancel" type="button">Cancelar</button>
                    <button class="gh-video-save" type="submit">Guardar</button>
                </div>
            </form>
        `;

        document.body.appendChild(backdrop);

        const close = () => {
            state.modalOpen = false;
            backdrop.remove();
        };

        backdrop.querySelector('.gh-video-cancel').addEventListener('click', close);
        backdrop.addEventListener('click', (event) => {
            if (event.target === backdrop) {
                close();
            }
        });
        backdrop.querySelector('form').addEventListener('submit', (event) => {
            event.preventDefault();
            const form = new FormData(event.currentTarget);
            saveConfig({
                endpoint: form.get('endpoint') || '',
                email: form.get('email') || '',
                password: form.get('password') || '',
            });
            toast('Configuración guardada.');
            close();
        });
    }

    function escapeAttr(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function mount() {
        if (document.querySelector('.gh-video-tools')) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'gh-video-tools';

        const settingsButton = document.createElement('button');
        settingsButton.className = 'gh-video-settings';
        settingsButton.type = 'button';
        settingsButton.title = 'Configurar Gatitas Hot';
        settingsButton.textContent = '⚙';
        settingsButton.addEventListener('click', openSettings);

        const addButton = document.createElement('button');
        addButton.className = 'gh-video-add';
        addButton.type = 'button';
        addButton.title = 'Agregar video a Gatitas Hot';
        addButton.textContent = '+';
        addButton.addEventListener('click', () => sendCurrentVideo(addButton));

        wrapper.append(settingsButton, addButton);
        document.body.appendChild(wrapper);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mount);
    } else {
        mount();
    }
})();
