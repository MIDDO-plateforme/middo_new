document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("templateModal");
    const openBtn = document.getElementById("openTemplateModal");
    const closeBtn = document.getElementById("closeTemplateModal");

    openBtn.addEventListener("click", () => {
        modal.style.display = "flex";
    });

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });
});
