// ai-assistant.js - MIDDO AI Assistant avec Notifications Toast Premium
// SESSION 18 - Intégration système de notifications - CORRIGÉ ROUTES API

class MIDDOAIAssistant {
    constructor() {
        this.apiBaseUrl = '/api';
        this.chatMessages = [];
        this.isProcessing = false;
        this.init();
    }

    init() {
        console.log('🤖 MIDDO AI Assistant initialized');
        this.setupEventListeners();
        this.checkToastSystem();
    }

    // Vérification du système toast
    checkToastSystem() {
        if (typeof window.toast !== 'undefined') {
            console.log('✅ Toast system loaded');
        } else {
            console.warn('⚠️ Toast system not available');
        }
    }

    setupEventListeners() {
        // Boutons d'ouverture des modals - SÉLECTEURS CORRIGÉS
        const btnsChatbot = document.querySelectorAll('[data-ai-chat]');
        const btnImprove = document.querySelector('[data-ai-improve]');
        const btnMatch = document.querySelector('[data-ai-match]');
        const btnSentiment = document.querySelector('[data-ai-sentiment]');

        // Assistant IA (plusieurs boutons possibles)
        btnsChatbot.forEach(btn => {
            btn.addEventListener('click', () => this.openChatbot());
        });

        // Améliorer
        if (btnImprove) {
            btnImprove.addEventListener('click', () => {
                const projectId = btnImprove.dataset.aiImprove;
                if (projectId) {
                    this.suggestImprovements(projectId);
                }
            });
        }

        // Matching
        if (btnMatch) {
            btnMatch.addEventListener('click', () => {
                const projectId = btnMatch.dataset.aiMatch;
                if (projectId) {
                    this.matchUsers(projectId);
                }
            });
        }

        // Sentiment
        if (btnSentiment) {
            btnSentiment.addEventListener('click', () => {
                const title = btnSentiment.dataset.projectTitle;
                const description = btnSentiment.dataset.projectDescription;
                this.analyzeSentiment(title, description);
            });
        }

        // Bouton envoi message chatbot
        const btnSendMessage = document.getElementById('chatbot-send');
        if (btnSendMessage) {
            btnSendMessage.addEventListener('click', () => this.sendChatMessage());
        }

        // Entrée clavier dans le chatbot
        const chatInput = document.getElementById('chatbot-input');
        if (chatInput) {
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.sendChatMessage();
                }
            });
        }

        // Fermeture des modals
        const closeChatbot = document.getElementById('close-chatbot');
        if (closeChatbot) {
            closeChatbot.addEventListener('click', () => this.closeModal('chatbot-modal'));
        }

        const closeSentiment = document.getElementById('close-sentiment');
        if (closeSentiment) {
            closeSentiment.addEventListener('click', () => this.closeModal('sentiment-modal'));
        }
    }

    // ========================================
    // GESTION DES MODALS
    // ========================================

    openChatbot() {
        const modal = document.getElementById('chatbot-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Toast notification
            if (window.toast) {
                toast.ai('💬 Chatbot MIDDO activé');
            }
            
            // Focus sur l'input
            setTimeout(() => {
                const input = document.getElementById('chatbot-input');
                if (input) input.focus();
            }, 300);
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // ========================================
    // FONCTION 1 : CHATBOT
    // ========================================

    async sendChatMessage() {
        if (this.isProcessing) return;

        const input = document.getElementById('chatbot-input');
        const messagesContainer = document.getElementById('chatbot-messages');

        if (!input || !messagesContainer) {
            console.error('❌ Éléments manquants pour le chatbot');
            if (window.toast) {
                toast.error('Erreur : Éléments du chatbot introuvables');
            }
            return;
        }

        const userMessage = input.value.trim();
        if (!userMessage) {
            if (window.toast) {
                toast.warning('Veuillez saisir un message');
            }
            return;
        }

        this.isProcessing = true;

        // Afficher le message utilisateur
        this.addMessageToChat(userMessage, 'user', messagesContainer);
        input.value = '';

        // Toast notification - envoi
        if (window.toast) {
            toast.ai('📤 Envoi du message...');
        }

        // Afficher le loader
        const loaderId = this.showChatLoader(messagesContainer);

        try {
            // ✅ ROUTE CORRIGÉE : /api/ai/chat
            const response = await fetch(`${this.apiBaseUrl}/ai/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: userMessage,
                    history: this.chatMessages
                })
            });

            const data = await response.json();

            // Masquer le loader
            this.hideChatLoader(loaderId);

            if (data.success && data.response) {
                // Afficher la réponse IA
                this.addMessageToChat(data.response, 'assistant', messagesContainer);
                
                // Toast notification - succès
                if (window.toast) {
                    toast.success('✅ Réponse reçue');
                }
            } else {
                throw new Error(data.error || 'Erreur lors de la génération de la réponse');
            }

        } catch (error) {
            console.error('❌ Erreur chatbot:', error);
            this.hideChatLoader(loaderId);
            this.addMessageToChat('Désolé, une erreur est survenue. Veuillez réessayer.', 'error', messagesContainer);
            
            // Toast notification - erreur
            if (window.toast) {
                toast.error('❌ Erreur lors de l\'envoi du message');
            }
        } finally {
            this.isProcessing = false;
        }
    }

    addMessageToChat(message, role, container) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-3';

        if (role === 'assistant') {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-robot text-white text-sm"></i>
                </div>
                <div class="bg-white rounded-lg px-4 py-3 shadow-sm" style="max-width:80%;">
                    <p class="text-gray-800">${this.escapeHtml(message)}</p>
                </div>
            `;
        } else if (role === 'user') {
            messageDiv.classList.add('justify-end');
            messageDiv.innerHTML = `
                <div class="rounded-lg px-4 py-3 shadow-sm" style="max-width:80%;background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <p class="text-white">${this.escapeHtml(message)}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-person text-gray-600 text-sm"></i>
                </div>
            `;
        } else if (role === 'error') {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-red-100">
                    <i class="bi bi-exclamation-triangle text-red-600 text-sm"></i>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 shadow-sm" style="max-width:80%;">
                    <p class="text-red-800">${this.escapeHtml(message)}</p>
                </div>
            `;
        }

        container.appendChild(messageDiv);
        container.scrollTop = container.scrollHeight;

        // Sauvegarder dans l'historique (sauf erreurs)
        if (role !== 'error') {
            this.chatMessages.push({ role, content: message });
        }
    }

    showChatLoader(container) {
        const loaderId = 'chat-loader-' + Date.now();
        const loader = document.createElement('div');
        loader.id = loaderId;
        loader.className = 'flex items-start space-x-3';
        loader.innerHTML = `
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="bi bi-robot text-white text-sm"></i>
            </div>
            <div class="bg-white rounded-lg px-4 py-3 shadow-sm">
                <div class="flex space-x-2">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                </div>
            </div>
        `;
        container.appendChild(loader);
        container.scrollTop = container.scrollHeight;
        return loaderId;
    }

    hideChatLoader(loaderId) {
        const loader = document.getElementById(loaderId);
        if (loader) loader.remove();
    }

    // ========================================
    // FONCTION 2 : SUGGESTIONS D'AMÉLIORATION
    // ========================================

    async suggestImprovements(projectId) {
        if (this.isProcessing) return;
        if (!projectId) {
            if (window.toast) {
                toast.error('❌ ID du projet manquant');
            }
            return;
        }

        this.isProcessing = true;

        // Toast notification - début
        if (window.toast) {
            toast.ai('🔍 Analyse du projet en cours...');
        }

        try {
            // ✅ ROUTE CORRIGÉE : /api/ai/suggest-improvements
            const response = await fetch(`${this.apiBaseUrl}/ai/suggest-improvements/${projectId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success && data.suggestions) {
                this.displaySuggestionsModal(data.suggestions);
                
                // Toast notification - succès
                if (window.toast) {
                    toast.success('✅ Suggestions générées avec succès');
                }
            } else {
                throw new Error(data.error || 'Erreur lors de la génération des suggestions');
            }

        } catch (error) {
            console.error('❌ Erreur suggestions:', error);
            
            // Toast notification - erreur
            if (window.toast) {
                toast.error('❌ Erreur lors de la génération des suggestions');
            }
        } finally {
            this.isProcessing = false;
        }
    }

    displaySuggestionsModal(suggestions) {
        // Créer une modal dynamique
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 p-6 max-h-90vh overflow-y-auto">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">💡 Suggestions d'Amélioration</h3>
                    <button class="close-suggestions text-gray-400 hover:text-gray-600 transition">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    ${this.formatSuggestions(suggestions)}
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button class="close-suggestions btn-middo w-full">
                        Compris, merci !
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Fermeture
        modal.querySelectorAll('.close-suggestions').forEach(btn => {
            btn.addEventListener('click', () => modal.remove());
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
        });
    }

    formatSuggestions(suggestions) {
        if (typeof suggestions === 'string') {
            return `<p class="text-gray-700 whitespace-pre-line">${this.escapeHtml(suggestions)}</p>`;
        }

        if (Array.isArray(suggestions)) {
            return suggestions.map((s, i) => `
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2">Suggestion ${i + 1}</h4>
                    <p class="text-gray-700">${this.escapeHtml(s)}</p>
                </div>
            `).join('');
        }

        return '<p class="text-gray-600">Aucune suggestion disponible.</p>';
    }

    // ========================================
    // FONCTION 3 : MATCHING DE PROFILS
    // ========================================

    async matchUsers(projectId) {
        if (this.isProcessing) return;
        if (!projectId) {
            if (window.toast) {
                toast.error('❌ ID du projet manquant');
            }
            return;
        }

        this.isProcessing = true;

        // Toast notification - début
        if (window.toast) {
            toast.ai('🔎 Recherche de profils compatibles...');
        }

        try {
            // ✅ ROUTE CORRIGÉE : /api/ai/match-users
            const response = await fetch(`${this.apiBaseUrl}/ai/match-users/${projectId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success && data.matches) {
                this.displayMatchesModal(data.matches);
                
                // Toast notification - succès avec nombre de résultats
                if (window.toast) {
                    const count = Array.isArray(data.matches) ? data.matches.length : 0;
                    if (count === 0) {
                        toast.warning('⚠️ Aucun profil correspondant trouvé');
                    } else {
                        toast.success(`✅ ${count} profil${count > 1 ? 's' : ''} compatible${count > 1 ? 's' : ''} trouvé${count > 1 ? 's' : ''}`);
                    }
                }
            } else {
                throw new Error(data.error || 'Erreur lors du matching');
            }

        } catch (error) {
            console.error('❌ Erreur matching:', error);
            
            // Toast notification - erreur
            if (window.toast) {
                toast.error('❌ Erreur lors du matching');
            }
        } finally {
            this.isProcessing = false;
        }
    }

    displayMatchesModal(matches) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 p-6 max-h-90vh overflow-y-auto">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">🎯 Profils Compatibles</h3>
                    <button class="close-matches text-gray-400 hover:text-gray-600 transition">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    ${this.formatMatches(matches)}
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button class="close-matches btn-middo w-full">
                        Fermer
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Fermeture
        modal.querySelectorAll('.close-matches').forEach(btn => {
            btn.addEventListener('click', () => modal.remove());
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
        });
    }

    formatMatches(matches) {
        if (!matches || matches.length === 0) {
            return '<p class="text-gray-600 text-center py-8">Aucun profil compatible trouvé pour le moment.</p>';
        }

        if (typeof matches === 'string') {
            return `<p class="text-gray-700 whitespace-pre-line">${this.escapeHtml(matches)}</p>`;
        }

        return matches.map(match => `
            <div class="bg-gray-50 p-4 rounded-lg hover:shadow-md transition">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        ${match.title ? match.title.slice(0, 2).toUpperCase() : 'UN'}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">${this.escapeHtml(match.title || 'Utilisateur')}</h4>
                        <p class="text-sm text-gray-600">${this.escapeHtml(match.value || '')}</p>
                        ${(match.skills ? match.skills.join(', ') : '') ? `<p class="text-sm text-gray-700 mt-2"><strong>Expertise :</strong> ${this.escapeHtml((match.skills ? match.skills.join(', ') : ''))}</p>` : ''}
                        ${match.score ? `<p class="text-sm mt-2"><span class="px-2 py-1 rounded-full" style="background:#c6f6d5;color:#22543d;">Score : ${match.score}%</span></p>` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }

    // ========================================
    // FONCTION 4 : ANALYSE DE SENTIMENT
    // ========================================

    async analyzeSentiment(title, description) {
        if (this.isProcessing) return;
        if (!description) {
            if (window.toast) {
                toast.error('❌ Description du projet manquante');
            }
            return;
        }

        this.isProcessing = true;

        // Toast notification - début
        if (window.toast) {
            toast.ai('📊 Analyse du sentiment en cours...');
        }

        try {
            // ✅ ROUTE CORRIGÉE : /api/ai/analyze-sentiment
            const response = await fetch(`${this.apiBaseUrl}/ai/analyze-sentiment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    projectTitle: title,
                    projectDescription: description
                })
            });

            const data = await response.json();

            if (data.success) {
                this.displaySentimentResult(data);
                
                // Toast notification - succès
                if (window.toast) {
                    toast.success('✅ Analyse de sentiment terminée');
                }
            } else {
                throw new Error(data.error || 'Erreur lors de l\'analyse de sentiment');
            }

        } catch (error) {
            console.error('❌ Erreur analyse sentiment:', error);
            
            // Toast notification - erreur
            if (window.toast) {
                toast.error('❌ Erreur lors de l\'analyse de sentiment');
            }
        } finally {
            this.isProcessing = false;
        }
    }

    displaySentimentResult(data) {
        const sentimentModal = document.getElementById('sentiment-modal');
        if (!sentimentModal) return;

        const resultDiv = document.getElementById('sentiment-result');
        if (!resultDiv) return;

        const score = data.score || 50;
        const label = data.label || 'Neutre';
        const content = data.content || '';
        const recommendations = data.recommendations || '';

        // Déterminer la couleur selon le score
        let color = '#667eea';
        if (score >= 70) color = '#10b981';
        else if (score >= 40) color = '#f59e0b';
        else color = '#ef4444';

        resultDiv.innerHTML = `
            <div class="text-center mb-6">
                <div class="relative w-40 h-40 mx-auto mb-4">
                    <svg class="transform -rotate-90" width="160" height="160">
                        <circle cx="80" cy="80" r="70" stroke="#e5e7eb" stroke-width="12" fill="none" />
                        <circle cx="80" cy="80" r="70" stroke="${color}" stroke-width="12" fill="none"
                                stroke-dasharray="${2 * Math.PI * 70}"
                                stroke-dashoffset="${2 * Math.PI * 70 * (1 - score / 100)}"
                                class="transition-all duration-1000 ease-out" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-4xl font-bold" style="color:${color};">${score}%</p>
                            <p class="text-sm text-gray-600">${label}</p>
                        </div>
                    </div>
                </div>
            </div>

            ${content ? `
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">📝 Analyse</h4>
                <p class="text-gray-700 whitespace-pre-line">${this.escapeHtml(content)}</p>
            </div>
            ` : ''}

            ${recommendations ? `
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-800 mb-2">💡 Recommandations</h4>
                <p class="text-gray-700 whitespace-pre-line">${this.escapeHtml(recommendations)}</p>
            </div>
            ` : ''}
        `;

        // Ouvrir la modal
        sentimentModal.classList.remove('hidden');
        sentimentModal.classList.add('flex');
    }

    // ========================================
    // UTILITAIRES
    // ========================================

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    window.middoAI = new MIDDOAIAssistant();
    console.log('✅ MIDDO AI Assistant ready');
});
