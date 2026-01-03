// MIDDO Notifications Polling - SESSION 37B
class MIDDONotifications {
    constructor() {
        this.userId = document.querySelector('meta[name="user-id"]')?.content;
        this.unreadCount = 0;
        this.lastNotificationId = 0;
        this.init();
    }

    init() {
        console.log('🚀 MIDDO Notifications: Mode Polling activé');
        if (!this.userId) return;

        // Charger notifications initiales
        this.loadNotifications();

        // Polling toutes les 5 secondes
        setInterval(() => this.checkNewNotifications(), 5000);
    }

    async loadNotifications() {
        try {
            const response = await fetch('/api/notifications');
            const data = await response.json();

            if (data.success) {
                this.updateBadge(data.unread_count);
                
                if (data.notifications.length > 0) {
                    this.lastNotificationId = data.notifications[0].id;
                }
            }
        } catch (error) {
            console.error('Erreur chargement:', error);
        }
    }

    async checkNewNotifications() {
        try {
            const response = await fetch('/api/notifications');
            const data = await response.json();

            if (data.success && data.notifications.length > 0) {
                const latest = data.notifications[0];
                
                // Nouvelle notification détectée
                if (latest.id > this.lastNotificationId) {
                    this.lastNotificationId = latest.id;
                    this.handleNewNotification(latest);
                }

                this.updateBadge(data.unread_count);
            }
        } catch (error) {
            console.error('Erreur polling:', error);
        }
    }

    handleNewNotification(notification) {
        console.log(' Nouvelle notification:', notification.message);
        this.showToast(notification);
    }

    showToast(notification) {
        const toast = document.createElement('div');
        toast.className = 'middo-toast';
        toast.innerHTML = `
            <div class="middo-toast-content">
                <div class="middo-toast-icon">✓</div>
                <div class="middo-toast-body">
                    <strong>${notification.type}</strong>
                    <p>${notification.message}</p>
                </div>
                <button class="middo-toast-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => this.removeToast(toast), 5000);
        
        toast.querySelector('.middo-toast-close').onclick = () => this.removeToast(toast);
    }

    removeToast(toast) {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }

    updateBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.middoNotifications = new MIDDONotifications();
});
