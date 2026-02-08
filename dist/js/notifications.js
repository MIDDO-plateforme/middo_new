class NotificationManager {
  constructor() {
    this.badge = document.getElementById('notification-badge');
    this.panel = document.getElementById('notifications-panel');
    this.list = document.getElementById('notifications-list');
    this.unreadCount = document.getElementById('unread-count');
    this.toastContainer = document.getElementById('toast-container');
    this.pollingInterval = null;
  }

  startPolling() {
    console.log(' NotificationManager - MODE POLLING ACTIVÉ (30s)');
    
    // Charger immédiatement
    this.fetchNotifications();
    
    // Puis polling toutes les 30s
    this.pollingInterval = setInterval(() => {
      this.fetchNotifications();
    }, 30000);
  }

  async fetchNotifications() {
    try {
      const response = await fetch('/api/notifications');
      const data = await response.json();
      
      if (data.success && data.notifications) {
        this.updateNotificationsList(data.notifications);
        this.updateBadge(data.notifications);
        console.log(`📋 ${data.notifications.length} notifications chargées`);
      }
    } catch (error) {
      console.error(' Erreur chargement notifications:', error);
    }
  }

  updateNotificationsList(notifications) {
    if (!this.list) return;
    
    this.list.innerHTML = '';
    
    if (notifications.length === 0) {
      this.list.innerHTML = '<div style="padding: 2rem; text-align: center; color: #999;">Aucune notification</div>';
      return;
    }

    notifications.forEach(notif => {
      this.addNotificationToList(notif);
    });
  }

  addNotificationToList(notification) {
    const item = document.createElement('div');
    item.className = `notification-item ${notification.read ? '' : 'unread'}`;
    item.dataset.id = notification.id;
    item.innerHTML = `
      <span class="notification-icon">${this.getNotificationIcon(notification.type)}</span>
      <div class="notification-content">
        <div class="notification-title">${notification.title}</div>
        <div class="notification-message">${notification.message}</div>
        <div class="notification-date">${this.formatDate(notification.timestamp)}</div>
      </div>
    `;

    item.onclick = () => this.markAsRead(notification.id);
    
    if (this.list) {
      this.list.appendChild(item);
    }
  }

  updateBadge(notifications) {
    const unreadCount = notifications.filter(n => !n.read).length;
    
    if (this.badge) {
      if (unreadCount > 0) {
        this.badge.textContent = unreadCount;
        this.badge.classList.add('show');
      } else {
        this.badge.classList.remove('show');
      }
    }

    if (this.unreadCount) {
      this.unreadCount.textContent = unreadCount;
    }
  }

  async markAsRead(notificationId) {
    try {
      await fetch(`/api/notifications/${notificationId}/read`, {
        method: 'POST',
      });

      const item = this.list?.querySelector(`[data-id="${notificationId}"]`);
      if (item) {
        item.classList.remove('unread');
      }

      this.fetchNotifications(); // Refresh
    } catch (error) {
      console.error(' Erreur marquage lu:', error);
    }
  }

  getNotificationIcon(type) {
    const icons = {
      'message': '',
      'payment': '',
      'project': '',
      'match': '',
      'system': '',
      'test': '',
    };
    return icons[type] || '';
  }

  formatDate(timestamp) {
    const date = new Date(timestamp * 1000);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) return 'À l\'instant';
    if (diff < 3600) return `Il y a ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Il y a ${Math.floor(diff / 3600)}h`;
    return date.toLocaleDateString('fr-FR');
  }

  stop() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
      console.log(' Polling arrêté');
    }
  }
}

// Instance globale
const notificationManager = new NotificationManager();

// Démarrer au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
  notificationManager.startPolling();
});