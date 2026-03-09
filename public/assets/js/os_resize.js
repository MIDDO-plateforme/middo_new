document.addEventListener("DOMContentLoaded", () => {

    let resizing = false;
    let currentWindow = null;
    let startX = 0;
    let startY = 0;
    let startWidth = 0;
    let startHeight = 0;

    document.addEventListener("mousedown", (e) => {
        const handle = e.target.closest(".os-window-resize");
        if (!handle) return;

        currentWindow = handle.parentElement;
        resizing = true;

        const rect = currentWindow.getBoundingClientRect();

        startX = e.clientX;
        startY = e.clientY;
        startWidth = rect.width;
        startHeight = rect.height;

        currentWindow.style.transition = "none";
    });

    document.addEventListener("mousemove", (e) => {
        if (!resizing || !currentWindow) return;

        const newWidth = startWidth + (e.clientX - startX);
        const newHeight = startHeight + (e.clientY - startY);

        currentWindow.style.width = Math.max(300, newWidth) + "px";
        currentWindow.style.height = Math.max(200, newHeight) + "px";
    });

    document.addEventListener("mouseup", () => {
        if (currentWindow) {
            currentWindow.style.transition = "";
        }
        resizing = false;
        currentWindow = null;
    });

});
