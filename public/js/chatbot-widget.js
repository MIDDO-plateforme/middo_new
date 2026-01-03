// ============================================
// WIDGET CHATBOT - SESSION 66
// ============================================

class ChatbotWidget {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.isTyping = false;
        this.quickReplies = [
            " Trouver une mission",
            " Mon wallet",
            " Mon profil",
            " Aide"
        ];
        this.init();
    }

    init() {
        console.log(' ChatbotWidget initialized');
        
        // Load from localStorage
        this.loadHistory();
        
        // Event listeners
        this.attachEventListeners();
        
        // Show welcome if no history
        if (this.messages.length === 0) {
            this.showWelcome();
        }
    }

    attachEventListeners() {
        // Toggle widget
        document.getElementById('chatbot-toggle')?.addEventListener('click', () => {
            this.toggle();
        });

        // Send message
        document.getElementById('chatbot-send')?.addEventListener('click', () => {
            this.sendMessage();
        });

        // Enter to send
        document.getElementById('chatbot-input')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Clear chat
        document.getElementById('chatbot-clear')?.addEventListener('click', () => {
            this.clearChat();
        });

        // Quick replies
        document.querySelectorAll('.quick-reply').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const text = e.target.textContent.trim();
                document.getElementById('chatbot-input').value = text;
                this.sendMessage();
            });
        });
    }

    toggle() {
        this.isOpen = !this.isOpen;
        
        const widget = document.getElementById('chatbot-widget');
        const toggle = document.getElementById('chatbot-toggle');
        const badge = document.getElementById('chatbot-badge');

        widget.classList.toggle('show', this.isOpen);
        toggle.classList.toggle('open', this.isOpen);

        if (this.isOpen) {
            badge.style.display = 'none';
            this.scrollToBottom();
            document.getElementById('chatbot-input')?.focus();
        }
    }

    async sendMessage() {
        const input = document.getElementById('chatbot-input');
        const text = input.value.trim();

        if (!text) return;

        // Add user message
        this.addMessage('user', text);
        input.value = '';

        // Show typing indicator
        this.showTyping();

        try {
            // Call API
            const response = await fetch('/api/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: text,
                    userId: 1,
                    name: 'Baudouin',
                    skills: ['Symfony', 'React', 'PHP', 'AI']
                })
            });

            const data = await response.json();

            // Hide typing
            this.hideTyping();

            // Add bot response
            this.addMessage('bot', data.message || data.response || 'Désolé, je n\'ai pas compris.');

        } catch (error) {
            console.error(' Erreur chatbot:', error);
            this.hideTyping();
            this.addMessage('bot', 'Désolé, une erreur est survenue. Réessayez plus tard.');
        }
    }

    addMessage(type, text) {
        const message = {
            id: Date.now(),
            type,
            text,
            timestamp: new Date().toISOString()
        };

        this.messages.push(message);
        this.saveHistory();
        this.renderMessage(message);
        this.scrollToBottom();
    }

    renderMessage(message) {
        const messagesContainer = document.querySelector('.chatbot-messages');
        
        // Remove welcome if exists
        const welcome = messagesContainer.querySelector('.welcome-message');
        if (welcome) welcome.remove();

        const messageEl = document.createElement('div');
        messageEl.className = `message ${message.type}`;
        messageEl.innerHTML = `
            <div class="message-avatar">
                ${message.type === 'user' ? '👤' : '🤖'}
            </div>
            <div class="message-content">
                <div class="message-bubble">${this.escapeHtml(message.text)}</div>
                <div class="message-time">${this.formatTime(message.timestamp)}</div>
            </div>
        `;

        messagesContainer.appendChild(messageEl);
    }

    showTyping() {
        this.isTyping = true;
        const indicator = document.querySelector('.typing-indicator');
        if (indicator) {
            indicator.classList.add('show');
            this.scrollToBottom();
        }
    }

    hideTyping() {
        this.isTyping = false;
        const indicator = document.querySelector('.typing-indicator');
        if (indicator) {
            indicator.classList.remove('show');
        }
    }

    showWelcome() {
        const messagesContainer = document.querySelector('.chatbot-messages');
        messagesContainer.innerHTML = `
            <div class="welcome-message">
                <div class="bot-icon">🤖</div>
                <h4>Salut Baudouin ! 👋</h4>
                <p>Je suis ton assistant IA MIDDO.<br>Comment puis-je t'aider aujourd'hui ?</p>
            </div>
        `;
    }

    clearChat() {
        if (confirm('Effacer toute la conversation ?')) {
            this.messages = [];
            this.saveHistory();
            this.showWelcome();
            
            // Show toast
            if (window.notificationManager) {
                window.notificationManager.showToast('success', 'Conversation effacée', 'L\'historique a été supprimé');
            }
        }
    }

    scrollToBottom() {
        setTimeout(() => {
            const container = document.querySelector('.chatbot-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'À l\'instant';
        if (diff < 3600) return `Il y a ${Math.floor(diff / 60)} min`;
        if (diff < 86400) return `Il y a ${Math.floor(diff / 3600)} h`;
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    saveHistory() {
        localStorage.setItem('chatbot_history', JSON.stringify(this.messages));
    }

    loadHistory() {
        const stored = localStorage.getItem('chatbot_history');
        if (stored) {
            this.messages = JSON.parse(stored);
            this.renderHistory();
        }
    }

    renderHistory() {
        this.messages.forEach(msg => this.renderMessage(msg));
        this.scrollToBottom();
    }

    // Public API for testing
    test() {
        this.addMessage('bot', 'Ceci est un test de message bot ! ');
        setTimeout(() => {
            this.addMessage('user', 'Ceci est un test de message utilisateur ! ');
        }, 500);
    }
}

// Initialize
let chatbotWidget;

document.addEventListener('DOMContentLoaded', () => {
    chatbotWidget = new ChatbotWidget();
    window.chatbotWidget = chatbotWidget;
    console.log(' Chatbot Widget loaded! Test with: chatbotWidget.test()');
});
