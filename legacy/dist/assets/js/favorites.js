// Système de gestion des favoris MIDDO
class FavoritesManager {
    constructor() {
        this.storageKeys = {
            projects: 'middo_favorite_projects',
            collaborators: 'middo_favorite_collaborators'
        };
        
        // Ajouter les animations CSS si elles n'existent pas
        this.addAnimations();
    }
    
    addAnimations() {
        if (!document.getElementById('toast-animations')) {
            const style = document.createElement('style');
            style.id = 'toast-animations';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(400px); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(400px); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    addFavorite(type, id, data) {
        const key = this.storageKeys[type];
        let favorites = this.getFavorites(type);
        
        if (!favorites.find(f => f.id === id)) {
            favorites.push({ id, ...data, addedAt: new Date().toISOString() });
            localStorage.setItem(key, JSON.stringify(favorites));
            this.showToast('✨ Ajouté aux favoris !', 'success');
            return true;
        }
        return false;
    }
    
    removeFavorite(type, id) {
        const key = this.storageKeys[type];
        let favorites = this.getFavorites(type);
        favorites = favorites.filter(f => f.id !== id);
        localStorage.setItem(key, JSON.stringify(favorites));
        this.showToast('❌ Retiré des favoris', 'info');
        return true;
    }
    
    getFavorites(type) {
        const key = this.storageKeys[type];
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : [];
    }
    
    isFavorite(type, id) {
        const favorites = this.getFavorites(type);
        return favorites.some(f => f.id === id);
    }
    
    showToast(message, type = 'success') {
        // Créer un toast simple sans dépendance à NotificationSystem
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10000;
            font-weight: 600;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    getCount(type) {
        return this.getFavorites(type).length;
    }
}

// Exposer globalement
window.FavoritesManager = new FavoritesManager();

console.log('Systeme de favoris charge !');
