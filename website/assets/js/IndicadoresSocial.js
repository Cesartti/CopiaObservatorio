// =============================
//   FILTRO DE INDICADORES
// =============================
function filterIndicators() {
    const input = document.getElementById('searchInput');
    if (!input) return;

    const term = input.value.toLowerCase();
    const items = document.querySelectorAll('.icon-list-items li, .icon-list-items .icon-list-item');

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? '' : 'none';
    });
}

// =============================
//   ACORDEÓN
// =============================
function toggleAccordion(button) {
    const content = button.nextElementSibling;
    const isOpen = content.style.display === "block";

    // Cerrar todos
    document.querySelectorAll('.accordion-content').forEach(c => c.style.display = 'none');
    document.querySelectorAll('.accordion-button').forEach(b => b.classList.remove('active'));

    if (!isOpen) {
        content.style.display = "block";
        button.classList.add("active");
    }
}

// =============================
//   BASE DE DATOS DE INDICADORES
// =============================

// ⚠️ PEGA AQUÍ EL MISMO JSON QUE YA TENÍAS EN indic-social:
// const indicatorsData = { "2001": {...}, "2002": {...}, ..., "2021": {...} };

const indicatorsData = {
    // ... tu JSON completo 2001–2021 ...
};

// =============================
//  MOSTRAR INFO INDICADOR
// =============================
function showIndicatorInfo() {
    const select = document.getElementById("indicatorSelect");
    const infoContainer = document.getElementById("indicatorInfo");
    if (!select || !infoContainer) return;

    const selectedId = select.value;

    if (!selectedId || !indicatorsData[selectedId]) {
        infoContainer.innerHTML = "<p class='text-muted'>Seleccione un indicador para ver la información.</p>";
        return;
    }

    const d = indicatorsData[selectedId];

    infoContainer.innerHTML = `
        <h4><strong>${d.definicion}</strong></h4>
        <p><strong>Código:</strong> ${d.codigo}</p>
        <p><strong>Observatorio:</strong> ${d.observatorio}</p>
        <p><strong>Categoría:</strong> ${d.categoria1} / ${d.categoria2}</p>
        <p><strong>Etiqueta:</strong> ${d.etiqueta}</p>
        <p><strong>Unidad:</strong> ${d.unidad}</p>
        <p><strong>Desagregación:</strong> ${d.desagregacion}</p>
        <p><strong>Geografía:</strong> ${d.geografia}</p>
        <p><strong>Periodo:</strong> ${d.fecha}</p>
        <p><strong>Fuente:</strong> ${d.fuente}</p>
    `;
}
