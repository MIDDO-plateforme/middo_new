document.addEventListener("DOMContentLoaded", () => {

    const tempEl = document.querySelector("[data-weather-temp]");
    const descEl = document.querySelector("[data-weather-desc]");

    if (!tempEl || !descEl) {
        return;
    }

    async function loadWeather() {
        try {
            // Coordonnées de Paris
            const lat = 48.8566;
            const lon = 2.3522;

            const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;

            const response = await fetch(url);
            const data = await response.json();

            if (data.current_weather) {
                const temp = Math.round(data.current_weather.temperature);
                const code = data.current_weather.weathercode;

                tempEl.textContent = temp + "°C";
                descEl.textContent = weatherDescription(code);
            } else {
                tempEl.textContent = "--°C";
                descEl.textContent = "Indisponible";
            }

        } catch (e) {
            tempEl.textContent = "--°C";
            descEl.textContent = "Erreur";
        }
    }

    function weatherDescription(code) {
        const map = {
            0: "Ciel clair",
            1: "Principalement clair",
            2: "Partiellement nuageux",
            3: "Couvert",
            45: "Brouillard",
            48: "Brouillard givrant",
            51: "Bruine légère",
            53: "Bruine",
            55: "Bruine forte",
            61: "Pluie légère",
            63: "Pluie",
            65: "Pluie forte",
            71: "Neige légère",
            73: "Neige",
            75: "Neige forte",
            95: "Orage",
            96: "Orage avec grêle",
            99: "Orage violent"
        };

        return map[code] || "Conditions inconnues";
    }

    // Chargement immédiat
    loadWeather();

    // Mise à jour toutes les 10 minutes
    setInterval(loadWeather, 600000);
});
