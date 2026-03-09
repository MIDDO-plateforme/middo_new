// ============================================================
// MIDDO — Dark Mode Toggle (Version unifiée et corrigée)
// ============================================================

document.addEventListener("DOMContentLoaded", () => {
    const html = document.documentElement;
    const toggle = document.getElementById("themeToggle");

    if (!toggle) return; // Sécurité si le bouton n'existe pas

    // Charger le thème sauvegardé
    const saved = localStorage.getItem("middo-theme");
    if (saved) {
        html.setAttribute("data-theme", saved);
        toggle.textContent = saved === "dark" ? "☀️ Mode clair" : "🌙 Mode sombre";
    } else {
        // Thème par défaut
        html.setAttribute("data-theme", "light");
        toggle.textContent = "🌙 Mode sombre";
    }

    // Toggle du thème
    toggle.addEventListener("click", () => {
        const current = html.getAttribute("data-theme");
        const next = current === "dark" ? "light" : "dark";

        html.setAttribute("data-theme", next);
        localStorage.setItem("middo-theme", next);

        toggle.textContent = next === "dark" ? "☀️ Mode clair" : "🌙 Mode sombre";
    });
});
