/**
 * MIDDO - Script de forçage des styles des boutons
 * Ce script force TOUS les boutons à être visibles avec les bonnes couleurs
 */

console.log('🚀 MIDDO: Script force-buttons.js chargé');

// Fonction pour forcer les styles d'un bouton
function forceButtonStyles(button) {
    console.log('🎯 Forçage du bouton:', button.textContent.trim());
    
    // Styles de base
    button.style.setProperty('background', 'linear-gradient(135deg, #a855f7 0%, #ec4899 100%)', 'important');
    button.style.setProperty('color', '#ffffff', 'important');
    button.style.setProperty('border', '2px solid #a855f7', 'important');
    button.style.setProperty('padding', '1rem 2rem', 'important');
    button.style.setProperty('border-radius', '0.5rem', 'important');
    button.style.setProperty('font-weight', '700', 'important');
    button.style.setProperty('font-size', '1rem', 'important');
    button.style.setProperty('cursor', 'pointer', 'important');
    button.style.setProperty('text-decoration', 'none', 'important');
    button.style.setProperty('display', 'inline-block', 'important');
    button.style.setProperty('text-align', 'center', 'important');
    button.style.setProperty('transition', 'all 0.3s ease', 'important');
    button.style.setProperty('box-shadow', '0 4px 6px rgba(168, 85, 247, 0.3)', 'important');
    
    // Si dans un formulaire, pleine largeur
    if (button.closest('form')) {
        button.style.setProperty('width', '100%', 'important');
        button.style.setProperty('margin-top', '1rem', 'important');
        button.style.setProperty('padding', '1rem', 'important');
        button.style.setProperty('font-size', '1.1rem', 'important');
    }
    
    // Effet hover
    button.addEventListener('mouseenter', function() {
        this.style.setProperty('background', 'linear-gradient(135deg, #9333ea 0%, #db2777 100%)', 'important');
        this.style.setProperty('transform', 'translateY(-2px)', 'important');
        this.style.setProperty('box-shadow', '0 6px 12px rgba(168, 85, 247, 0.5)', 'important');
    });
    
    button.addEventListener('mouseleave', function() {
        this.style.setProperty('background', 'linear-gradient(135deg, #a855f7 0%, #ec4899 100%)', 'important');
        this.style.setProperty('transform', 'translateY(0)', 'important');
        this.style.setProperty('box-shadow', '0 4px 6px rgba(168, 85, 247, 0.3)', 'important');
    });
}

// Fonction principale
function forceAllButtons() {
    console.log('🔍 MIDDO: Recherche de tous les boutons...');
    
    // Trouver TOUS les boutons
    const allButtons = document.querySelectorAll('button, input[type="submit"], .btn, [type="button"]');
    
    console.log('✅ MIDDO: ' + allButtons.length + ' boutons trouvés');
    
    allButtons.forEach((button, index) => {
        forceButtonStyles(button);
    });
    
    console.log('🎉 MIDDO: Tous les boutons ont été forcés !');
}

// Exécuter au chargement du DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', forceAllButtons);
} else {
    // DOM déjà chargé
    forceAllButtons();
}

// Aussi exécuter après un court délai pour attraper les éléments chargés dynamiquement
setTimeout(forceAllButtons, 500);
setTimeout(forceAllButtons, 1000);

// Observer les changements du DOM pour forcer les nouveaux boutons
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    if (node.matches('button, input[type="submit"], .btn')) {
                        console.log('🆕 MIDDO: Nouveau bouton détecté, forçage...');
                        forceButtonStyles(node);
                    }
                    // Vérifier les descendants
                    const buttons = node.querySelectorAll('button, input[type="submit"], .btn');
                    if (buttons.length > 0) {
                        console.log('🆕 MIDDO: ' + buttons.length + ' nouveaux boutons détectés');
                        buttons.forEach(forceButtonStyles);
                    }
                }
            });
        }
    });
});

// Observer le body pour les changements
observer.observe(document.body, {
    childList: true,
    subtree: true
});

console.log('👀 MIDDO: Observateur de mutations activé');
