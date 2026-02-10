document.addEventListener("DOMContentLoaded", () => {
    const panels = document.querySelectorAll(".ia-panel");

    panels.forEach(panel => {
        const input = panel.querySelector(".ia-input");
        const runBtn = panel.querySelector(".ia-run");
        const clearBtn = panel.querySelector(".ia-clear");
        const loader = panel.querySelector(".ia-loader");
        const result = panel.querySelector(".ia-result");
        const output = panel.querySelector(".ia-output");
        const status = panel.querySelector(".ia-status");

        clearBtn.addEventListener("click", () => {
            input.value = "";
            result.style.display = "none";
            output.innerHTML = "";
            status.textContent = "Prêt";
        });

        runBtn.addEventListener("click", async () => {
            const text = input.value.trim();
            if (!text) return;

            loader.style.display = "block";
            result.style.display = "none";
            status.textContent = "Analyse…";

            // Ici tu brancheras ton API IA
            setTimeout(() => {
                loader.style.display = "none";
                result.style.display = "block";
                output.innerHTML = "⚠️ API IA non connectée.<br>Le composant fonctionne.";
                status.textContent = "Terminé";
            }, 1200);
        });
    });
});
