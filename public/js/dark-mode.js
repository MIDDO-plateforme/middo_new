// ================================================================
// MIDDO - DARK MODE TOGGLE
// Session 24 - Phase 2 - With Icons
// ================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Récupérer le thème sauvegardé
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Créer le bouton de toggle s'il n'existe pas
    let toggleBtn = document.querySelector('.dark-mode-toggle');
    
    if (!toggleBtn) {
        toggleBtn = document.createElement('button');
        toggleBtn.className = 'dark-mode-toggle';
        toggleBtn.setAttribute('aria-label', 'Toggle Dark Mode');
        toggleBtn.innerHTML = '<span class="sun-icon"></span><span class="moon-icon">🌙</span>';
        document.body.appendChild(toggleBtn);
    }

    // Gestion du clic
    toggleBtn.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        console.log(' Theme switched to:', newTheme);
    });

    console.log(' Dark Mode initialized. Current theme:', savedTheme);
});