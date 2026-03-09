document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector("[data-quick-ai-form]");
    const responseBox = document.querySelector("[data-quick-ai-response]");

    if (!form || !responseBox) {
        return;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const input = form.querySelector("input[name='prompt']");
        const prompt = input.value.trim();

        if (!prompt) {
            return;
        }

        responseBox.textContent = "⏳ Réflexion en cours…";

        try {
            const res = await fetch("/api/ia", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ prompt })
            });

            const data = await res.json();

            if (data && data.response) {
                responseBox.textContent = data.response;
            } else {
                responseBox.textContent = "❌ Réponse invalide.";
            }

        } catch (err) {
            responseBox.textContent = "⚠️ Erreur de communication.";
        }
    });

});
