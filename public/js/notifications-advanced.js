// ============================================
// NOTIFICATIONS AMÉLIORÉES - SESSION 65
// ============================================

class AdvancedNotificationManager {
    constructor() {
        this.pollingInterval = 30000; // 30 secondes
        this.notifications = [];
        this.toastQueue = [];
        this.soundEnabled = localStorage.getItem('notificationSound') !== 'false';
        this.init();
    }

    init() {
        console.log('🔔 AdvancedNotificationManager - MODE POLLING ACTIVÉ (30s)');
        
        // Load from localStorage
        this.loadFromStorage();
        
        // Fetch initial
        this.fetchNotifications();
        
        // Start polling
        setInterval(() => this.fetchNotifications(), this.pollingInterval);
        
        // Event listeners
        this.attachEventListeners();
    }

    async fetchNotifications() {
        try {
            const response = await fetch('/api/notifications');
            const data = await response.json();
            
            this.updateNotifications(data);
        } catch (error) {
            console.error(' Erreur fetch notifications:', error);
        }
    }

    updateNotifications(newNotifications) {
        // Detect new notifications
        const oldIds = this.notifications.map(n => n.id);
        const reallyNew = newNotifications.filter(n => !oldIds.includes(n.id));
        
        // Show toasts for new ones
        reallyNew.forEach(notif => {
            this.showToast(notif.type || 'info', notif.title, notif.message);
        });
        
        this.notifications = newNotifications;
        this.saveToStorage();
        this.updateUI();
    }

    showToast(type, title, message, duration = 5000) {
        const icons = {
            success: '',
            info: 'ℹ',
            warning: '',
            error: ''
        };

        const toast = document.createElement('div');
        toast.className = `toast ${type} slide-in`;
        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || 'ℹ'}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()"></button>
            <div class="toast-progress"></div>
        `;

        document.body.appendChild(toast);

        // Play sound
        if (this.soundEnabled) {
            this.playNotificationSound();
        }

        // Auto-dismiss
        setTimeout(() => {
            toast.classList.add('slide-out');
            setTimeout(() => toast.remove(), 400);
        }, duration);
    }

    playNotificationSound() {
        // Simple beep using Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (e) {
            console.log('Son désactivé');
        }
    }

    updateUI() {
        const unreadCount = this.notifications.filter(n => !n.isRead).length;
        
        // Update badge
        const badge = document.getElementById('notification-badge');
        if (badge) {
            badge.textContent = unreadCount;
            badge.classList.toggle('has-unread', unreadCount > 0);
            badge.classList.add('animate-count');
            setTimeout(() => badge.classList.remove('animate-count'), 400);
        }

        // Update panel
        this.updatePanel();
    }

    updatePanel() {
        const panel = document.getElementById('notification-panel');
        if (!panel) return;

        if (this.notifications.length === 0) {
            panel.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">Aucune notification</div>';
            return;
        }

        panel.innerHTML = this.notifications.map(notif => `
            <div class="notification-item ${notif.isRead ? '' : 'unread'}" 
                 data-id="${notif.id}"
                 onclick="notificationManager.markAsRead(${notif.id})">
                <div class="notification-icon">${this.getIcon(notif.type)}</div>
                <div class="notification-content">
                    <div class="notification-title">${notif.title}</div>
                    <div class="notification-message">${notif.message}</div>
                    <div class="notification-time">${this.formatTime(notif.createdAt)}</div>
                </div>
            </div>
        `).join('');
    }

    async markAsRead(id) {
        try {
            await fetch(`/api/notifications/${id}/read`, { method: 'POST' });
            
            const notif = this.notifications.find(n => n.id === id);
            if (notif) {
                notif.isRead = true;
                this.saveToStorage();
                this.updateUI();
            }
        } catch (error) {
            console.error(' Erreur mark as read:', error);
        }
    }

    getIcon(type) {
        const icons = {
            'message': '',
            'payment': '',
            'match': '',
            'project': '',
            'info': 'ℹ'
        };
        return icons[type] || '';
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'À l\'instant';
        if (diff < 3600) return `Il y a ${Math.floor(diff / 60)} min`;
        if (diff < 86400) return `Il y a ${Math.floor(diff / 3600)} h`;
        return `Il y a ${Math.floor(diff / 86400)} j`;
    }

    saveToStorage() {
        localStorage.setItem('notifications', JSON.stringify(this.notifications));
    }

    loadFromStorage() {
        const stored = localStorage.getItem('notifications');
        if (stored) {
            this.notifications = JSON.parse(stored);
            this.updateUI();
        }
    }

    clearHistory() {
        if (confirm('Effacer tout l\'historique des notifications ?')) {
            this.notifications = [];
            this.saveToStorage();
            this.updateUI();
            this.showToast('success', 'Historique effacé', 'Toutes les notifications ont été supprimées');
        }
    }

    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        localStorage.setItem('notificationSound', this.soundEnabled);
        this.showToast('info', 'Sons notifications', this.soundEnabled ? 'Activés ' : 'Désactivés ');
    }

    attachEventListeners() {
        // Toggle panel
        document.getElementById('notification-bell')?.addEventListener('click', (e) => {
            e.stopPropagation();
            const panel = document.getElementById('notification-panel');
            panel.classList.toggle('show');
        });

        // Close panel on outside click
        document.addEventListener('click', (e) => {
            const panel = document.getElementById('notification-panel');
            const bell = document.getElementById('notification-bell');
            if (!panel.contains(e.target) && !bell.contains(e.target)) {
                panel.classList.remove('show');
            }
        });
    }

    // Public API for testing
    test() {
        this.showToast('success', 'Test Success', 'Notification de succès');
        setTimeout(() => this.showToast('info', 'Test Info', 'Notification d\'information'), 500);
        setTimeout(() => this.showToast('warning', 'Test Warning', 'Notification d\'alerte'), 1000);
        setTimeout(() => this.showToast('error', 'Test Error', 'Notification d\'erreur'), 1500);
    }
}

// Initialize
const notificationManager = new AdvancedNotificationManager();

// Expose for console testing
window.notificationManager = notificationManager;

console.log(' Advanced Notifications loaded! Test with: notificationManager.test()');
