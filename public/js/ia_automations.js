document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("automationModal");
    const openBtn = document.getElementById("openAutomationModal");
    const closeBtn = document.getElementById("closeAutomationModal");

    openBtn.addEventListener("click", () => {
        modal.style.display = "flex";
    });

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });
});
