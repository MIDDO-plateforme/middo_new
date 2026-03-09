document.addEventListener("DOMContentLoaded", () => {
    console.log("MIDDO OS + IA — UI Loaded");

    const windowsContainer = document.getElementById("windows-container");
    const taskbar = document.getElementById("taskbar");
    const desktop = document.getElementById("desktop");
    let zIndexCounter = 10;

    if (!windowsContainer || !taskbar || !desktop) return;

    /* ===========================
       DOCK
    ============================ */

    const dock = document.createElement("div");
    dock.id = "dock";
    desktop.appendChild(dock);

    const dockApps = [
        { id: "documents", icon: "📁", route: "/documents", title: "Documents" },
        { id: "notifications", icon: "🔔", route: "/notifications", title: "Notifications" },
        { id: "ia_admin", icon: "🧭", route: "/ia/admin", title: "IA Admin" },
        { id: "ia_translate", icon: "🌍", route: "/ia/translate", title: "IA Traduction" }
    ];

    dockApps.forEach(app => {
        const item = document.createElement("div");
        item.className = "dock-item";
        item.dataset.id = app.id;
        item.dataset.route = app.route;
        item.title = app.title;
        item.innerText = app.icon;

        item.addEventListener("click", () => {
            openWindow(app.id, app.route, app.title);
        });

        dock.appendChild(item);
    });

    function updateDockActiveState() {
        document.querySelectorAll(".dock-item").forEach(item => {
            const win = document.querySelector(`.window[data-id="${item.dataset.id}"]`);
            if (win) item.classList.add("active");
            else item.classList.remove("active");
        });
    }

    /* ===========================
       DESKTOP ICONS
    ============================ */

    document.querySelectorAll(".os-icon").forEach(icon => {
        icon.addEventListener("click", () => {
            openWindow(
                icon.dataset.app,
                icon.dataset.route,
                icon.querySelector(".label").innerText
            );
        });
    });

    /* ===========================
       WINDOW MANAGEMENT
    ============================ */

    function bringToFront(win) {
        zIndexCounter += 1;
        win.style.zIndex = zIndexCounter;
        win.classList.add("active");
        setTimeout(() => win.classList.remove("active"), 150);
    }

    function openWindow(id, route, title) {
        const existing = document.querySelector(`.window[data-id="${id}"]`);
        if (existing) {
            restoreWindow(existing);
            bringToFront(existing);
            return;
        }

        const win = document.createElement("div");
        win.className = "window";
        win.dataset.id = id;
        win.dataset.maximized = "false";
        win.dataset.minimized = "false";

        win.style.top = "60px";
        win.style.left = "60px";
        win.style.width = "500px";
        win.style.height = "350px";
        bringToFront(win);

        win.innerHTML = `
            <div class="window-header">
                <span class="window-header-title">${title}</span>
                <div class="window-header-buttons">
                    <button class="window-btn window-btn-minimize"></button>
                    <button class="window-btn window-btn-maximize"></button>
                    <button class="window-btn window-btn-close"></button>
                </div>
            </div>
            <div class="window-body">Chargement...</div>
            <div class="window-resize"></div>
        `;

        windowsContainer.appendChild(win);

        fetch(route)
            .then(r => r.text())
            .then(html => win.querySelector(".window-body").innerHTML = html)
            .catch(() => win.querySelector(".window-body").innerHTML = "Erreur de chargement.");

        const btnMin = win.querySelector(".window-btn-minimize");
        const btnMax = win.querySelector(".window-btn-maximize");
        const btnClose = win.querySelector(".window-btn-close");

        btnMin.addEventListener("click", e => { e.stopPropagation(); minimizeWindow(win); });
        btnMax.addEventListener("click", e => { e.stopPropagation(); toggleMaximizeWindow(win); });
        btnClose.addEventListener("click", e => { e.stopPropagation(); closeWindow(win); });

        const task = document.createElement("div");
        task.className = "taskbar-item";
        task.dataset.id = id;
        task.innerText = title;

        task.addEventListener("click", () => {
            if (win.dataset.minimized === "true") restoreWindow(win);
            bringToFront(win);
        });

        taskbar.appendChild(task);

        win.addEventListener("mousedown", () => bringToFront(win));

        dragWindow(win);
        resizeWindow(win);

        updateDockActiveState();
    }

    function closeWindow(win) {
        win.style.animation = "windowClose 0.2s ease-out forwards";
        const id = win.dataset.id;

        setTimeout(() => {
            win.remove();
            document.querySelector(`.taskbar-item[data-id="${id}"]`)?.remove();
            updateDockActiveState();
        }, 180);
    }

    function minimizeWindow(win) {
        win.dataset.minimized = "true";
        win.style.animation = "windowMinimize 0.2s ease-out forwards";

        setTimeout(() => win.style.display = "none", 180);

        const task = document.querySelector(`.taskbar-item[data-id="${win.dataset.id}"]`);
        if (task) task.classList.add("taskbar-item--minimized");
    }

    function restoreWindow(win) {
        win.dataset.minimized = "false";
        win.style.display = "flex";
        win.style.animation = "windowRestore 0.2s ease-out";

        const task = document.querySelector(`.taskbar-item[data-id="${win.dataset.id}"]`);
        if (task) task.classList.remove("taskbar-item--minimized");
    }

    function toggleMaximizeWindow(win) {
        const isMax = win.dataset.maximized === "true";

        if (!isMax) {
            win.dataset.prevLeft = win.style.left;
            win.dataset.prevTop = win.style.top;
            win.dataset.prevWidth = win.style.width;
            win.dataset.prevHeight = win.style.height;

            const rect = desktop.getBoundingClientRect();

            win.style.left = "0px";
            win.style.top = "0px";
            win.style.width = rect.width + "px";
            win.style.height = rect.height + "px";

            win.dataset.maximized = "true";
        } else {
            win.style.left = win.dataset.prevLeft || "60px";
            win.style.top = win.dataset.prevTop || "60px";
            win.style.width = win.dataset.prevWidth || "500px";
            win.style.height = win.dataset.prevHeight || "350px";

            win.dataset.maximized = "false";
        }

        bringToFront(win);
    }

    /* ===========================
       DRAG + SNAP UNIVERSAL
    ============================ */

    function dragWindow(win) {
        const header = win.querySelector(".window-header");
        let offsetX = 0, offsetY = 0, dragging = false;
        let startLeft, startTop, startWidth, startHeight;

        header.addEventListener("mousedown", e => {
            if (e.target.closest(".window-header-buttons")) return;

            dragging = true;
            bringToFront(win);

            startLeft = win.offsetLeft;
            startTop = win.offsetTop;
            startWidth = win.offsetWidth;
            startHeight = win.offsetHeight;

            offsetX = e.clientX - win.offsetLeft;
            offsetY = e.clientY - win.offsetTop;

            // si maximisée, on la remet en mode normal avant drag
            if (win.dataset.maximized === "true") {
                win.dataset.maximized = "false";
                win.style.width = startWidth + "px";
                win.style.height = startHeight + "px";
            }
        });

        document.addEventListener("mousemove", e => {
            if (!dragging) return;

            const x = e.clientX - offsetX;
            const y = e.clientY - offsetY;

            win.style.left = `${x}px`;
            win.style.top = `${y}px`;
        });

        document.addEventListener("mouseup", e => {
            if (!dragging) return;
            dragging = false;

            const rectDesktop = desktop.getBoundingClientRect();
            const marginSnap = 40;

            const winRect = win.getBoundingClientRect();

            const nearLeft = winRect.left <= rectDesktop.left + marginSnap;
            const nearRight = winRect.right >= rectDesktop.right - marginSnap;
            const nearTop = winRect.top <= rectDesktop.top + marginSnap;
            const nearBottom = winRect.bottom >= rectDesktop.bottom - marginSnap - 40; // au-dessus taskbar

            // Snap haut = maximise
            if (nearTop) {
                toggleMaximizeWindow(win);
                return;
            }

            // Snap gauche
            if (nearLeft) {
                win.dataset.maximized = "false";
                win.style.left = rectDesktop.left + "px";
                win.style.top = rectDesktop.top + "px";
                win.style.width = rectDesktop.width / 2 + "px";
                win.style.height = rectDesktop.height + "px";
                return;
            }

            // Snap droite
            if (nearRight) {
                win.dataset.maximized = "false";
                win.style.left = rectDesktop.left + rectDesktop.width / 2 + "px";
                win.style.top = rectDesktop.top + "px";
                win.style.width = rectDesktop.width / 2 + "px";
                win.style.height = rectDesktop.height + "px";
                return;
            }

            // Snap bas = demi-hauteur centré
            if (nearBottom) {
                win.dataset.maximized = "false";
                win.style.top = rectDesktop.top + rectDesktop.height / 2 + "px";
                win.style.height = rectDesktop.height / 2 + "px";
                return;
            }

            // Sinon : on laisse la fenêtre là où elle est (comportement universel, pas de surprise)
        });
    }

    /* ===========================
       RESIZE
    ============================ */

    function resizeWindow(win) {
        const resizer = win.querySelector(".window-resize");
        let resizing = false;
        let startX, startY, startWidth, startHeight;

        resizer.addEventListener("mousedown", e => {
            e.preventDefault();
            resizing = true;
            bringToFront(win);

            startX = e.clientX;
            startY = e.clientY;
            startWidth = win.offsetWidth;
            startHeight = win.offsetHeight;
        });

        document.addEventListener("mousemove", e => {
            if (!resizing) return;
            if (win.dataset.maximized === "true") return;

            const newWidth = startWidth + (e.clientX - startX);
            const newHeight = startHeight + (e.clientY - startY);

            win.style.width = Math.max(300, newWidth) + "px";
            win.style.height = Math.max(200, newHeight) + "px";
        });

        document.addEventListener("mouseup", () => {
            resizing = false;
        });
    }
});
