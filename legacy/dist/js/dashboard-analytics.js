/**
 * SESSION 69 - Dashboard Analytics Manager
 * Chart.js + KPIs animés + Export données
 */

console.log(' Dashboard Analytics chargé !');

class DashboardManager {
    constructor() {
        this.charts = {};
        this.currentPeriod = '7d';
        this.data = this.generateMockData();
        
        this.init();
    }
    
    init() {
        console.log(' Initialisation Dashboard...');
        
        // Charger Chart.js depuis CDN
        if (typeof Chart === 'undefined') {
            this.loadChartJS(() => {
                this.initAfterChartJS();
            });
        } else {
            this.initAfterChartJS();
        }
    }
    
    loadChartJS(callback) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
        script.onload = callback;
        document.head.appendChild(script);
        console.log(' Chart.js chargement...');
    }
    
    initAfterChartJS() {
        console.log(' Chart.js OK !');
        
        this.bindEvents();
        this.animateKPIs();
        this.renderCharts();
        
        console.log(' Dashboard initialisé !');
    }
    
    bindEvents() {
        // Filtres temporels
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.currentPeriod = e.target.dataset.period;
                this.updateDashboard();
            });
        });
        
        // Export CSV
        document.getElementById('export-csv-btn').addEventListener('click', () => {
            this.exportCSV();
        });
        
        // Export JSON
        document.getElementById('export-json-btn').addEventListener('click', () => {
            this.exportJSON();
        });
    }
    
    generateMockData() {
        const periods = {
            '7d': 7,
            '30d': 30,
            '90d': 90,
            '1y': 365
        };
        
        const data = {};
        
        Object.keys(periods).forEach(period => {
            const days = periods[period];
            const revenues = [];
            const labels = [];
            
            for (let i = days - 1; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                labels.push(this.formatDate(date, period));
                revenues.push(Math.floor(Math.random() * 5000) + 1000);
            }
            
            data[period] = {
                revenues: revenues,
                labels: labels,
                totalRevenue: revenues.reduce((a, b) => a + b, 0),
                missions: Math.floor(Math.random() * 50) + 20,
                satisfaction: Math.floor(Math.random() * 20) + 80,
                avgResponse: (Math.random() * 5 + 1).toFixed(1)
            };
        });
        
        return data;
    }
    
    formatDate(date, period) {
        if (period === '7d') {
            const days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
            return days[date.getDay()];
        } else if (period === '30d' || period === '90d') {
            return date.getDate() + '/' + (date.getMonth() + 1);
        } else {
            const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            return months[date.getMonth()];
        }
    }
    
    animateKPIs() {
        const currentData = this.data[this.currentPeriod];
        
        this.animateValue('kpi-revenue', 0, currentData.totalRevenue, 2000, (val) => val.toLocaleString() + ' €');
        this.animateValue('kpi-missions', 0, currentData.missions, 1500, (val) => val);
        this.animateValue('kpi-satisfaction', 0, currentData.satisfaction, 1800, (val) => val + '%');
        this.animateValue('kpi-response', 0, parseFloat(currentData.avgResponse), 1600, (val) => val.toFixed(1) + 'h');
    }
    
    animateValue(elementId, start, end, duration, formatter) {
        const element = document.getElementById(elementId);
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = start + (end - start) * easeOutQuart;
            
            element.textContent = formatter(current);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }
    
    renderCharts() {
        this.renderLineChart();
        this.renderBarChart();
        this.renderDoughnutChart();
        this.renderRadarChart();
    }
    
    renderLineChart() {
        const ctx = document.getElementById('revenue-chart');
        const currentData = this.data[this.currentPeriod];
        
        if (this.charts.line) {
            this.charts.line.destroy();
        }
        
        this.charts.line = new Chart(ctx, {
            type: 'line',
            data: {
                labels: currentData.labels,
                datasets: [{
                    label: 'Revenus (€)',
                    data: currentData.revenues,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => value.toLocaleString() + ' €'
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });
    }
    
    renderBarChart() {
        const ctx = document.getElementById('missions-chart');
        
        if (this.charts.bar) {
            this.charts.bar.destroy();
        }
        
        this.charts.bar = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Développement', 'Design', 'Marketing', 'Rédaction', 'Consulting'],
                datasets: [{
                    label: 'Missions',
                    data: [25, 18, 12, 15, 8],
                    backgroundColor: [
                        '#8b5cf6',
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutBounce'
                }
            }
        });
    }
    
    renderDoughnutChart() {
        const ctx = document.getElementById('skills-chart');
        
        if (this.charts.doughnut) {
            this.charts.doughnut.destroy();
        }
        
        this.charts.doughnut = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Symfony', 'React', 'PHP', 'JavaScript', 'CSS'],
                datasets: [{
                    data: [30, 25, 20, 15, 10],
                    backgroundColor: [
                        '#8b5cf6',
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });
    }
    
    renderRadarChart() {
        const ctx = document.getElementById('performance-chart');
        
        if (this.charts.radar) {
            this.charts.radar.destroy();
        }
        
        this.charts.radar = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Qualité', 'Rapidité', 'Communication', 'Créativité', 'Fiabilité'],
                datasets: [{
                    label: 'Performance',
                    data: [90, 85, 88, 92, 87],
                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                    borderColor: '#8b5cf6',
                    borderWidth: 2,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });
    }
    
    updateDashboard() {
        console.log(' Mise à jour dashboard:', this.currentPeriod);
        this.animateKPIs();
        this.renderLineChart();
    }
    
    exportCSV() {
        const currentData = this.data[this.currentPeriod];
        let csv = 'Date,Revenus\n';
        
        currentData.labels.forEach((label, i) => {
            csv += label + ',' + currentData.revenues[i] + '\n';
        });
        
        this.downloadFile(csv, 'dashboard-data.csv', 'text/csv');
        alert('Export CSV réussi !');
    }
    
    exportJSON() {
        const json = JSON.stringify(this.data[this.currentPeriod], null, 2);
        this.downloadFile(json, 'dashboard-data.json', 'application/json');
        alert('Export JSON réussi !');
    }
    
    downloadFile(content, filename, mimeType) {
        const blob = new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.click();
        URL.revokeObjectURL(url);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    console.log(' DOM ready !');
    window.dashboardManager = new DashboardManager();
});
