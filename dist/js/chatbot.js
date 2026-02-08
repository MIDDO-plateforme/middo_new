/**
 * MIDDO Chatbot Widget - SESSION 33a
 * Gestion complète du chatbot avec appels API
 */

(function() {
    'use strict';

    // ==================== CONFIGURATION ====================
    const CONFIG = {
        apiUrl: '/api/chatbot',
        maxRetries: 3,
        retryDelay: 1000,
        typingDelay: 1500,
        autoCloseDelay: null,
    };

    // ==================== ÉTAT ====================
    let state = {
        isOpen: false,
        isTyping: false,
        messageCount: 0,
        retryCount: 0,
    };

    // ==================== ÉLÉMENTS DOM ====================
    const elements = {
        widget: null,
        toggleBtn: null,
        closeBtn: null,
        window: null,
        messages: null,
        form: null,
        input: null,
        sendBtn: null,
        typing: null,
        charCount: null,
        badge: null,
        quickReplies: null,
    };

    // ==================== INITIALISATION ====================
    function init() {
        elements.widget = document.getElementById('middo-chatbot-widget');
        elements.toggleBtn = document.getElementById('chatbot-toggle-btn');
        elements.closeBtn = document.getElementById('chatbot-close-btn');
        elements.window = document.getElementById('chatbot-window');
        elements.messages = document.getElementById('chatbot-messages');
        elements.form = document.getElementById('chatbot-form');
        elements.input = document.getElementById('chatbot-input');
        elements.sendBtn = document.getElementById('chatbot-send-btn');
        elements.typing = document.getElementById('chatbot-typing');
        elements.charCount = document.getElementById('chatbot-char-count');
        elements.badge = document.getElementById('chatbot-badge');
        elements.quickReplies = document.getElementById('chatbot-quick-replies');

        if (!elements.widget) {
            console.error('[Chatbot] Widget non trouvé');
            return;
        }

        attachEventListeners();
        autoResizeTextarea();

        console.log('[Chatbot] Initialisé avec succès ✅');
    }

    // ==================== EVENT LISTENERS ====================
    function attachEventListeners() {
        elements.toggleBtn?.addEventListener('click', toggleChatbot);
        elements.closeBtn?.addEventListener('click', closeChatbot);
        elements.form?.addEventListener('submit', handleSubmit);

        elements.input?.addEventListener('input', function() {
            updateCharCount();
            autoResizeTextarea();
        });

        elements.quickReplies?.querySelectorAll('.quick-reply-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const message = this.dataset.reply;
                if (message) {
                    elements.input.value = message;
                    elements.input.focus();
                    handleSubmit(new Event('submit'));
                }
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && state.isOpen) {
                closeChatbot();
            }
        });
    }

    // ==================== TOGGLE CHATBOT ====================
    function toggleChatbot() {
        if (state.isOpen) {
            closeChatbot();
        } else {
            openChatbot();
        }
    }

    function openChatbot() {
        elements.window.style.display = 'flex';
        elements.window.classList.remove('chatbot-closing');
        elements.toggleBtn.setAttribute('aria-expanded', 'true');
        state.isOpen = true;

        setTimeout(() => {
            elements.input?.focus();
        }, 100);

        if (elements.badge) {
            elements.badge.style.display = 'none';
        }

        console.log('[Chatbot] Ouvert');
    }

    function closeChatbot() {
        elements.window.classList.add('chatbot-closing');
        elements.toggleBtn.setAttribute('aria-expanded', 'false');
        
        setTimeout(() => {
            elements.window.style.display = 'none';
            elements.window.classList.remove('chatbot-closing');
        }, 300);

        state.isOpen = false;
        console.log('[Chatbot] Fermé');
    }

    // ==================== GESTION MESSAGES ====================
    function handleSubmit(e) {
        e.preventDefault();

        const message = elements.input.value.trim();
        if (!message || state.isTyping) return;

        addMessage(message, 'user');

        elements.input.value = '';
        updateCharCount();
        autoResizeTextarea();

        sendMessageToAPI(message);
    }

    function addMessage(text, type = 'bot') {
        const messageId = `msg-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        const time = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        
        const messageHTML = `
            <div class="chatbot-message chatbot-message-${type}" data-message-id="${messageId}">
                <div class="chatbot-message-avatar">${type === 'bot' ? '🤖' : '👤'}</div>
                <div class="chatbot-message-content">
                    <div class="chatbot-message-bubble">
                        <p>${escapeHtml(text)}</p>
                    </div>
                    <div class="chatbot-message-time">${time}</div>
                </div>
            </div>
        `;

        elements.messages.insertAdjacentHTML('beforeend', messageHTML);
        scrollToBottom();

        state.messageCount++;
        console.log('[Chatbot] Message ajouté:', type, text);
    }

    // ==================== API CALL ====================
    async function sendMessageToAPI(message) {
        showTyping();

        try {
            const csrfToken = elements.form.querySelector('input[name="_token"]')?.value;

            const response = await fetch(CONFIG.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message: message,
                    _token: csrfToken,
                }),
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            await sleep(CONFIG.typingDelay);

            hideTyping();

            if (data.response) {
                addMessage(data.response, 'bot');
            } else if (data.error) {
                addMessage(`❌ Erreur: ${data.error}`, 'bot');
            } else {
                addMessage('❌ Réponse invalide du serveur.', 'bot');
            }

            state.retryCount = 0;

        } catch (error) {
            console.error('[Chatbot] Erreur API:', error);
            
            hideTyping();

            if (state.retryCount < CONFIG.maxRetries) {
                state.retryCount++;
                addMessage(`⚠️ Erreur de connexion. Nouvelle tentative (${state.retryCount}/${CONFIG.maxRetries})...`, 'bot');
                
                await sleep(CONFIG.retryDelay);
                return sendMessageToAPI(message);
            } else {
                addMessage('❌ Impossible de contacter le serveur. Vérifie ta connexion et réessaye.', 'bot');
                state.retryCount = 0;
            }
        }
    }

    // ==================== TYPING INDICATOR ====================
    function showTyping() {
        state.isTyping = true;
        elements.typing.style.display = 'flex';
        elements.sendBtn.disabled = true;
        scrollToBottom();
    }

    function hideTyping() {
        state.isTyping = false;
        elements.typing.style.display = 'none';
        elements.sendBtn.disabled = false;
    }

    // ==================== UTILITAIRES ====================
    function updateCharCount() {
        const count = elements.input.value.length;
        elements.charCount.textContent = count;

        if (count > 450) {
            elements.charCount.style.color = 'var(--chatbot-danger)';
        } else {
            elements.charCount.style.color = 'var(--chatbot-text-light)';
        }
    }

    function autoResizeTextarea() {
        if (!elements.input) return;
        
        elements.input.style.height = 'auto';
        elements.input.style.height = Math.min(elements.input.scrollHeight, 100) + 'px';
    }

    function scrollToBottom() {
        if (!elements.messages) return;
        
        setTimeout(() => {
            elements.messages.scrollTop = elements.messages.scrollHeight;
        }, 100);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // ==================== DÉMARRAGE ====================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.MIDDOChatbot = {
        open: openChatbot,
        close: closeChatbot,
        toggle: toggleChatbot,
        sendMessage: function(text) {
            elements.input.value = text;
            handleSubmit(new Event('submit'));
        },
    };

})();
