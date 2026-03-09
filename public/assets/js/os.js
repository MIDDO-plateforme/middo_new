document.addEventListener("DOMContentLoaded", () => {

    const windowsContainer = document.getElementById("windows-container");
    const taskbar = document.getElementById("taskbar");

    let windowZ = 10;

    // --- OUVERTURE D'UNE APP ---
    document.querySelectorAll(".os-icon").forEach(icon => {
        icon.addEventListener("click", () => {
            const app = icon.getAttribute("data-app");
            const route = icon.getAttribute("data-route");

            openApp(app, route);
        });
    });

    function openApp(appName, route) {

        // Vérifier si la fenêtre existe déjà
        let existing = document.querySelector(`.os-window[data-app="${appName}"]`);
        if (existing) {
            focusWindow(existing);
            return;
        }

        // Créer la fenêtre
        const win = document.createElement("div");
        win.classList.add("os-window");
        win.setAttribute("data-app", appName);
        win.style.zIndex = windowZ++;

        win.innerHTML = `
            <div class="os-window-header">
                <span class="os-window-title">${appName}</span>
                <button class="os-window-close">✖</button>
            </div>
            <div class="os-window-content">Chargement…</div>
            <div class="os-window-resize"></div>
        `;

        windowsContainer.appendChild(win);

        // Charger le contenu
        fetch(route)
            .then(res => res.text())
            .then(html => {
                win.querySelector(".os-window-content").innerHTML = html;
            })
            .catch(() => {
                win.querySelector(".os-window-content").innerHTML = "Erreur de chargement.";
            });

        // Fermer la fenêtre
        win.querySelector(".os-window-close").addEventListener("click", () => {
            win.remove();
            removeFromTaskbar(appName);
        });

        // Focus
        win.addEventListener("mousedown", () => focusWindow(win));

        // Ajouter à la taskbar
        addToTaskbar(appName);
    }

    // --- FOCUS ---
    function focusWindow(win) {
        windowZ++;
        win.style.zIndex = windowZ;
    }

    // --- TASKBAR ---
    function addToTaskbar(appName) {
        if (taskbar.querySelector(`[data-app="${appName}"]`)) {
            return;
        }

        const btn = document.createElement("button");
        btn.classList.add("taskbar-btn");
        btn.setAttribute("data-app", appName);
        btn.textContent = appName;

        btn.addEventListener("click", () => {
            const win = document.querySelector(`.os-window[data-app="${appName}"]`);
            if (win) focusWindow(win);
        });

        taskbar.appendChild(btn);
    }

    function removeFromTaskbar(appName) {
        const btn = taskbar.querySelector(`[data-app="${appName}"]`);
        if (btn) btn.remove();
    }

});
