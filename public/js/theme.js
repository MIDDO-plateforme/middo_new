// MIDDO – Dark Mode Toggle

const body = document.body;
const toggleBtn = document.querySelector('.navbar-btn.theme-toggle');

// Charger le thème sauvegardé
if (localStorage.getItem('middo-theme') === 'dark') {
    body.classList.add('dark-mode');
}

// Toggle du thème
toggleBtn.addEventListener('click', () => {
    body.classList.toggle('dark-mode');

    if (body.classList.contains('dark-mode')) {
        localStorage.setItem('middo-theme', 'dark');
    } else {
        localStorage.setItem('middo-theme', 'light');
    }
});
