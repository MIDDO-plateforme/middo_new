/**
 * CLIENT JAVASCRIPT POUR NOTIFICATIONS TEMPS RÉEL
 * SESSION 39 - MIDDO Platform
 * 
 * Utilisation:
 * <script src="realtime-client.js"></script>
 * <script>
 *   const realtime = new MIDDORealtimeClient(1); // userId = 1
 *   realtime.connect();
 * </script>
 */

class MIDDORealtimeClient {
    constructor(userId, options = {}) {
        this.userId = userId;
        this.baseUrl = options.baseUrl || '';
        this.useSSE = options.useSSE !== false;
        this.pollInterval = options.pollInterval || 3000; // 3 secondes
        this.eventSource = null;
        this.pollTimer = null;
        this.lastNotificationId = 0;
        this.onNotificationCallbacks = [];
        this.onConnectCallbacks = [];
        this.onDisconnectCallbacks = [];
    }

    /**
     * Se connecter au stream de notifications
     */
    connect() {
        if (this.useSSE && typeof EventSource !== 'undefined') {
            this.connectSSE();
        } else {
            this.connectPolling();
        }
    }

    /**
     * Connexion via Server-Sent Events
     */
    connectSSE() {
        const url = `${this.baseUrl}/api/realtime/notifications/stream?userId=${this.userId}`;
        
        this.eventSource = new EventSource(url);

        this.eventSource.onopen = () => {
            console.log('[MIDDO Realtime] SSE connecté');
            this.triggerCallbacks(this.onConnectCallbacks, { method: 'SSE' });
        };

        this.eventSource.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                
                if (data.type === 'connected') {
                    console.log('[MIDDO Realtime] Stream actif');
                } else if (data.type === 'disconnected') {
                    console.log('[MIDDO Realtime] Stream fermé, reconnexion...');
                    this.reconnectSSE();
                } else {
                    // Notification réelle
                    this.handleNotification(data);
                }
            } catch (error) {
                console.error('[MIDDO Realtime] Erreur parsing:', error);
            }
        };

        this.eventSource.onerror = (error) => {
            console.error('[MIDDO Realtime] Erreur SSE:', error);
            this.eventSource.close();
            this.triggerCallbacks(this.onDisconnectCallbacks, { error });
            
            // Fallback vers polling
            setTimeout(() => {
                console.log('[MIDDO Realtime] Fallback vers polling...');
                this.connectPolling();
            }, 2000);
        };
    }

    /**
     * Reconnexion SSE après timeout
     */
    reconnectSSE() {
        if (this.eventSource) {
            this.eventSource.close();
        }
        setTimeout(() => {
            console.log('[MIDDO Realtime] Reconnexion SSE...');
            this.connectSSE();
        }, 1000);
    }

    /**
     * Connexion via Polling (fallback)
     */
    connectPolling() {
        console.log('[MIDDO Realtime] Mode Polling activé');
        this.triggerCallbacks(this.onConnectCallbacks, { method: 'Polling' });

        this.pollTimer = setInterval(() => {
            this.pollNotifications();
        }, this.pollInterval);
    }

    /**
     * Récupérer les nouvelles notifications (polling)
     */
    async pollNotifications() {
        try {
            const url = `${this.baseUrl}/api/realtime/notifications/poll?userId=${this.userId}&lastId=${this.lastNotificationId}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.success && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    this.handleNotification(notification);
                });
            }
        } catch (error) {
            console.error('[MIDDO Realtime] Erreur polling:', error);
        }
    }

    /**
     * Gérer une nouvelle notification
     */
    handleNotification(notification) {
        console.log('[MIDDO Realtime] Nouvelle notification:', notification);
        
        this.lastNotificationId = Math.max(this.lastNotificationId, notification.id);
        
        // Déclencher les callbacks
        this.triggerCallbacks(this.onNotificationCallbacks, notification);
        
        // Afficher un toast par défaut
        this.showToast(notification);
    }

    /**
     * Afficher un toast de notification
     */
    showToast(notification) {
        // Créer le toast si le conteneur n'existe pas
        let container = document.getElementById('middo-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'middo-toast-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            `;
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `middo-toast middo-toast-${notification.type}`;
        toast.style.cssText = `
            background: ${this.getToastColor(notification.type)};
            color: white;
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
            min-width: 300px;
            max-width: 400px;
        `;

        toast.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 5px;">${notification.title}</div>
            <div style="font-size: 14px;">${notification.message}</div>
        `;

        container.appendChild(toast);

        // Supprimer après 5 secondes
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    /**
     * Couleur du toast selon le type
     */
    getToastColor(type) {
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        return colors[type] || colors.info;
    }

    /**
     * Ajouter un callback pour les nouvelles notifications
     */
    onNotification(callback) {
        this.onNotificationCallbacks.push(callback);
    }

    /**
     * Ajouter un callback pour la connexion
     */
    onConnect(callback) {
        this.onConnectCallbacks.push(callback);
    }

    /**
     * Ajouter un callback pour la déconnexion
     */
    onDisconnect(callback) {
        this.onDisconnectCallbacks.push(callback);
    }

    /**
     * Déclencher les callbacks
     */
    triggerCallbacks(callbacks, data) {
        callbacks.forEach(callback => {
            try {
                callback(data);
            } catch (error) {
                console.error('[MIDDO Realtime] Erreur callback:', error);
            }
        });
    }

    /**
     * Se déconnecter
     */
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
        console.log('[MIDDO Realtime] Déconnecté');
    }

    /**
     * Créer une notification de test
     */
    async pushTestNotification() {
        try {
            const url = `${this.baseUrl}/api/realtime/notifications/push-test?userId=${this.userId}`;
            const response = await fetch(url, { method: 'POST' });
            const data = await response.json();
            console.log('[MIDDO Realtime] Notification test créée:', data);
            return data;
        } catch (error) {
            console.error('[MIDDO Realtime] Erreur push test:', error);
            return { success: false, error: error.message };
        }
    }
}

// Ajouter les animations CSS
const style = document.createElement('style');
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
