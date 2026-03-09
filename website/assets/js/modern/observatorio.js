(async function () {
  const slug = window.OBS_SLUG;
  const list = document.getElementById('featuredIndicators');
  const totalKpi = document.getElementById('kpi-total');

  const prefixMap = { economico: '1', social: '2', ambiente: '3', cti: '4', genero: '5' };
  const prefix = prefixMap[slug] || '';

  try {
    const response = await fetch('api/indicators.php');
    const data = await response.json();
    const all = data.items || [];
    const filtered = prefix ? all.filter((item) => String(item.id).startsWith(prefix)) : all;

    totalKpi.textContent = filtered.length;

    const top = filtered.slice(0, 6);
    list.innerHTML = top.length
      ? top.map((item) => `<li class="list-group-item"><a href="${item.url}"><strong>${item.titulo}</strong></a><div>${item.categoria} / ${item.subcategoria}</div></li>`).join('')
      : '<li class="list-group-item">Aún sin indicadores asociados.</li>';
  } catch (error) {
    list.innerHTML = '<li class="list-group-item">No fue posible cargar indicadores.</li>';
  }
})();
