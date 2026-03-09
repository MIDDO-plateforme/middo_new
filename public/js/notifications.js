// MIDDO – Notifications IA

const notifBtn = document.querySelector('.navbar-btn.notifications');
const notifPanel = document.querySelector('.notification-panel');

// Toggle du panneau
notifBtn.addEventListener('click', () => {
    notifPanel.classList.toggle('active');
});

// Fermer en cliquant ailleurs
document.addEventListener('click', (e) => {
    if (!notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
        notifPanel.classList.remove('active');
    }
});
