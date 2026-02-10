// ============================================================
// MIDDO — IA Analyse de risques
// ============================================================

document.addEventListener("DOMContentLoaded", () => {
    const panels = document.querySelectorAll(".ia-risks");

    panels.forEach(panel => {
        const runBtn = panel.querySelector(".ia-risks-run");
        const loader = panel.querySelector(".ia-risks-loader");
        const results = panel.querySelector(".ia-risks-results");
        const list = panel.querySelector(".ia-risks-list");
        const status = panel.querySelector(".ia-risks-status");

        runBtn.addEventListener("click", async () => {
            list.innerHTML = "";
            results.style.display = "none";
            loader.style.display = "block";
            status.textContent = "Analyse…";

            // Placeholder IA
            setTimeout(() => {
                loader.style.display = "none";
                results.style.display = "block";
                status.textContent = "Terminé";

                const risks = [
                    { level: "high", text: "Dépendance forte à un seul fournisseur critique." },
                    { level: "medium", text: "Données incomplètes pour la prise de décision finale." },
                    { level: "low", text: "Retards potentiels mais maîtrisables sur certaines étapes." }
                ];

                risks.forEach(r => {
                    const li = document.createElement("li");
                    li.classList.add(r.level);
                    li.textContent = r.text;
                    list.appendChild(li);
                });
            }, 1200);
        });
    });
});
