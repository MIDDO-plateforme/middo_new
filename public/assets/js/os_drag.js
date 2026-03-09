document.addEventListener("DOMContentLoaded", () => {

    let currentWindow = null;
    let offsetX = 0;
    let offsetY = 0;

    document.addEventListener("mousedown", (e) => {
        const header = e.target.closest(".os-window-header");
        if (!header) return;

        currentWindow = header.parentElement;

        const rect = currentWindow.getBoundingClientRect();
        offsetX = e.clientX - rect.left;
        offsetY = e.clientY - rect.top;

        currentWindow.style.transition = "none";
    });

    document.addEventListener("mousemove", (e) => {
        if (!currentWindow) return;

        currentWindow.style.left = (e.clientX - offsetX) + "px";
        currentWindow.style.top = (e.clientY - offsetY) + "px";
    });

    document.addEventListener("mouseup", () => {
        if (currentWindow) {
            currentWindow.style.transition = "";
        }
        currentWindow = null;
    });

});
