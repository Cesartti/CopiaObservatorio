// assets/js/IndicadoresEconomico.js

// =============================
//   BASE DE DATOS DE INDICADORES
//   (RESUMIDA AQUÍ; DEJA TU JSON COMPLETO)
// =============================
const indicatorsData = {
    "1001": {
        "codigo": "1001",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el porcentaje de la población de centros poblados y rural disperso que no sabe leer ni escribir en un idioma.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1002": {
        "codigo": "1002",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas de 15 años o más que viven en hogares donde el nivel educativo alcanzado por sus miembros es considerado bajo.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1003": {
        "codigo": "1003",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el porcentaje de hogares que reportan barreras para acceder a servicios de cuidado de la primera infancia.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1004": {
        "codigo": "1004",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Salud",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la proporción de personas que enfrentan obstáculos para acceder a servicios de salud.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1005": {
        "codigo": "1005",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Vivienda",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide las condiciones de hacinamiento crítico en los hogares.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1006": {
        "codigo": "1006",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Vivienda",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de hogares que no disponen de sistemas adecuados para la eliminación de excretas.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1007": {
        "codigo": "1007",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de población en edad escolar que no asiste a una institución educativa.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1008": {
        "codigo": "1008",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el porcentaje de estudiantes con rezago escolar.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1009": {
        "codigo": "1009",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Servicios públicos",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas u hogares sin acceso a una fuente de agua mejorada.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1010": {
        "codigo": "1010",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Salud",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas que no están afiliadas a un sistema de aseguramiento en salud.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1011": {
        "codigo": "1011",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Empleo",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la proporción de personas desempleadas por más de un año.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1012": {
        "codigo": "1012",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Vivienda",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Evalúa la calidad del material utilizado en paredes exteriores de las viviendas.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1013": {
        "codigo": "1013",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Vivienda",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Evalúa los materiales utilizados en la construcción de los pisos de las viviendas.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1014": {
        "codigo": "1014",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Trabajo infantil",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la prevalencia de trabajo infantil en la región.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1015": {
        "codigo": "1015",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Trabajo informal",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas ocupadas fuera de la formalidad.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1016": {
        "codigo": "1016",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Género",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la pobreza multidimensional según el sexo del jefe del hogar.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1017": {
        "codigo": "1017",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Género",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Evalúa la incidencia de pobreza multidimensional según el sexo de la persona.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1018": {
        "codigo": "1018",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el nivel de pobreza basado en ingresos en la región.",
        "fecha": "2021 - 2023",
        "fuente": "DANE"
    },

    "1019": {
        "codigo": "1019",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria extrema",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el nivel de ingresos mínimos necesarios para alimentación en pobreza extrema.",
        "fecha": "2021 - 2023",
        "fuente": "DANE"
    },

    "1020": {
        "codigo": "1020",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria",
        "etiqueta": "Coeficiente de Gini",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la desigualdad en la distribución del ingreso.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1021": {
        "codigo": "1021",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria",
        "etiqueta": "Género",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Proporción de población en pobreza monetaria desagregada por sexo.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1022": {
        "codigo": "1022",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria extrema",
        "etiqueta": "Género",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas en pobreza extrema monetaria según sexo.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1023": {
        "codigo": "1023",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Ingreso",
        "etiqueta": "Ingreso per cápita",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Promedio del ingreso per cápita de la población.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1024": {
        "codigo": "1024",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Brecha de pobreza monetaria",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el déficit relativo de ingresos respecto a la línea de pobreza.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1025": {
        "codigo": "1025",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Brecha de pobreza monetaria extrema",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Distancia promedio entre el ingreso per cápita y la línea de pobreza extrema.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

        "1026": {
        "codigo": "1026",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Brecha de pobreza monetaria extrema",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Distancia promedio entre el ingreso per cápita y la línea de pobreza extrema.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    }

    // 👉 pega aquí TODO el JSON largo que ya tienes (1003, 1004, ..., 1128)
    // asegurándote de que:
    // - NO haya comas colgando al final del último objeto
    // - NO haya comentarios dentro del objeto
};

// =============================
//   FUNCIONES GLOBALES
//   (NO TOCAN EL MENÚ DEL HEADER)
// =============================

// Filtro de indicadores (buscador en Categorías)
function filterIndicators() {
    const input = document.getElementById("searchInput");
    if (!input) return;

    const value = input.value.toLowerCase();
    const items = document.querySelectorAll(".icon-list-items li, .icon-list-items .icon-list-item");

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(value) ? "" : "none";
    });
}

// Mostrar ficha técnica del indicador seleccionado
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
        <p><strong>Unidad de medida:</strong> ${d.unidad}</p>
        <p><strong>Desagregación:</strong> ${d.desagregacion}</p>
        <p><strong>Ámbito geográfico:</strong> ${d.geografia}</p>
        <p><strong>Periodo de datos:</strong> ${d.fecha}</p>
        <p><strong>Fuente:</strong> ${d.fuente}</p>
    `;
}

// Acordeón de categorías (solo afecta el área económica)
function initAccordion() {
    const buttons = document.querySelectorAll(".accordion-button");
    if (!buttons.length) return;

    buttons.forEach(button => {
        button.addEventListener("click", () => {
            const content = button.nextElementSibling;
            const isOpen = content.style.display === "block";

            // Cierra todos
            document.querySelectorAll(".accordion-content").forEach(c => c.style.display = "none");
            document.querySelectorAll(".accordion-button").forEach(b => b.classList.remove("active"));

            // Abre solo el actual
            if (!isOpen) {
                content.style.display = "block";
                button.classList.add("active");
            }
        });
    });
}

// Modal de caracterización
function initFormCarac() {
    const form = document.getElementById("formCarac");
    const modal = document.getElementById("modalCaracterizacion");

    if (!form || !modal) return;

    form.onsubmit = async (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            await fetch("/website/formulario_usuario.php", {
                method: "POST",
                body: formData,
            });
        } catch (err) {
            console.error("Error enviando formulario de caracterización", err);
        }

        modal.style.display = "none";
    };
}

// =============================
//   INICIALIZACIÓN SEGURA
// =============================
document.addEventListener("DOMContentLoaded", () => {
    // No tocamos .nav-link global, ni dropdowns del header.
    initAccordion();
    initFormCarac();

    // Enlazamos eventos si existen los controles:
    const select = document.getElementById("indicatorSelect");
    if (select) {
        select.addEventListener("change", showIndicatorInfo);
    }

    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keyup", filterIndicators);
    }
});
