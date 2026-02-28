document.addEventListener("DOMContentLoaded", () => {

    const cards = document.querySelectorAll(".tab-card");
    const panels = document.querySelectorAll(".tab-panel");
    const body = document.getElementById("main-body");

    // COLORES PARA CADA PANEL
    const fondos = {
        "panel-tablero": "#FFF8E1",      // Amarillo claro
        "panel-hv": "#FFFFFF",           // Blanco
        "panel-categorias": "#FFF8E1"    // Amarillo claro
    };

    cards.forEach(card => {
        card.addEventListener("click", () => {

            // 1️⃣ Cambiar tarjeta activa
            cards.forEach(c => c.classList.remove("active"));
            card.classList.add("active");

            // 2️⃣ Identificar panel destino
            const target = card.getAttribute("data-target");

            // 3️⃣ Cambiar panel visible
            panels.forEach(panel => {
                panel.classList.remove("active-panel");
            });
            document.getElementById(target).classList.add("active-panel");

            // 4️⃣ Cambiar color de fondo del body
            body.style.background = fondos[target];
        });
    });

});
