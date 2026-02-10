document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("ia-risks-input");
    const btn = document.getElementById("ia-risks-run");
    const output = document.getElementById("ia-risks-output");

    btn.addEventListener("click", async () => {
        const text = input.value.trim();
        if (!text) {
            output.textContent = "Veuillez entrer un texte.";
            return;
        }

        output.textContent = "⏳ Analyse des risques...";

        try {
            const response = await fetch("/api/ia/risks", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ text })
            });

            const data = await response.json();
            output.textContent = data.result || "Aucun risque détecté.";
        } catch (error) {
            output.textContent = "❌ Erreur lors de l'analyse.";
        }
    });
});
