/**
 * MIDDO Universal Dark Mode Manager V2.0
 * Version ULTRA-PUISSANTE avec forçage CSS complet
 */

(function() {
  'use strict';

  const STORAGE_KEY = 'theme';
  const ATTRIBUTE = 'data-theme';
  const LIGHT = 'light';
  const DARK = 'dark';
  
  // CSS ULTRA-COMPLET pour forcer TOUS les éléments
  const DARK_CSS = `
    /* ============================================
       DARK MODE ULTRA-PUISSANT - MIDDO V2.0
       Force TOUS les éléments en mode sombre
       ============================================ */
    
    [data-theme='dark'] {
      background-color: #0f172a !important;
      color: #f1f5f9 !important;
    }
    
    [data-theme='dark'] *,
    [data-theme='dark'] body,
    [data-theme='dark'] html {
      background-color: #0f172a !important;
      color: #f1f5f9 !important;
    }
    
    /* --- Conteneurs et Cartes --- */
    [data-theme='dark'] .container,
    [data-theme='dark'] .wrapper,
    [data-theme='dark'] main,
    [data-theme='dark'] section,
    [data-theme='dark'] article {
      background-color: transparent !important;
    }
    
    [data-theme='dark'] .card,
    [data-theme='dark'] .panel,
    [data-theme='dark'] .box,
    [data-theme='dark'] .widget,
    [data-theme='dark'] .mission-card,
    [data-theme='dark'] .notification-card,
    [data-theme='dark'] .chat-message,
    [data-theme='dark'] .kpi-card {
      background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
      border-color: #475569 !important;
      color: #f1f5f9 !important;
    }
    
    [data-theme='dark'] .card:hover,
    [data-theme='dark'] .panel:hover,
    [data-theme='dark'] .mission-card:hover {
      background: linear-gradient(135deg, #334155 0%, #475569 100%) !important;
      box-shadow: 0 8px 30px rgba(99, 102, 241, 0.3) !important;
    }
    
    /* --- Textes et Titres --- */
    [data-theme='dark'] h1,
    [data-theme='dark'] h2,
    [data-theme='dark'] h3,
    [data-theme='dark'] h4,
    [data-theme='dark'] h5,
    [data-theme='dark'] h6,
    [data-theme='dark'] .title,
    [data-theme='dark'] .heading {
      color: #f1f5f9 !important;
    }
    
    [data-theme='dark'] p,
    [data-theme='dark'] span,
    [data-theme='dark'] div,
    [data-theme='dark'] label,
    [data-theme='dark'] li {
      color: #cbd5e1 !important;
    }
    
    [data-theme='dark'] small,
    [data-theme='dark'] .text-muted,
    [data-theme='dark'] .subtitle {
      color: #94a3b8 !important;
    }
    
    /* --- Formulaires et Inputs --- */
    [data-theme='dark'] input,
    [data-theme='dark'] textarea,
    [data-theme='dark'] select {
      background-color: #1e293b !important;
      border-color: #475569 !important;
      color: #f1f5f9 !important;
    }
    
    [data-theme='dark'] input::placeholder,
    [data-theme='dark'] textarea::placeholder {
      color: #64748b !important;
    }
    
    [data-theme='dark'] input:focus,
    [data-theme='dark'] textarea:focus,
    [data-theme='dark'] select:focus {
      background-color: #334155 !important;
      border-color: #6366f1 !important;
      outline: none !important;
    }
    
    /* --- Boutons --- */
    [data-theme='dark'] button,
    [data-theme='dark'] .btn {
      background-color: #334155 !important;
      color: #f1f5f9 !important;
      border-color: #475569 !important;
    }
    
    [data-theme='dark'] button:hover,
    [data-theme='dark'] .btn:hover {
      background-color: #475569 !important;
    }
    
    [data-theme='dark'] button.active,
    [data-theme='dark'] .btn.active,
    [data-theme='dark'] button.btn-primary,
    [data-theme='dark'] .btn-primary {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
      color: white !important;
      border-color: #6366f1 !important;
    }
    
    /* --- Tableaux --- */
    [data-theme='dark'] table {
      background-color: #1e293b !important;
      color: #f1f5f9 !important;
      border-color: #475569 !important;
    }
    
    [data-theme='dark'] th {
      background-color: #334155 !important;
      color: #f1f5f9 !important;
      border-color: #475569 !important;
    }
    
    [data-theme='dark'] td {
      background-color: #1e293b !important;
      color: #cbd5e1 !important;
      border-color: #475569 !important;
    }
    
    [data-theme='dark'] tr:hover {
      background-color: #334155 !important;
    }
    
    /* --- Modals --- */
    [data-theme='dark'] .modal,
    [data-theme='dark'] .modal-content,
    [data-theme='dark'] .modal-body {
      background-color: #1e293b !important;
      color: #f1f5f9 !important;
      border-color: #475569 !important;
    }
    
    [data-theme='dark'] .modal-header,
    [data-theme='dark'] .modal-footer {
      background-color: #334155 !important;
      border-color: #475569 !important;
    }
    
    /* --- Header et Navigation --- */
    [data-theme='dark'] header,
    [data-theme='dark'] nav,
    [data-theme='dark'] .navbar {
      background-color: #1e293b !important;
      border-color: #475569 !important;
    }
    
    [data-theme='dark'] .nav-link,
    [data-theme='dark'] a {
      color: #818cf8 !important;
    }
    
    [data-theme='dark'] .nav-link:hover,
    [data-theme='dark'] a:hover {
      color: #a5b4fc !important;
    }
    
    /* --- Charts et Canvas --- */
    [data-theme='dark'] canvas {
      filter: brightness(0.9);
    }
    
    /* --- Badges et Tags --- */
    [data-theme='dark'] .badge,
    [data-theme='dark'] .tag {
      background-color: #334155 !important;
      color: #cbd5e1 !important;
      border-color: #475569 !important;
    }
    
    /* --- Séparateurs --- */
    [data-theme='dark'] hr {
      border-color: #475569 !important;
    }
    
    /* --- Scrollbars --- */
    [data-theme='dark'] ::-webkit-scrollbar {
      background-color: #1e293b;
    }
    
    [data-theme='dark'] ::-webkit-scrollbar-thumb {
      background-color: #475569;
      border-radius: 4px;
    }
    
    [data-theme='dark'] ::-webkit-scrollbar-thumb:hover {
      background-color: #64748b;
    }
    
    /* --- Toggle Button Styles --- */
    .middo-floating-toggle {
      position: fixed !important;
      bottom: 20px !important;
      right: 20px !important;
      width: 56px !important;
      height: 56px !important;
      border-radius: 50% !important;
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
      color: white !important;
      border: 3px solid rgba(255, 255, 255, 0.2) !important;
      box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4) !important;
      cursor: pointer !important;
      z-index: 9999 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-size: 28px !important;
      line-height: 1 !important;
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
      font-family: 'Segoe UI Emoji', 'Apple Color Emoji', 'Noto Color Emoji', sans-serif !important;
    }
    
    .middo-floating-toggle:hover {
      transform: scale(1.1) rotate(15deg) !important;
      box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6) !important;
    }
    
    .middo-floating-toggle:active {
      transform: scale(0.95) !important;
    }
  `;

  function loadTheme() {
    return localStorage.getItem(STORAGE_KEY) || LIGHT;
  }

  function applyTheme(theme) {
    const html = document.documentElement;
    html.setAttribute(ATTRIBUTE, theme);
    localStorage.setItem(STORAGE_KEY, theme);
    updateToggleButtons(theme);
    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
  }

  function toggleTheme() {
    const current = loadTheme();
    const next = current === DARK ? LIGHT : DARK;
    applyTheme(next);
    
    const btn = document.getElementById('middo-universal-toggle');
    if (btn) {
      btn.style.transform = 'rotate(360deg)';
      setTimeout(() => { btn.style.transform = ''; }, 500);
    }
  }

  function updateToggleButtons(theme) {
    const isDark = theme === DARK;
    const icon = isDark ? '' : '';
    
    const floatingBtn = document.getElementById('middo-universal-toggle');
    if (floatingBtn) floatingBtn.textContent = icon;
    
    const headerBtn = document.getElementById('theme-toggle');
    if (headerBtn) {
      const iconSpan = headerBtn.querySelector('.theme-icon');
      if (iconSpan) iconSpan.textContent = icon;
    }
  }

  function injectStyles() {
    if (!document.getElementById('middo-theme-styles')) {
      const style = document.createElement('style');
      style.id = 'middo-theme-styles';
      style.textContent = DARK_CSS;
      document.head.appendChild(style);
    }
  }

  function createFloatingToggle() {
    if (document.getElementById('theme-toggle')) return;
    if (document.getElementById('middo-universal-toggle')) return;
    
    const btn = document.createElement('button');
    btn.id = 'middo-universal-toggle';
    btn.className = 'middo-floating-toggle';
    btn.setAttribute('aria-label', 'Toggle Dark Mode');
    btn.addEventListener('click', toggleTheme);
    document.body.appendChild(btn);
  }

  function init() {
    injectStyles();
    const savedTheme = loadTheme();
    applyTheme(savedTheme);
    
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => {
        createFloatingToggle();
        updateToggleButtons(savedTheme);
      });
    } else {
      createFloatingToggle();
      updateToggleButtons(savedTheme);
    }
    
    window.addEventListener('storage', (e) => {
      if (e.key === STORAGE_KEY) applyTheme(e.newValue);
    });
  }

  init();

})();