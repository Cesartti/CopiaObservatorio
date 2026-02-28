<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Semaforización Municipal – Mecanismos Articuladores</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

  <style>
    body { margin:0; padding:0; }
    #map { width:100%; height:85vh; }
    .legend {
      background:#fff;
      padding:6px;
      font-size:.9em;
      box-shadow:0 0 5px rgba(0,0,0,0.3);
      max-height:80vh; overflow-y:auto;
    }
    .legend-item { display:flex; align-items:center; margin-bottom:4px; }
    .legend-item input { margin-right:6px; }
    .legend-color {
      width:12px; height:12px; margin-right:6px; border:1px solid #333;
    }
  </style>
</head>

<body>

<h1 style="text-align:center; margin:.5em 0;">
  Semaforización Municipal – Mecanismos Articuladores
</h1>

<div id="map"></div>
<div id="legend" class="legend" style="position:absolute; top:100px; right:10px; z-index:1000;"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="assets/js/boyaca_low.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

  if (typeof boyacaData === 'undefined') {
    console.error("No se cargó boyaca_low.js");
    return;
  }

  // 1) INICIALIZA MAPA
  const map = L.map('map').setView([5.5, -73], 8);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:'&copy; OpenStreetMap'
  }).addTo(map);


  // 2) COLOR PARA CADA MUNICIPIO (TODO EN VERDE)
  const dataColorById = {
    '15022':'#28a745','15236':'#28a745','15322':'#28a745','15325':'#28a745',
    '15380':'#28a745','15761':'#28a745','15778':'#28a745','15798':'#28a745',
    '15087':'#28a745','15114':'#28a745','15162':'#28a745','15215':'#28a745',
    '15238':'#28a745','15276':'#28a745','15516':'#28a745','15693':'#28a745',
    '15839':'#28a745','15097':'#28a745','15218':'#28a745','15403':'#28a745',
    '15673':'#28a745','15720':'#28a745','15723':'#28a745','15753':'#28a745',
    '15774':'#28a745','15810':'#28a745','15092':'#28a745','15183':'#28a745',
    '15368':'#28a745','15537':'#28a745','15757':'#28a745','15755':'#28a745',
    '15790':'#28a745','15232':'#28a745','15187':'#28a745','15204':'#28a745',
    '15224':'#28a745','15476':'#28a745','15500':'#28a745','15646':'#28a745',
    '15740':'#28a745','15762':'#28a745','15764':'#28a745','15763':'#28a745',
    '15814':'#28a745','15001':'#28a745','15837':'#28a745','15090':'#28a745',
    '15135':'#28a745','15455':'#28a745','15514':'#28a745','15660':'#28a745',
    '15897':'#28a745','15621':'#28a745','15172':'#28a745','15299':'#28a745',
    '15425':'#28a745','15511':'#28a745','15667':'#28a745','15690':'#28a745',
    '15104':'#28a745','15189':'#28a745','15367':'#28a745','15494':'#28a745',
    '15599':'#28a745','15804':'#28a745','15835':'#28a745','15842':'#28a745',
    '15879':'#28a745','15861':'#28a745','15180':'#28a745','15244':'#28a745',
    '15248':'#28a745','15317':'#28a745','15332':'#28a745','15522':'#28a745',
    '15047':'#28a745','15226':'#28a745','15272':'#28a745','15296':'#28a745',
    '15362':'#28a745','15464':'#28a745','15466':'#28a745','15491':'#28a745',
    '15542':'#28a745','15759':'#28a745','15806':'#28a745','15820':'#28a745',
    '15822':'#28a745','15377':'#28a745','15518':'#28a745','15533':'#28a745',
    '15550':'#28a745','15051':'#28a745','15185':'#28a745','15293':'#28a745',
    '15469':'#28a745','15600':'#28a745','15638':'#28a745','15664':'#28a745',
    '15696':'#28a745','15686':'#28a745','15776':'#28a745','15808':'#28a745',
    '15816':'#28a745','15407':'#28a745','15106':'#28a745','15109':'#28a745',
    '15131':'#28a745','15176':'#28a745','15212':'#28a745','15401':'#28a745',
    '15442':'#28a745','15480':'#28a745','15507':'#28a745','15531':'#28a745',
    '15580':'#28a745','15632':'#28a745','15676':'#28a745','15681':'#28a745',
    '15832':'#28a745','15572':'#28a745','15223':'#28a745'
  };


  // 3) LEYENDA (solo verde aparece activa)
  const colorLabels = {
    '#28a745':'Con Mecanismo Articulador Municipal activo.'
  };

  const layersByColor = { '#28a745': [], '#cccccc': [] };


  // 4) DIBUJA MAPA
  L.geoJSON(boyacaData, {
    style: feature => {
      const col = dataColorById[feature.properties.id] || "#cccccc";
      return { fillColor: col, fillOpacity: 0.7, color: '#333', weight: 1 };
    },
    onEachFeature: (feature, layer) => {
      const id = feature.properties.id;
      const nm = feature.properties.name;
      const col = dataColorById[id] || "#cccccc";
      layer.bindTooltip(`${nm} (${id})`, { sticky: true });
      layersByColor[col].push(layer);
      layer.addTo(map);
    }
  });


  // 5) CREA LEYENDA CON CHECKBOX
  const legend = document.getElementById('legend');
  legend.innerHTML = "<strong>Filtrar por estado</strong><br>";

  Object.entries(colorLabels).forEach(([col, desc]) => {
    const group = layersByColor[col];
    if (!group.length) return;

    const div = document.createElement("div");
    div.className = "legend-item";

    const cb = document.createElement("input");
    cb.type = "checkbox";
    cb.checked = true;
    cb.dataset.color = col;
    cb.addEventListener("change", e => {
      group.forEach(g => e.target.checked ? map.addLayer(g) : map.removeLayer(g));
    });

    const sw = document.createElement("span");
    sw.className = "legend-color";
    sw.style.background = col;

    div.appendChild(cb);
    div.appendChild(sw);
    div.appendChild(document.createTextNode(desc));
    legend.appendChild(div);
  });

});
</script>

</body>
</html>
