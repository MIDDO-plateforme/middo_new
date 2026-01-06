// ================================================================
// MIDDO - DARK MODE TOGGLE
// Session 24 - Phase 2 - Enhanced with Icons
// ================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log(' Dark Mode: Initialisation...');
    
    // Supprimer tout ancien bouton existant
    const oldButtons = document.querySelectorAll('.dark-mode-toggle');
    oldButtons.forEach(btn => btn.remove());
    
    // Récupérer le thème sauvegardé
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    console.log(' Thème actuel:', savedTheme);
    
    // Créer le nouveau bouton
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'dark-mode-toggle';
    toggleBtn.setAttribute('aria-label', 'Toggle Dark Mode');
    toggleBtn.setAttribute('title', 'Changer le thème');
    
    // Créer les spans pour les icônes
    const sunIcon = document.createElement('span');
    sunIcon.className = 'sun-icon';
    sunIcon.textContent = '';
    
    const moonIcon = document.createElement('span');
    moonIcon.className = 'moon-icon';
    moonIcon.textContent = '';
    
    toggleBtn.appendChild(sunIcon);
    toggleBtn.appendChild(moonIcon);
    
    // Ajouter au body
    document.body.appendChild(toggleBtn);
    console.log('✅ Bouton créé et ajouté');
    
    // Gestion du clic
    toggleBtn.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        console.log('🌙 Thème changé:', currentTheme, '→', newTheme);
    });
    
    console.log('🌙 Dark Mode: Prêt !');
});