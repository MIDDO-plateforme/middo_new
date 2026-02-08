/**
 * MIDDO - Animation Jauge Sentiment
 * SESSION 18 - BLOC 4
 */

console.log('🎨 MIDDO: Module Animation Jauge Sentiment chargé');

class SentimentGaugeAnimation {
    constructor() {
        this.gauges = [];
        this.init();
    }
    
    init() {
        console.log('🎯 Initialisation des jauges de sentiment...');
        this.observeDOM();
        this.animateExistingGauges();
    }
    
    animateExistingGauges() {
        const gaugeContainers = document.querySelectorAll('.sentiment-gauge-container');
        
        if (gaugeContainers.length === 0) {
            console.log('ℹ️ Aucune jauge de sentiment trouvée pour le moment');
            return;
        }
        
        console.log(`✅ ${gaugeContainers.length} jauge(s) de sentiment trouvée(s)`);
        
        gaugeContainers.forEach((container, index) => {
            this.animateGauge(container, index);
        });
    }
    
    animateGauge(container, index = 0) {
        const bar = container.querySelector('.sentiment-gauge-bar');
        const scoreText = container.querySelector('.sentiment-score-text');
        
        if (!bar) {
            console.warn('⚠️ Barre de jauge non trouvée');
            return;
        }
        
        const score = parseInt(container.dataset.score || bar.dataset.score || 0);
        
        console.log(`🎯 Animation jauge ${index + 1}: Score = ${score}%`);
        
        bar.style.setProperty('--target-width', `${score}%`);
        
        const category = this.getSentimentCategory(score);
        
        bar.classList.add(category);
        bar.classList.add('animating');
        
        const label = container.parentElement.querySelector('.sentiment-label');
        if (label) {
            label.classList.add(category);
        }
        
        if (scoreText) {
            this.animateCounter(scoreText, 0, score, 2000);
        }
        
        this.gauges.push({ container, bar, score, category });
        
        console.log(`✨ Animation jauge ${index + 1} démarrée (${category})`);
    }
    
    animateCounter(element, start, end, duration) {
        const startTime = performance.now();
        const range = end - start;
        
        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const eased = this.easeOutBounce(progress);
            const value = Math.floor(start + (range * eased));
            
            element.textContent = `${value}%`;
            
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                element.textContent = `${end}%`;
            }
        };
        
        requestAnimationFrame(step);
    }
    
    easeOutBounce(t) {
        const n1 = 7.5625;
        const d1 = 2.75;
        
        if (t < 1 / d1) {
            return n1 * t * t;
        } else if (t < 2 / d1) {
            return n1 * (t -= 1.5 / d1) * t + 0.75;
        } else if (t < 2.5 / d1) {
            return n1 * (t -= 2.25 / d1) * t + 0.9375;
        } else {
            return n1 * (t -= 2.625 / d1) * t + 0.984375;
        }
    }
    
    getSentimentCategory(score) {
        if (score <= 30) return 'negative';
        if (score <= 60) return 'neutral';
        return 'positive';
    }
    
    observeDOM() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) {
                            if (node.classList && node.classList.contains('sentiment-gauge-container')) {
                                console.log('🆕 Nouvelle jauge détectée');
                                this.animateGauge(node, this.gauges.length);
                            }
                            
                            const gauges = node.querySelectorAll('.sentiment-gauge-container');
                            if (gauges.length > 0) {
                                console.log(`🆕 ${gauges.length} nouvelle(s) jauge(s) détectée(s)`);
                                gauges.forEach((gauge) => {
                                    this.animateGauge(gauge, this.gauges.length);
                                });
                            }
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        console.log('👀 Observateur DOM activé pour les jauges');
    }
    
    resetAndAnimate(container) {
        const bar = container.querySelector('.sentiment-gauge-bar');
        if (!bar) return;
        
        bar.classList.remove('animating');
        bar.style.width = '0%';
        
        void bar.offsetWidth;
        
        setTimeout(() => {
            this.animateGauge(container, this.gauges.length);
        }, 100);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.sentimentGaugeAnimation = new SentimentGaugeAnimation();
        console.log('✅ Animation Jauge Sentiment prête');
    });
} else {
    window.sentimentGaugeAnimation = new SentimentGaugeAnimation();
    console.log('✅ Animation Jauge Sentiment prête');
}
