// ============================================
// INTERFACE MATCHING MISSIONS - SESSION 67
// ============================================

class MatchingInterface {
    constructor() {
        this.missions = [];
        this.filteredMissions = [];
        this.currentPage = 1;
        this.missionsPerPage = 6;
        this.filters = {
            search: '',
            skills: [],
            budgetMin: 0,
            budgetMax: 10000,
            location: 'all',
            minScore: 0
        };
        this.sortBy = 'score';
        this.sortOrder = 'desc';
        this.init();
    }

    async init() {
        console.log(' MatchingInterface initialized');
        
        // Fetch missions from API
        await this.fetchMissions();
        
        // Attach event listeners
        this.attachEventListeners();
        
        // Initial render
        this.applyFilters();
    }

    async fetchMissions() {
        try {
            const response = await fetch('/api/matching/find', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    skills: ['Symfony', 'PHP', 'React', 'JavaScript', 'Docker']
                })
            });

            const data = await response.json();
            this.missions = data.map(m => ({
                ...m,
                date: new Date().toISOString()
            }));
            
            console.log(` ${this.missions.length} missions chargées`);
        } catch (error) {
            console.error(' Erreur fetch missions:', error);
            // Fallback: missions démo
            this.missions = this.getDemoMissions();
        }
    }

    getDemoMissions() {
        return [
            {
                id: 1,
                title: "Développeur Full Stack Symfony + React",
                description: "Recherche développeur expérimenté pour créer une plateforme collaborative avec IA intégrée. Stack: Symfony 6, React, PostgreSQL, Redis.",
                budget: 5000,
                location: "Remote France",
                skills: ["Symfony", "React", "PHP", "JavaScript", "Redis"],
                score: 95,
                reasons: ["Excellente correspondance des compétences Symfony et React", "Budget aligné avec votre profil", "Localisation idéale"],
                date: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString()
            },
            {
                id: 2,
                title: "API REST avec Symfony + Doctrine",
                description: "Développement d'APIs REST sécurisées avec JWT, Doctrine ORM, et intégration Redis pour le cache.",
                budget: 3500,
                location: "Paris",
                skills: ["Symfony", "PHP", "Doctrine", "Redis", "JWT"],
                score: 88,
                reasons: ["Compétences Symfony et Doctrine parfaitement alignées", "Expérience Redis requise"],
                date: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000).toISOString()
            },
            {
                id: 3,
                title: "Intégration IA avec Gemini API",
                description: "Intégrer Gemini Pro API dans une application Symfony pour créer un chatbot intelligent avec historique et cache.",
                budget: 4200,
                location: "Remote",
                skills: ["PHP", "Symfony", "API Integration", "AI"],
                score: 82,
                reasons: ["Expérience APIs et intégration IA", "Budget attractif"],
                date: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString()
            },
            {
                id: 4,
                title: "Frontend React avec animations CSS3",
                description: "Créer une interface utilisateur moderne avec React, animations CSS3, et intégration d'APIs REST.",
                budget: 2800,
                location: "Lyon",
                skills: ["React", "JavaScript", "CSS3", "HTML5"],
                score: 75,
                reasons: ["Compétences React confirmées", "Animations CSS3 appréciées"],
                date: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString()
            },
            {
                id: 5,
                title: "Tests PHPUnit + Symfony Test Pack",
                description: "Écrire des tests unitaires et fonctionnels pour une application Symfony avec PHPUnit et Symfony Test Framework.",
                budget: 1500,
                location: "Remote",
                skills: ["PHP", "Symfony", "PHPUnit", "Testing"],
                score: 70,
                reasons: ["Compétences PHP et Symfony solides"],
                date: new Date(Date.now() - 10 * 24 * 60 * 60 * 1000).toISOString()
            },
            {
                id: 6,
                title: "Architecture Docker + Redis + PostgreSQL",
                description: "Mise en place d'une architecture Docker avec Redis cache, PostgreSQL, et Symfony. Configuration docker-compose.yml.",
                budget: 3000,
                location: "Marseille",
                skills: ["Docker", "Redis", "PostgreSQL", "DevOps"],
                score: 65,
                reasons: ["Expérience Docker et Redis"],
                date: new Date(Date.now() - 12 * 24 * 60 * 60 * 1000).toISOString()
            }
        ];
    }

    attachEventListeners() {
        // Search
        document.getElementById('search-input')?.addEventListener('input', (e) => {
            this.filters.search = e.target.value;
            this.applyFilters();
        });

        // Skills checkboxes
        document.querySelectorAll('.skill-checkbox input').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const skill = e.target.value;
                if (e.target.checked) {
                    this.filters.skills.push(skill);
                } else {
                    this.filters.skills = this.filters.skills.filter(s => s !== skill);
                }
                this.applyFilters();
            });
        });

        // Location filter
        document.getElementById('location-filter')?.addEventListener('change', (e) => {
            this.filters.location = e.target.value;
            this.applyFilters();
        });

        // Sort
        document.getElementById('sort-select')?.addEventListener('change', (e) => {
            const [sortBy, sortOrder] = e.target.value.split('-');
            this.sortBy = sortBy;
            this.sortOrder = sortOrder;
            this.applyFilters();
        });

        // Reset filters
        document.getElementById('reset-filters')?.addEventListener('click', () => {
            this.resetFilters();
        });

        // Modal close
        document.getElementById('modal-close')?.addEventListener('click', () => {
            this.closeModal();
        });

        document.getElementById('modal-overlay')?.addEventListener('click', (e) => {
            if (e.target.id === 'modal-overlay') {
                this.closeModal();
            }
        });
    }

    applyFilters() {
        // Start with all missions
        let filtered = [...this.missions];

        // Search filter
        if (this.filters.search) {
            const search = this.filters.search.toLowerCase();
            filtered = filtered.filter(m =>
                m.title.toLowerCase().includes(search) ||
                m.description.toLowerCase().includes(search) ||
                m.skills.some(s => s.toLowerCase().includes(search))
            );
        }

        // Skills filter
        if (this.filters.skills.length > 0) {
            filtered = filtered.filter(m =>
                this.filters.skills.some(skill => m.skills.includes(skill))
            );
        }

        // Location filter
        if (this.filters.location !== 'all') {
            filtered = filtered.filter(m =>
                m.location.toLowerCase().includes(this.filters.location.toLowerCase())
            );
        }

        // Sort
        filtered.sort((a, b) => {
            let comparison = 0;
            if (this.sortBy === 'score') {
                comparison = b.score - a.score;
            } else if (this.sortBy === 'date') {
                comparison = new Date(b.date) - new Date(a.date);
            } else if (this.sortBy === 'budget') {
                comparison = b.budget - a.budget;
            }
            return this.sortOrder === 'desc' ? comparison : -comparison;
        });

        this.filteredMissions = filtered;
        this.currentPage = 1;
        this.render();
    }

    resetFilters() {
        this.filters = {
            search: '',
            skills: [],
            budgetMin: 0,
            budgetMax: 10000,
            location: 'all',
            minScore: 0
        };

        // Reset UI
        document.getElementById('search-input').value = '';
        document.querySelectorAll('.skill-checkbox input').forEach(cb => cb.checked = false);
        document.getElementById('location-filter').value = 'all';

        this.applyFilters();
    }

    render() {
        const grid = document.getElementById('missions-grid');
        const resultCount = document.getElementById('result-count');
        
        // Update result count
        resultCount.textContent = `${this.filteredMissions.length} mission${this.filteredMissions.length > 1 ? 's' : ''} trouvée${this.filteredMissions.length > 1 ? 's' : ''}`;

        // Get missions for current page
        const start = (this.currentPage - 1) * this.missionsPerPage;
        const end = start + this.missionsPerPage;
        const pageMissions = this.filteredMissions.slice(start, end);

        if (pageMissions.length === 0) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <div class="empty-icon"></div>
                    <div class="empty-title">Aucune mission trouvée</div>
                    <div class="empty-text">Essayez de modifier vos filtres</div>
                </div>
            `;
            this.renderPagination();
            return;
        }

        grid.innerHTML = pageMissions.map(mission => `
            <div class="mission-card" onclick="matchingInterface.openModal(${mission.id})">
                <div class="mission-header">
                    <div>
                        <div class="mission-title">${mission.title}</div>
                        <div class="mission-location"> ${mission.location}</div>
                    </div>
                    <div class="mission-budget">${mission.budget}€</div>
                </div>

                <div class="mission-description">${mission.description}</div>

                <div class="matching-score">
                    <div class="score-header">
                        <span class="score-label">Score de matching</span>
                        <span class="score-value ${this.getScoreClass(mission.score)}">${mission.score}%</span>
                    </div>
                    <div class="score-bar">
                        <div class="score-fill ${this.getScoreClass(mission.score)}" style="width: ${mission.score}%"></div>
                    </div>
                </div>

                <div class="mission-skills">
                    ${mission.skills.map(skill => 
                        `<span class="skill-badge ${this.filters.skills.includes(skill) ? 'matched' : ''}">${skill}</span>`
                    ).join('')}
                </div>

                <div class="mission-footer">
                    <div class="mission-date">${this.formatDate(mission.date)}</div>
                    <button class="btn-view" onclick="event.stopPropagation(); matchingInterface.openModal(${mission.id})">
                        Voir détails 
                    </button>
                </div>
            </div>
        `).join('');

        this.renderPagination();
    }

    renderPagination() {
        const totalPages = Math.ceil(this.filteredMissions.length / this.missionsPerPage);
        const pagination = document.getElementById('pagination');

        if (totalPages <= 1) {
            pagination.style.display = 'none';
            return;
        }

        pagination.style.display = 'flex';
        pagination.innerHTML = `
            <button class="pagination-btn" onclick="matchingInterface.prevPage()" ${this.currentPage === 1 ? 'disabled' : ''}>
                 Précédent
            </button>
            <div class="pagination-numbers">
                ${Array.from({length: totalPages}, (_, i) => i + 1).map(page => `
                    <button class="page-number ${page === this.currentPage ? 'active' : ''}"
                            onclick="matchingInterface.goToPage(${page})">
                        ${page}
                    </button>
                `).join('')}
            </div>
            <button class="pagination-btn" onclick="matchingInterface.nextPage()" ${this.currentPage === totalPages ? 'disabled' : ''}>
                Suivant 
            </button>
        `;
    }

    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    nextPage() {
        const totalPages = Math.ceil(this.filteredMissions.length / this.missionsPerPage);
        if (this.currentPage < totalPages) {
            this.currentPage++;
            this.render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    goToPage(page) {
        this.currentPage = page;
        this.render();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    openModal(missionId) {
        const mission = this.missions.find(m => m.id === missionId);
        if (!mission) return;

        const modal = document.getElementById('modal-overlay');
        const modalContent = document.getElementById('modal-content-dynamic');

        modalContent.innerHTML = `
            <div class="modal-header">
                <div class="modal-title">${mission.title}</div>
                <div class="modal-budget">${mission.budget}€</div>
            </div>
            <div class="modal-body">
                <div class="modal-section">
                    <div class="modal-section-title">Description</div>
                    <div class="modal-description">${mission.description}</div>
                </div>

                <div class="modal-section">
                    <div class="modal-section-title">Score de matching: ${mission.score}%</div>
                    <ul class="matching-reasons">
                        ${mission.reasons.map(reason => `<li> ${reason}</li>`).join('')}
                    </ul>
                </div>

                <div class="modal-section">
                    <div class="modal-section-title">Compétences requises</div>
                    <div class="mission-skills">
                        ${mission.skills.map(skill => `<span class="skill-badge matched">${skill}</span>`).join('')}
                    </div>
                </div>

                <div class="modal-section">
                    <div class="modal-section-title">Informations</div>
                    <div> Localisation: ${mission.location}</div>
                    <div> Publié: ${this.formatDate(mission.date)}</div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-apply" onclick="matchingInterface.applyToMission(${mission.id})">
                     Postuler à cette mission
                </button>
                <button class="btn-close" onclick="matchingInterface.closeModal()">
                    Fermer
                </button>
            </div>
        `;

        modal.classList.add('show');
    }

    closeModal() {
        document.getElementById('modal-overlay').classList.remove('show');
    }

    applyToMission(missionId) {
        const mission = this.missions.find(m => m.id === missionId);
        if (window.notificationManager) {
            window.notificationManager.showToast('success', 'Candidature envoyée !', `Votre candidature pour "${mission.title}" a été envoyée avec succès.`);
        } else {
            alert(`Candidature envoyée pour: ${mission.title}`);
        }
        this.closeModal();
    }

    getScoreClass(score) {
        if (score >= 80) return 'high';
        if (score >= 60) return 'medium';
        return 'low';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / (1000 * 60 * 60 * 24));

        if (diff === 0) return 'Aujourd\'hui';
        if (diff === 1) return 'Hier';
        if (diff < 7) return `Il y a ${diff} jours`;
        if (diff < 30) return `Il y a ${Math.floor(diff / 7)} semaines`;
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
    }
}

// Initialize
let matchingInterface;

document.addEventListener('DOMContentLoaded', () => {
    matchingInterface = new MatchingInterface();
    window.matchingInterface = matchingInterface;
    console.log(' Matching Interface loaded!');
});
