/* ============================================================
   JS OBSERVATORIO DE GÉNERO – SIN CONFLICTOS
   ============================================================ */

(function () {

    console.log("genero.js cargado correctamente");

    // Scroll suave al cambiar de pestaña (mejora UX)
    const generoTabs = document.querySelectorAll("#generoTabs .nav-link");

    generoTabs.forEach(tab => {
        tab.addEventListener("click", () => {
            setTimeout(() => {
                const panel = document.querySelector(tab.dataset.bsTarget);
                if (panel) {
                    panel.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });
                }
            }, 150);
        });
    });

document.querySelectorAll('.ver-tablero-btn').forEach(btn => {
    btn.addEventListener('click', function () {

        const card = this.closest('.tablero-card');
        const url = card.getAttribute('data-iframe');
        const title = card.getAttribute('data-title');

        const iframe = document.getElementById("iframeViewer");
        const loader = document.getElementById("iframeLoader");
        const modalTitle = document.getElementById("iframeTitle");

        // Mostrar título
        modalTitle.textContent = title;

        // Reset: ocultar iframe, mostrar loader
        iframe.style.display = "none";
        loader.style.display = "flex";

        // Cargar iframe
        iframe.src = url;

        iframe.onload = () => {
            loader.style.display = "none";
            iframe.style.display = "block";
        };

        // Abrir modal
        const modal = new bootstrap.Modal(document.getElementById('iframeModal'));
        modal.show();
    });
});


})();
