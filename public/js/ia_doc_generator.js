// ============================================================
// MIDDO — IA Génération de documents
// ============================================================

document.addEventListener("DOMContentLoaded", () => {
    const panels = document.querySelectorAll(".ia-doc-generator");

    panels.forEach(panel => {
        const typeSelect = panel.querySelector(".ia-doc-type");
        const input = panel.querySelector(".ia-doc-input");
        const runBtn = panel.querySelector(".ia-doc-run");
        const clearBtn = panel.querySelector(".ia-doc-clear");
        const loader = panel.querySelector(".ia-doc-loader");
        const result = panel.querySelector(".ia-doc-result");
        const output = panel.querySelector(".ia-doc-output");
        const status = panel.querySelector(".ia-doc-generator-status");

        clearBtn.addEventListener("click", () => {
            input.value = "";
            result.style.display = "none";
            output.textContent = "";
            status.textContent = "Prêt";
        });

        runBtn.addEventListener("click", async () => {
            const type = typeSelect.value;
            const context = input.value.trim();
            if (!context) return;

            result.style.display = "none";
            output.textContent = "";
            loader.style.display = "block";
            status.textContent = "Génération…";

            // Placeholder IA
            setTimeout(() => {
                loader.style.display = "none";
                result.style.display = "block";
                status.textContent = "Terminé";

                output.textContent =
`[${type.toUpperCase()} — Brouillon IA]

Contexte :
${context}

Ce texte est un exemple de document généré automatiquement.
Tu pourras ici brancher ton API IA pour produire un contenu réel.`;
            }, 1500);
        });
    });
});
