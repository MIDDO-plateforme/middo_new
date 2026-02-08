/**
 * MIDDO - API Integration Layer
 * Connexion Frontend <-> Backend APIs IA
 */

class MIDDOApiClient {
    constructor() {
        this.baseUrl = window.location.origin;
        this.endpoints = {
            chatbot: '/api/ai/assistant/chat',
            matching: '/api/ai/matching/suggest',
            analysis: '/api/ai/project/analyze',
            suggestions: '/api/matching/suggestions'
        };
    }

    async callChatbot(message) {
        try {
            const response = await fetch(this.baseUrl + this.endpoints.chatbot, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });
            return await response.json();
        } catch (error) {
            console.error('Erreur API Chatbot:', error);
            return { error: 'Erreur de connexion' };
        }
    }

    async callMatching(profileData) {
        try {
            const response = await fetch(this.baseUrl + this.endpoints.matching, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(profileData)
            });
            return await response.json();
        } catch (error) {
            console.error('Erreur API Matching:', error);
            return { error: 'Erreur de connexion' };
        }
    }

    async callAnalysis(projectData) {
        try {
            const response = await fetch(this.baseUrl + this.endpoints.analysis, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(projectData)
            });
            return await response.json();
        } catch (error) {
            console.error('Erreur API Analysis:', error);
            return { error: 'Erreur de connexion' };
        }
    }
}

// Instance globale
window.middoApi = new MIDDOApiClient();

console.log(' MIDDO API Client initialisé');
