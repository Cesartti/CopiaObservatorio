// ===========================================
//   TABS ESTÁTICAS DEL SISTEMA ANTIGUO
// ===========================================
function openTabStatic(button, tabId) {
    // Ocultar todos los tabContent
    const tabs = document.querySelectorAll(".tabContent");
    tabs.forEach(t => t.style.display = "none");

    // Remover "active" de todos los botones
    const buttons = document.querySelectorAll("#tab button");
    buttons.forEach(b => b.classList.remove("active"));

    // Mostrar tab seleccionado
    document.getElementById(tabId).style.display = "block";
    button.classList.add("active");
}

// ===========================================
//   FILTRO DE INDICADORES (si deseas agregarlo)
// ===========================================
function filterIndicatorsAmbiental() {
    const input = document.getElementById("searchInputAmbiental").value.toLowerCase();
    const items = document.querySelectorAll(".icon-list-item");

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(input) ? "" : "none";
    });
}
