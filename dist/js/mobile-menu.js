// ================================================================
// MIDDO - MOBILE HAMBURGER MENU
// Session 24 - Phase 2 - Option B
// ================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log(' Mobile Menu: Initialisation...');
    
    // Vérifier si on est sur mobile/tablette
    const isMobile = window.innerWidth <= 768;
    
    if (!isMobile) {
        console.log(' Desktop détecté, menu hamburger non nécessaire');
        return;
    }
    
    // ================================================================
    // CRÉER LE BOUTON HAMBURGER
    // ================================================================
    
    const hamburgerBtn = document.createElement('button');
    hamburgerBtn.className = 'hamburger-menu';
    hamburgerBtn.setAttribute('aria-label', 'Toggle Menu');
    hamburgerBtn.innerHTML = `
        <span></span>
        <span></span>
        <span></span>
    `;
    
    // ================================================================
    // CRÉER L'OVERLAY
    // ================================================================
    
    const overlay = document.createElement('div');
    overlay.className = 'mobile-sidebar-overlay';
    
    // ================================================================
    // CRÉER LA SIDEBAR MOBILE
    // ================================================================
    
    const mobileSidebar = document.createElement('div');
    mobileSidebar.className = 'mobile-sidebar';
    
    // En-tête
    const sidebarHeader = document.createElement('div');
    sidebarHeader.className = 'mobile-sidebar-header';
    sidebarHeader.innerHTML = '<h2>MIDDO</h2>';
    
    // Navigation
    const sidebarNav = document.createElement('nav');
    sidebarNav.className = 'mobile-sidebar-nav';
    
    // Récupérer les liens de la sidebar existante
    const desktopSidebar = document.querySelector('.sidebar, aside');
    if (desktopSidebar) {
        const links = desktopSidebar.querySelectorAll('a');
        links.forEach(link => {
            const mobileLink = link.cloneNode(true);
            sidebarNav.appendChild(mobileLink);
        });
    } else {
        // Liens par défaut si pas de sidebar
        sidebarNav.innerHTML = `
            <a href="/"><i></i> Accueil</a>
            <a href="/annuaire"><i></i> Annuaire</a>
            <a href="/banque"><i></i> Banque</a>
            <a href="/projets"><i></i> Projets</a>
            <a href="/messages"><i></i> Messages</a>
            <a href="/visio"><i></i> Visio</a>
            <a href="/analytics"><i></i> Analytics</a>
            <a href="/travail"><i></i> Travail</a>
        `;
    }
    
    // Assembler la sidebar
    mobileSidebar.appendChild(sidebarHeader);
    mobileSidebar.appendChild(sidebarNav);
    
    // Ajouter tout au DOM
    document.body.appendChild(hamburgerBtn);
    document.body.appendChild(overlay);
    document.body.appendChild(mobileSidebar);
    
    console.log(' Menu hamburger créé');
    
    // ================================================================
    // GESTION DES ÉVÉNEMENTS
    // ================================================================
    
    // Toggle menu
    function toggleMenu() {
        hamburgerBtn.classList.toggle('active');
        overlay.classList.toggle('active');
        mobileSidebar.classList.toggle('active');
        
        // Empêcher le scroll quand le menu est ouvert
        if (mobileSidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
    
    // Clic sur le bouton hamburger
    hamburgerBtn.addEventListener('click', toggleMenu);
    
    // Clic sur l'overlay (fermer le menu)
    overlay.addEventListener('click', toggleMenu);
    
    // Clic sur un lien (fermer le menu)
    sidebarNav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            toggleMenu();
        });
    });
    
    // Gestion du resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            // Masquer le menu mobile sur desktop
            hamburgerBtn.classList.remove('active');
            overlay.classList.remove('active');
            mobileSidebar.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
    console.log('🍔 Mobile Menu: Prêt !');
});