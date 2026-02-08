// ============================================================
// FICHIER : public/js/mercure.js
// Gestion unifiée des événements Mercure pour MIDDO
// - Alertes temps réel
// - Badge navbar
// - Flux #events dans le cockpit / dashboards
// ============================================================

document.addEventListener("DOMContentLoaded", () => {
    // --------------------------------------------------------
    // 1) Sélecteurs DOM
    // --------------------------------------------------------
    const eventsContainer = document.getElementById("events");
    const alertBadge = document.getElementById("alert-badge");

    // Si aucun conteneur, on ne fait rien (page simple)
    if (!eventsContainer && !alertBadge) {
        return;
    }

    // --------------------------------------------------------
    // 2) Configuration Mercure
    // --------------------------------------------------------
    const url = new URL("http://localhost:3000/.well-known/mercure");
    url.searchParams.append("topic", "middo/alerts");

    const eventSource = new EventSource(url);

    // --------------------------------------------------------
    // 3) Gestion des messages
    // --------------------------------------------------------
    eventSource.onmessage = (event) => {
        try {
            const data = JSON.parse(event.data);

            // data attendu :
            // {
            //   type: "ALERTE_TRANSPORT",
            //   message: "Retard sur le segment X",
            //   severity: "critical" | "warning" | "info",
            //   createdAt: "2026-02-05T09:30:00+00:00"
            // }

            // 3.1) Mise à jour du badge
            if (alertBadge) {
                const current = parseInt(alertBadge.textContent || "0", 10) || 0;
                alertBadge.textContent = current + 1;
            }

            // 3.2) Ajout dans le flux #events
            if (eventsContainer) {
                const wrapper = document.createElement("div");

                let borderColor = "#3498db";
                if (data.severity === "critical") borderColor = "red";
                else if (data.severity === "warning") borderColor = "orange";

                wrapper.style.background = "white";
                wrapper.style.padding = "0.75rem 1rem";
                wrapper.style.borderRadius = "6px";
                wrapper.style.boxShadow = "0 2px 6px rgba(0,0,0,0.06)";
                wrapper.style.borderLeft = `4px solid ${borderColor}`;
                wrapper.style.marginBottom = "0.5rem";

                const createdAt = data.createdAt
                    ? new Date(data.createdAt).toLocaleString()
                    : new Date().toLocaleString();

                wrapper.innerHTML = `
                    <strong>${data.type || "Alerte"}</strong><br>
                    <span style="opacity:0.8;">${data.message || ""}</span><br>
                    <small style="opacity:0.6;">${createdAt}</small>
                `;

                // On ajoute en haut de la liste
                eventsContainer.prepend(wrapper);
            }
        } catch (e) {
            console.error("Erreur Mercure (parsing):", e);
        }
    };

    eventSource.onerror = (error) => {
        console.error("Erreur Mercure (connexion):", error);
    };
});
