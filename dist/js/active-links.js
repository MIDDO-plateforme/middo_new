// ================================================================
// MIDDO - ACTIVE LINKS AUTO-DETECTION
// Session 24 - Phase 2 - Option C
// ================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔗 Active Links: Initialisation...');
    
    // Récupérer l'URL actuelle
    const currentPath = window.location.pathname;
    const currentUrl = window.location.href;
    
    console.log(' URL actuelle:', currentPath);
    
    // ================================================================
    // FONCTION DE DÉTECTION ET ACTIVATION
    // ================================================================
    
    function setActiveLinks() {
        // Sélectionner TOUS les liens de navigation
        const allLinks = document.querySelectorAll(`
            .sidebar a,
            aside a,
            nav a,
            .mobile-sidebar-nav a,
            [role="navigation"] a
        `);
        
        console.log(` ${allLinks.length} liens trouvés`);
        
        let activeFound = false;
        
        allLinks.forEach(link => {
            const linkPath = new URL(link.href, window.location.origin).pathname;
            
            // Nettoyer les classes active existantes
            link.classList.remove('active');
            
            // Comparaison exacte
            if (linkPath === currentPath) {
                link.classList.add('active');
                console.log(' Lien actif (exact):', linkPath);
                activeFound = true;
            }
            // Comparaison partielle (pour sous-pages)
            else if (currentPath.startsWith(linkPath) && linkPath !== '/') {
                link.classList.add('active');
                console.log(' Lien actif (partiel):', linkPath);
                activeFound = true;
            }
        });
        
        // Si aucun lien actif trouvé, activer "Accueil" par défaut
        if (!activeFound && currentPath === '/') {
            const homeLink = document.querySelector('a[href="/"], a[href="/accueil"]');
            if (homeLink) {
                homeLink.classList.add('active');
                console.log(' Lien actif (défaut): Accueil');
            }
        }
        
        console.log(' Active Links: Prêt !');
    }
    
    // ================================================================
    // EXÉCUTION INITIALE
    // ================================================================
    
    setActiveLinks();
    
    // ================================================================
    // RÉACTIVER SUR CHANGEMENTS DE PAGE (SPA/AJAX)
    // ================================================================
    
    // Écouter les changements d'historique (navigation AJAX)
    window.addEventListener('popstate', setActiveLinks);
    
    // Observer les changements du DOM (si navigation dynamique)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length > 0) {
                setActiveLinks();
            }
        });
    });
    
    // Observer le body pour détecter les nouveaux liens
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});