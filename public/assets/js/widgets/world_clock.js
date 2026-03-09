document.addEventListener("DOMContentLoaded", () => {

    function updateClocks() {
        document.querySelectorAll("[data-clock]").forEach(el => {
            const tz = el.getAttribute("data-clock");

            try {
                const now = new Date().toLocaleString("fr-FR", {
                    timeZone: tz,
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit"
                });

                el.textContent = now;
            } catch (e) {
                el.textContent = "--:--:--";
            }
        });
    }

    // Mise à jour immédiate
    updateClocks();

    // Mise à jour toutes les secondes
    setInterval(updateClocks, 1000);
});
