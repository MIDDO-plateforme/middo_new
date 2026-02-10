// ============================================================
// MIDDO — IA Suggestions (Version complète)
// ============================================================

document.addEventListener("DOMContentLoaded", () => {

    const panels = document.querySelectorAll(".ia-suggestions");

    panels.forEach(panel => {
        const runBtn = panel.querySelector(".ia-suggestions-run");
        const loader = panel.querySelector(".ia-suggestions-loader");
        const results = panel.querySelector(".ia-suggestions-results");
        const list = panel.querySelector(".ia-suggestions-list");
        const status = panel.querySelector(".ia-suggestions-status");

        runBtn.addEventListener("click", async () => {

            // Reset
            list.innerHTML = "";
            results.style.display = "none";

            // Loader ON
            loader.style.display = "block";
            status.textContent = "Analyse…";

            // Simulation IA (à remplacer par ton API)
            setTimeout(() => {

                loader.style.display = "none";
                results.style.display = "block";
                status.textContent = "Terminé";

                // Suggestions fictives (placeholder)
                const suggestions = [
                    "Analyser les risques potentiels liés à ce dossier.",
                    "Identifier les documents manquants pour finaliser la procédure.",
                    "Proposer les prochaines étapes pour accélérer le traitement.",
                    "Détecter les incohérences dans les données fournies.",
                    "Générer un résumé automatique pour le client."
                ];

                suggestions.forEach(s => {
                    const li = document.createElement("li");
                    li.textContent = s;
                    list.appendChild(li);
                });

            }, 1200);
        });
    });
});
