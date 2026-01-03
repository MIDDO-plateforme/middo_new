class MIDDOWebSocketClient {
    constructor(userId, options = {}) {
        this.userId = userId;
        this.mercureUrl = options.mercureUrl || '/api/realtime/notifications/stream';
        this.presenceApiUrl = options.presenceApiUrl || '/api/presence';
        this.heartbeatInterval = options.heartbeatInterval || 30000;
        this.eventSource = null;
        this.heartbeatTimer = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.listeners = {};

        this.connect();
    }

    connect() {
        try {
            this.sendPresenceUpdate('online', 'Connected');
            this.eventSource = new EventSource(`${this.mercureUrl}?userId=${this.userId}`);

            this.eventSource.onopen = () => {
                console.log('🟢 MIDDOWebSocketClient connected');
                this.reconnectAttempts = 0;
                this.startHeartbeat();
                this.trigger('connected', { userId: this.userId });
            };

            this.eventSource.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    this.handleMessage(data);
                } catch (e) {
                    console.error('❌ Error parsing message:', e);
                }
            };

            this.eventSource.onerror = (error) => {
                console.error('🔴 WebSocket error:', error);
                this.eventSource.close();
                this.reconnect();
            };

            this.subscribeToPresence();
        } catch (error) {
            console.error('❌ Connection failed:', error);
            this.reconnect();
        }
    }

    handleMessage(data) {
        const type = data.type || 'unknown';
        console.log(`📩 Message received:`, type, data);

        switch (type) {
            case 'notification':
                this.trigger('notification', data.data);
                break;
            case 'user_connected':
                console.log(`👤 User ${data.user_id} is now online`);
                this.trigger('presence', { userId: data.user_id, status: 'online' });
                break;
            case 'user_disconnected':
                console.log(`👤 User ${data.user_id} is now offline`);
                this.trigger('presence', { userId: data.user_id, status: 'offline' });
                break;
            default:
                this.trigger(type, data);
        }
    }

    subscribeToPresence() {
        setInterval(() => {
            fetch(`${this.presenceApiUrl}/online`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.trigger('online_users', data.data.online_users);
                    }
                })
                .catch(err => console.error('❌ Presence fetch failed:', err));
        }, 10000);
    }

    sendPresenceUpdate(status, message = '') {
        fetch(`${this.presenceApiUrl}/connect`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                user_id: this.userId,
                metadata: { status, message, timestamp: new Date().toISOString() }
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Presence updated:', status);
            }
        })
        .catch(err => console.error('❌ Presence update failed:', err));
    }

    startHeartbeat() {
        this.heartbeatTimer = setInterval(() => {
            fetch(`${this.presenceApiUrl}/heartbeat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: this.userId })
            }).catch(err => console.error('❌ Heartbeat failed:', err));
        }, this.heartbeatInterval);
    }

    reconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            const delay = Math.min(1000 * Math.pow(2, this.reconnectAttempts), 30000);
            console.log(`🔄 Reconnecting in ${delay / 1000}s...`);
            setTimeout(() => this.connect(), delay);
        } else {
            console.error('❌ Max reconnect attempts reached');
            this.trigger('error', { message: 'Max reconnect attempts reached' });
        }
    }

    subscribe(event, callback) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }
        this.listeners[event].push(callback);
    }

    trigger(event, data) {
        if (this.listeners[event]) {
            this.listeners[event].forEach(callback => callback(data));
        }
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
        }
        if (this.heartbeatTimer) {
            clearInterval(this.heartbeatTimer);
        }
        this.sendPresenceUpdate('offline', 'Disconnected');
        console.log('🔴 MIDDOWebSocketClient disconnected');
    }
}

window.MIDDOWebSocketClient = MIDDOWebSocketClient;