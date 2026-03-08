(function () {
  const endpoint = 'api/indicators.php';
  const searchInput = document.getElementById('searchInput');
  const container = document.getElementById('indicatorsByDimension');

  let indicators = [];

  function groupByDimension(items) {
    return items.reduce((acc, item) => {
      if (!acc[item.dimension]) {
        acc[item.dimension] = [];
      }
      acc[item.dimension].push(item);
      return acc;
    }, {});
  }

  function renderSummary(items) {
    const countByDimension = items.reduce((acc, item) => {
      acc[item.dimension] = (acc[item.dimension] || 0) + 1;
      return acc;
    }, {});

    const total = items.length;
    const social = countByDimension['social'] || 0;
    const economica = countByDimension['económica'] || 0;
    const ambiental = countByDimension['ambiental'] || 0;
    const tecnologia = countByDimension['tecnología'] || 0;

    document.getElementById('totalIndicators').textContent = total;
    document.getElementById('totalSocial').textContent = social;
    document.getElementById('totalEconomica').textContent = economica;
    document.getElementById('totalOtros').textContent = ambiental + tecnologia;
  }

  function renderIndicators(items) {
    const grouped = groupByDimension(items);

    const html = Object.keys(grouped)
      .map((dimension) => {
        const cards = grouped[dimension]
          .map((indicator) => `
            <li class="indicator-item">
              <a href="${indicator.url}" class="indicator-link">
                <strong>${indicator.id} · ${indicator.titulo}</strong>
                <span>${indicator.categoria || 'Sin categoría'} / ${indicator.subcategoria || 'Sin subcategoría'}</span>
              </a>
            </li>
          `)
          .join('');

        return `
          <section class="dimension-column" aria-label="Dimensión ${dimension}">
            <h2>${dimension}</h2>
            <ul>${cards}</ul>
          </section>
        `;
      })
      .join('');

    container.innerHTML = html || '<p>No se encontraron indicadores con ese filtro.</p>';
  }

  function filterAndRender() {
    const term = (searchInput.value || '').toLowerCase().trim();
    const filtered = indicators.filter((item) => {
      return (
        item.titulo.toLowerCase().includes(term) ||
        item.categoria.toLowerCase().includes(term) ||
        item.subcategoria.toLowerCase().includes(term) ||
        item.dimension.toLowerCase().includes(term)
      );
    });

    renderSummary(filtered);
    renderIndicators(filtered);
  }

  async function init() {
    try {
      const response = await fetch(endpoint);
      const json = await response.json();
      indicators = json.items || [];
      renderSummary(indicators);
      renderIndicators(indicators);
    } catch (error) {
      container.innerHTML = '<p>No fue posible cargar los indicadores automáticamente.</p>';
      console.error(error);
    }
  }

  searchInput.addEventListener('input', filterAndRender);
  init();
})();
