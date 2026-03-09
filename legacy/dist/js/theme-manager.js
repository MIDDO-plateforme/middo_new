/**
 * SESSION 70 - Theme Manager
 * Dark Mode + LocalStorage + Responsive Menu
 */

console.log(' Theme Manager chargé !');

class ThemeManager {
    constructor() {
        this.currentTheme = this.loadTheme();
        this.init();
    }
    
    init() {
        console.log(' Initialisation Theme Manager...');
        
        // Appliquer le thème sauvegardé
        this.applyTheme(this.currentTheme);
        
        // Créer le toggle button
        this.createToggleButton();
        
        // Créer le menu mobile
        this.createMobileMenu();
        
        // Bind events
        this.bindEvents();
        
        console.log(' Theme Manager OK ! Theme actuel:', this.currentTheme);
    }
    
    loadTheme() {
        // 1. Vérifier LocalStorage
        const saved = localStorage.getItem('middo-theme');
        if (saved) {
            console.log(' Thème chargé depuis LocalStorage:', saved);
            return saved;
        }
        
        // 2. Détecter préférence système
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            console.log(' Préférence système: dark');
            return 'dark';
        }
        
        // 3. Défaut: light
        console.log(' Thème par défaut: light');
        return 'light';
    }
    
    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        this.currentTheme = theme;
        
        // Sauvegarder dans LocalStorage
        localStorage.setItem('middo-theme', theme);
        
        console.log(' Thème appliqué:', theme);
    }
    
    toggleTheme() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
        this.updateToggleButton();
        
        // Animation du bouton
        const toggleBtn = document.querySelector('.theme-toggle');
        if (toggleBtn) {
            toggleBtn.classList.add('switching');
            setTimeout(() => {
                toggleBtn.classList.remove('switching');
            }, 500);
        }
    }
    
    createToggleButton() {
        const existing = document.querySelector('.theme-toggle');
        if (existing) {
            existing.remove();
        }
        
        const button = document.createElement('button');
        button.className = 'theme-toggle';
        button.innerHTML = '<span class="theme-icon">☀️</span><span class="theme-label">Mode Clair</span>';
        button.title = 'Changer de thème';
        
        document.body.appendChild(button);
        
        this.updateToggleButton();
    }
    
    updateToggleButton() {
        const button = document.querySelector('.theme-toggle');
        if (!button) return;
        
        const icon = button.querySelector('.theme-icon');
        const label = button.querySelector('.theme-label');
        
        if (this.currentTheme === 'dark') {
            icon.textContent = '🌙';
            if (label) label.textContent = 'Mode Sombre';
        } else {
            icon.textContent = '';
            if (label) label.textContent = 'Mode Clair';
        }
    }
    
    createMobileMenu() {
        // Vérifier si déjà créé
        if (document.querySelector('.mobile-menu-toggle')) {
            return;
        }
        
        // Toggle button
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'mobile-menu-toggle';
        toggleBtn.innerHTML = '<div class="hamburger"><span></span><span></span><span></span></div>';
        toggleBtn.title = 'Menu';
        
        // Navigation
        const nav = document.createElement('nav');
        nav.className = 'mobile-nav';
        nav.innerHTML = '<a href="/"> Accueil</a><a href="/dashboard-analytics.html"> Dashboard</a><a href="/matching-missions.html"> Missions</a><a href="/upload-premium.html"> Upload</a><a href="/test-chatbot-widget.html"> Chatbot</a><a href="/test-notifications-advanced.html"> Notifications</a>';
        
        // Overlay
        const overlay = document.createElement('div');
        overlay.className = 'mobile-nav-overlay';
        
        document.body.appendChild(toggleBtn);
        document.body.appendChild(nav);
        document.body.appendChild(overlay);
        
        console.log(' Menu mobile créé !');
    }
    
    bindEvents() {
        // Toggle theme
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        }
        
        // Mobile menu toggle
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mobileNav = document.querySelector('.mobile-nav');
        const overlay = document.querySelector('.mobile-nav-overlay');
        
        if (mobileToggle && mobileNav && overlay) {
            mobileToggle.addEventListener('click', () => {
                mobileToggle.classList.toggle('active');
                mobileNav.classList.toggle('active');
                overlay.classList.toggle('active');
            });
            
            overlay.addEventListener('click', () => {
                mobileToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                overlay.classList.remove('active');
            });
            
            // Fermer menu au clic sur lien
            mobileNav.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobileToggle.classList.remove('active');
                    mobileNav.classList.remove('active');
                    overlay.classList.remove('active');
                });
            });
        }
        
        // Détecter changement préférence système
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('middo-theme')) {
                    this.applyTheme(e.matches ? 'dark' : 'light');
                    this.updateToggleButton();
                }
            });
        }
        
        console.log(' Events attachés !');
    }
    
    // Méthode publique pour forcer un thème
    setTheme(theme) {
        if (theme === 'light' || theme === 'dark') {
            this.applyTheme(theme);
            this.updateToggleButton();
        }
    }
}

// Animations Scroll Reveal
class ScrollReveal {
    constructor() {
        this.elements = [];
        this.init();
    }
    
    init() {
        // Ajouter classe reveal aux éléments
        document.querySelectorAll('.card, .kpi-card, .chart-card').forEach(el => {
            el.classList.add('scroll-reveal');
            this.elements.push(el);
        });
        
        // Observer
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        this.observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            this.elements.forEach(el => this.observer.observe(el));
            
            console.log(' Scroll Reveal activé pour', this.elements.length, 'éléments');
        }
    }
}

// Smooth Scroll
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    console.log(' Smooth scroll activé !');
}

// Debounce utility
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Responsive resize handler (debounced)
const handleResize = debounce(() => {
    console.log(' Resize détecté:', window.innerWidth + 'px');
    // Émettre événement custom
    window.dispatchEvent(new CustomEvent('middo-resize', {
        detail: { width: window.innerWidth }
    }));
}, 250);

// Initialize everything
document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ DOM ready !');
    
    // Theme Manager
    window.themeManager = new ThemeManager();
    
    // Scroll Reveal
    window.scrollReveal = new ScrollReveal();
    
    // Smooth Scroll
    initSmoothScroll();
    
    // Resize listener
    window.addEventListener('resize', handleResize);
    
    // Prefers-reduced-motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.documentElement.classList.add('reduce-motion');
        console.log(' Mode réduit mouvement activé');
    }
    
    console.log(' SESSION 70 - Tout initialisé !');
});

// Export pour usage externe
window.ThemeManager = ThemeManager;
