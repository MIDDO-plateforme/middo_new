document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("ia-docgen-input");
    const btn = document.getElementById("ia-docgen-run");
    const output = document.getElementById("ia-docgen-output");

    btn.addEventListener("click", async () => {
        const text = input.value.trim();
        if (!text) {
            output.textContent = "Veuillez décrire le document.";
            return;
        }

        output.textContent = "⏳ Génération du document...";

        try {
            const response = await fetch("/api/ia/docgen", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ text })
            });

            const data = await response.json();
            output.textContent = data.result || "Document non généré.";
        } catch (error) {
            output.textContent = "❌ Erreur lors de la génération.";
        }
    });
});
