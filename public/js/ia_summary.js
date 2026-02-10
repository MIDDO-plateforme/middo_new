document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("ia-summary-input");
    const btn = document.getElementById("ia-summary-run");
    const output = document.getElementById("ia-summary-output");

    btn.addEventListener("click", async () => {
        const text = input.value.trim();
        if (!text) {
            output.textContent = "Veuillez entrer un texte.";
            return;
        }

        output.textContent = "⏳ Résumé en cours...";

        try {
            const response = await fetch("/api/ia/summary", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ text })
            });

            const data = await response.json();
            output.textContent = data.result || "Résumé indisponible.";
        } catch (error) {
            output.textContent = "❌ Erreur lors du résumé.";
        }
    });
});
