/**
 * accessibility-widget.js — Panel de accesibilidad del sitio.
 *
 * Funciones: tamaño de texto (zoom), filtros de color (alto contraste,
 * escala de grises, saturación alta para daltonismo, inversión), fuente
 * legible, resaltar enlaces, cursor grande, guía de lectura, detener
 * animaciones y lectura en voz alta (síntesis de voz en español):
 * "leer al hacer clic" y "leer página". Preferencias en localStorage.
 */
(function () {
    'use strict';
    if (window.__a11yInit) return;
    window.__a11yInit = true;

    var LS_KEY = 'obs_a11y_prefs';
    var FILTERS = ['a11y-contrast', 'a11y-grayscale', 'a11y-saturate', 'a11y-invert'];
    var TOGGLES = ['a11y-links', 'a11y-font', 'a11y-cursor', 'a11y-guide', 'a11y-noanim'];
    var FONT_LEVELS = [0.85, 1, 1.15, 1.3, 1.45];

    var state = { font: 1, filter: '', toggles: {}, readClick: false };
    try {
        var saved = JSON.parse(localStorage.getItem(LS_KEY) || 'null');
        if (saved && typeof saved === 'object') state = Object.assign(state, saved);
    } catch (e) { /* preferencias corruptas: usar defaults */ }

    // ── Aplicación de estado ──────────────────────────────────────────────
    function apply() {
        var html = document.documentElement;
        FILTERS.forEach(function (f) { html.classList.toggle(f, state.filter === f); });
        TOGGLES.forEach(function (t) { html.classList.toggle(t, !!state.toggles[t]); });
        html.classList.toggle('a11y-reading-click', !!state.readClick);
        document.body.style.zoom = FONT_LEVELS[state.font] === 1 ? '' : String(FONT_LEVELS[state.font]);
        var pct = document.getElementById('a11yFontVal');
        if (pct) pct.textContent = Math.round(FONT_LEVELS[state.font] * 100) + '%';
        document.querySelectorAll('.a11y-opt[data-a11y]').forEach(function (btn) {
            var key = btn.getAttribute('data-a11y');
            var on = state.filter === key || !!state.toggles[key] || (key === 'read-click' && state.readClick);
            btn.classList.toggle('active', on);
            btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
        try { localStorage.setItem(LS_KEY, JSON.stringify(state)); } catch (e) { /* sin storage */ }
    }

    // ── Síntesis de voz ───────────────────────────────────────────────────
    function speak(text) {
        if (!('speechSynthesis' in window) || !text) return;
        window.speechSynthesis.cancel();
        var u = new SpeechSynthesisUtterance(text.replace(/\s+/g, ' ').trim().slice(0, 6000));
        u.lang = 'es-CO';
        u.rate = 0.95;
        var voices = window.speechSynthesis.getVoices();
        var v = voices.find(function (x) { return /^es/.test(x.lang); });
        if (v) u.voice = v;
        window.speechSynthesis.speak(u);
    }
    function stopSpeak() { if ('speechSynthesis' in window) window.speechSynthesis.cancel(); }

    function readPage() {
        var root = document.querySelector('main') || document.body;
        var parts = [];
        root.querySelectorAll('h1,h2,h3,h4,p,li').forEach(function (el) {
            if (el.offsetParent !== null && el.textContent.trim() !== '') parts.push(el.textContent.trim());
        });
        speak(parts.join('. '));
    }

    // Lectura al hacer clic
    document.addEventListener('mouseover', function (e) {
        if (!state.readClick) return;
        var el = e.target.closest('p,h1,h2,h3,h4,h5,li,a,button,span,td,th,label');
        document.querySelectorAll('.a11y-readable-hover').forEach(function (x) { x.classList.remove('a11y-readable-hover'); });
        if (el && !el.closest('.a11y-panel') && !el.closest('.a11y-fab')) el.classList.add('a11y-readable-hover');
    });
    document.addEventListener('click', function (e) {
        if (!state.readClick) return;
        if (e.target.closest('.a11y-panel') || e.target.closest('.a11y-fab')) return;
        var el = e.target.closest('p,h1,h2,h3,h4,h5,li,a,button,span,td,th,label');
        if (!el) return;
        e.preventDefault();
        e.stopPropagation();
        speak(el.textContent);
    }, true);

    // Guía de lectura
    document.addEventListener('mousemove', function (e) {
        var bar = document.querySelector('.a11y-guide-bar');
        if (bar && state.toggles['a11y-guide']) bar.style.top = (e.clientY - 6) + 'px';
    }, { passive: true });

    // ── Interacción del panel ─────────────────────────────────────────────
    function bind() {
        var fab = document.getElementById('a11yFab');
        var panel = document.getElementById('a11yPanel');
        if (!fab || !panel) return;

        fab.addEventListener('click', function () {
            var open = panel.classList.toggle('open');
            fab.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        document.getElementById('a11yClose').addEventListener('click', function () {
            panel.classList.remove('open');
            fab.setAttribute('aria-expanded', 'false');
        });

        panel.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-a11y], [data-a11y-action]');
            if (!btn) return;
            var key = btn.getAttribute('data-a11y');
            var action = btn.getAttribute('data-a11y-action');

            if (action === 'font-up') state.font = Math.min(FONT_LEVELS.length - 1, state.font + 1);
            else if (action === 'font-down') state.font = Math.max(0, state.font - 1);
            else if (action === 'read-page') { readPage(); return; }
            else if (action === 'read-stop') { stopSpeak(); return; }
            else if (action === 'reset') {
                state = { font: 1, filter: '', toggles: {}, readClick: false };
                stopSpeak();
            } else if (key === 'read-click') {
                state.readClick = !state.readClick;
                if (!state.readClick) stopSpeak();
            } else if (FILTERS.indexOf(key) !== -1) {
                state.filter = state.filter === key ? '' : key;
            } else if (key) {
                state.toggles[key] = !state.toggles[key];
            }
            apply();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { bind(); apply(); });
    } else {
        bind();
        apply();
    }
})();
