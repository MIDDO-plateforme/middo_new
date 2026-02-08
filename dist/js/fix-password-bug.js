// Fix pour le bug du mot de passe qui disparaît
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[action*="login"]');
    
    if (loginForm) {
        // Désactive Turbo pour ce formulaire
        loginForm.setAttribute('data-turbo', 'false');
        
        // Empêche la soumission AJAX
        loginForm.addEventListener('submit', function(e) {
            // Laisse la soumission normale se faire
            console.log('Formulaire soumis normalement (sans Turbo)');
        });
        
        // Préserve les valeurs des champs
        const passwordField = loginForm.querySelector('input[type="password"]');
        if (passwordField) {
            passwordField.setAttribute('autocomplete', 'current-password');
            
            // Empêche le vidage du champ
            loginForm.addEventListener('submit', function() {
                sessionStorage.setItem('login_email', loginForm.querySelector('input[name="_username"]').value);
            });
        }
    }
    
    // Restaure l'email après erreur
    const emailField = document.querySelector('input[name="_username"]');
    if (emailField && !emailField.value) {
        const savedEmail = sessionStorage.getItem('login_email');
        if (savedEmail) {
            emailField.value = savedEmail;
        }
    }
});