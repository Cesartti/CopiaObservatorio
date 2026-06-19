(function () {
  var raw = document.getElementById('obs-tablero-json');
  if (!raw || !raw.textContent) return;

  var charts;
  try {
    charts = JSON.parse(raw.textContent);
  } catch (e) {
    return;
  }
  if (!Array.isArray(charts) || charts.length === 0) return;

  var drawn = [];

  function drawAll() {
    drawn.forEach(function (item) {
      if (item.chart && item.dataTable && item.options) {
        try {
          item.chart.draw(item.dataTable, item.options);
        } catch (e) { /* ignore resize glitches */ }
      }
    });
  }

  google.charts.load('current', { packages: ['corechart', 'geochart', 'table'] });
  google.charts.setOnLoadCallback(function () {
    drawn = [];
    charts.forEach(function (cfg) {
      var el = document.getElementById(cfg.domId);
      if (!el) return;

      if (cfg.error) {
        el.parentNode.classList.add('pbi-tile--error');
        el.textContent = cfg.error;
        return;
      }

      var dataTable;
      try {
        dataTable = google.visualization.arrayToDataTable(cfg.data);
      } catch (e) {
        el.parentNode.classList.add('pbi-tile--error');
        el.textContent = 'No se pudieron leer los datos del gráfico.';
        return;
      }

      var Ctor = google.visualization[cfg.type] || google.visualization.ColumnChart;
      var chart = new Ctor(el);
      var options = cfg.options && typeof cfg.options === 'object' ? cfg.options : {};

      chart.draw(dataTable, options);
      drawn.push({ chart: chart, dataTable: dataTable, options: options });
    });

    var t;
    window.addEventListener('resize', function () {
      clearTimeout(t);
      t = setTimeout(drawAll, 200);
    });
  });
})();
