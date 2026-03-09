(async function () {
  const input = document.getElementById('globalSearch');
  const button = document.getElementById('searchButton');
  const results = document.getElementById('searchResults');

  async function runSearch() {
    const q = (input.value || '').trim();
    if (!q) {
      results.innerHTML = '';
      return;
    }

    try {
      const response = await fetch('api/search.php?q=' + encodeURIComponent(q));
      const data = await response.json();
      const items = data.items || [];
      results.innerHTML = items.length
        ? items.map((item) => `<div class="search-item"><a href="${item.url}"><strong>${item.title}</strong></a><div>${item.context}</div></div>`).join('')
        : '<div class="search-item">Sin resultados.</div>';
    } catch (e) {
      results.innerHTML = '<div class="search-item">No fue posible consultar el buscador.</div>';
    }
  }

  button.addEventListener('click', runSearch);
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      runSearch();
    }
  });
})();
