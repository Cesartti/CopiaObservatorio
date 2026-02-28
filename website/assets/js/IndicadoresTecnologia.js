// =============================
// TABS
// =============================
document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll(".tecno-tab");
    const panes = document.querySelectorAll(".tecno-pane");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");

            const target = tab.getAttribute("data-bs-target");
            panes.forEach(p => p.style.display = "none");
            document.querySelector(target).style.display = "block";
        });
    });
});

// =============================
// FILTRO DE INDICADORES
// =============================
function filterIndicators() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const items = document.querySelectorAll(".icon-list-items li");

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(input) ? "" : "none";
    });
}
