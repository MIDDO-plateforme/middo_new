document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("ia-suggestions-input");
    const btn = document.getElementById("ia-suggestions-run");
    const output = document.getElementById("ia-suggestions-output");

    btn.addEventListener("click", async () => {
        const text = input.value.trim();
        if (!text) {
            output.textContent = "Veuillez entrer un texte.";
            return;
        }

        output.textContent = "⏳ Génération des suggestions...";

        try {
            const response = await fetch("/api/ia/suggestions", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ text })
            });

            const data = await response.json();
            output.textContent = data.result || "Aucune réponse IA.";
        } catch (error) {
            output.textContent = "❌ Erreur lors de la génération.";
        }
    });
});
