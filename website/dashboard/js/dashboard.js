// dashboard/js/dashboard.js

let chartPais = null;
let chartPaginas = null;
let chartDias = null;
let chartDispositivos = null;
let chartNavegadores = null;

let mapAccesos = null;
let markersLayer = null;

document.addEventListener('DOMContentLoaded', () => {
    initFechasPorDefecto();
    initMapa();
    cargarDatos(); // primera carga

    document.getElementById('btnFiltrar').addEventListener('click', () => {
        cargarDatos();
    });
});

function initFechasPorDefecto() {
    const hoy = new Date();
    const hace7 = new Date();
    hace7.setDate(hoy.getDate() - 7);

    const formato = d => d.toISOString().slice(0, 10);

    const inputInicio = document.getElementById('fechaInicio');
    const inputFin = document.getElementById('fechaFin');

    if (inputInicio && inputFin) {
        inputInicio.value = formato(hace7);
        inputFin.value = formato(hoy);
    }
}

function cargarDatos() {
    const pagina = document.getElementById('filtroPagina').value;
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    const params = new URLSearchParams();
    if (pagina) params.append('pagina', pagina);
    if (fechaInicio) params.append('fechaInicio', fechaInicio);
    if (fechaFin) params.append('fechaFin', fechaFin);

    fetch('api/filtrar_datos.php?' + params.toString())
        .then(res => res.json())
        .then(data => {
            actualizarTarjetas(data.resumen);
            actualizarGraficoPaises(data.paises);
            actualizarGraficoPaginas(data.paginas);
            actualizarGraficoDias(data.dias);
            actualizarGraficoDispositivos(data.dispositivos);
            actualizarGraficoNavegadores(data.navegadores);
            actualizarMapa(data.geo);
        })
        .catch(err => {
            console.error('Error cargando datos del dashboard', err);
        });
}

function actualizarTarjetas(resumen) {
    document.getElementById('cardTotalVisitas').textContent   = resumen.total_visitas || 0;
    document.getElementById('cardVisitasHoy').textContent     = resumen.visitas_hoy || 0;
    document.getElementById('cardTotalPaises').textContent    = resumen.total_paises || 0;
    document.getElementById('cardTotalPaginas').textContent   = resumen.total_paginas || 0;
}

// ---- Gráfico de Países ----
function actualizarGraficoPaises(paises) {
    const labels = paises.map(p => p.pais);
    const data   = paises.map(p => parseInt(p.total));

    const ctx = document.getElementById('chartPais').getContext('2d');
    if (chartPais) chartPais.destroy();

    chartPais = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Visitas',
                data
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                zoom: {
                    zoom: {
                        wheel: { enabled: true },
                        pinch: { enabled: true },
                        mode: 'x'
                    },
                    pan: {
                        enabled: true,
                        mode: 'x'
                    }
                }
            },
            scales: {
                x: { ticks: { autoSkip: true, maxTicksLimit: 10 } },
                y: { beginAtZero: true }
            }
        }
    });
}

// ---- Gráfico de Páginas ----
function actualizarGraficoPaginas(paginas) {
    const labels = paginas.map(p => mapearNombrePagina(p.pagina));
    const data   = paginas.map(p => parseInt(p.total));

    const ctx = document.getElementById('chartPaginas').getContext('2d');
    if (chartPaginas) chartPaginas.destroy();

    chartPaginas = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Visitas',
                data
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                zoom: {
                    zoom: {
                        wheel: { enabled: true },
                        pinch: { enabled: true },
                        mode: 'y'
                    },
                    pan: {
                        enabled: true,
                        mode: 'y'
                    }
                }
            },
            scales: {
                x: { beginAtZero: true },
                y: { ticks: { autoSkip: false } }
            }
        }
    });
}

// ---- Gráfico de Días ----
function actualizarGraficoDias(dias) {
    const labels = dias.map(d => d.dia);
    const data   = dias.map(d => parseInt(d.total));

    const ctx = document.getElementById('chartDias').getContext('2d');
    if (chartDias) chartDias.destroy();

    chartDias = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Visitas por día',
                data,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                zoom: {
                    zoom: {
                        wheel: { enabled: true },
                        pinch: { enabled: true },
                        mode: 'x'
                    },
                    pan: {
                        enabled: true,
                        mode: 'x'
                    }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// ---- Gráfico de Dispositivos ----
function actualizarGraficoDispositivos(dispositivos) {
    const labels = dispositivos.map(d => d.dispositivo || 'Sin dato');
    const data   = dispositivos.map(d => parseInt(d.total));

    const ctx = document.getElementById('chartDispositivos').getContext('2d');
    if (chartDispositivos) chartDispositivos.destroy();

    chartDispositivos = new Chart(ctx, {
        type: 'pie',
        data: {
            labels,
            datasets: [{
                data
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// ---- Gráfico de Navegadores ----
function actualizarGraficoNavegadores(navegadores) {
    const labels = navegadores.map(n => n.navegador || 'Sin dato');
    const data   = navegadores.map(n => parseInt(n.total));

    const ctx = document.getElementById('chartNavegadores').getContext('2d');
    if (chartNavegadores) chartNavegadores.destroy();

    chartNavegadores = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// ---- Mapa ----
function initMapa() {
    mapAccesos = L.map('mapAccesos').setView([5, -73], 5); // Colombia–Boyacá aprox.

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapAccesos);

    markersLayer = L.layerGroup().addTo(mapAccesos);
}

function actualizarMapa(geo) {
    markersLayer.clearLayers();

    if (!geo || geo.length === 0) {
        return;
    }

    geo.forEach(p => {
        const lat = parseFloat(p.latitud);
        const lon = parseFloat(p.longitud);
        if (!isNaN(lat) && !isNaN(lon)) {
            const marker = L.circleMarker([lat, lon], {
                radius: 6
            }).bindPopup(
                `<strong>${p.ciudad || ''} - ${p.pais || ''}</strong><br>` +
                `${p.total} visita(s)`
            );
            markersLayer.addLayer(marker);
        }
    });

    try {
        const bounds = markersLayer.getBounds();
        if (bounds.isValid()) {
            mapAccesos.fitBounds(bounds.pad(0.3));
        }
    } catch (e) {
        console.warn('No se pudo ajustar el mapa', e);
    }
}

// ---- Mapeo de nombres de página ----
function mapearNombrePagina(pagina) {
    const nombres = {
        "publicaciones.php":       "Página Publicaciones Universidades",
        "indic-genero.php":        "Dimensión de Género",
        "indic-ambiental.php":     "Dimensión Ambiental",
        "indic-economico.php":     "Dimensión Económica",
        "indic-social.php":        "Dimensión Social",
        "indic-tecnologia.php":    "Dimensión CTeI",
        "indic-boletin.php":       "Boletines"
    };

    return nombres[pagina] || pagina;
}

