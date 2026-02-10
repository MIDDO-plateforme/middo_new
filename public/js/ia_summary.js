// ============================================================
// MIDDO — IA Résumé automatique
// ============================================================

document.addEventListener("DOMContentLoaded", () => {
    const panels = document.querySelectorAll(".ia-summary");

    panels.forEach(panel => {
        const runBtn = panel.querySelector(".ia-summary-run");
        const loader = panel.querySelector(".ia-summary-loader");
        const result = panel.querySelector(".ia-summary-result");
        const output = panel.querySelector(".ia-summary-output");
        const status = panel.querySelector(".ia-summary-status");

        runBtn.addEventListener("click", async () => {
            result.style.display = "none";
            output.innerHTML = "";
            loader.style.display = "block";
            status.textContent = "Analyse…";

            // Placeholder IA
            setTimeout(() => {
                loader.style.display = "none";
                result.style.display = "block";
                status.textContent = "Terminé";

                output.innerHTML = `
                    Ce dossier peut être résumé en quelques points clés :
                    <ul>
                        <li>Contexte général clarifié.</li>
                        <li>Enjeux principaux identifiés.</li>
                        <li>Risques et dépendances à surveiller.</li>
                        <li>Prochaines étapes recommandées.</li>
                    </ul>
                `;
            }, 1200);
        });
    });
});
