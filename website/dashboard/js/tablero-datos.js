/**
 * Tablero exploratorio: Excel/CSV → ECharts (cliente).
 * Requiere: SheetJS (XLSX), ECharts.
 */
(function () {
  'use strict';

  let rawRows = [];
  let headers = [];
  let chartBarras = null;
  let chartLineas = null;
  let chartPastel = null;

  const elArchivo = document.getElementById('archivoDatos');
  const elHoja = document.getElementById('hojaExcel');
  const elColCat = document.getElementById('colCategoria');
  const elColVal = document.getElementById('colValor');
  const elColFiltro = document.getElementById('colFiltro');
  const elValorFiltro = document.getElementById('valorFiltro');
  const btnAplicar = document.getElementById('btnAplicar');
  const btnLimpiar = document.getElementById('btnLimpiar');
  const tablaPreview = document.getElementById('tablaPreview');

  function initCharts() {
    const domB = document.getElementById('chartBarras');
    const domL = document.getElementById('chartLineas');
    const domP = document.getElementById('chartPastel');
    if (typeof echarts === 'undefined' || !domB) return;
    chartBarras = echarts.init(domB);
    chartLineas = echarts.init(domL);
    chartPastel = echarts.init(domP);
    window.addEventListener('resize', () => {
      chartBarras && chartBarras.resize();
      chartLineas && chartLineas.resize();
      chartPastel && chartPastel.resize();
    });
  }

  function sheetToMatrix(sheet) {
    if (typeof XLSX === 'undefined') return [];
    const ref = sheet['!ref'];
    if (!ref) return [];
    return XLSX.utils.sheet_to_json(sheet, { header: 1, defval: '', raw: false });
  }

  function parseNumber(v) {
    if (v === '' || v === null || v === undefined) return NaN;
    if (typeof v === 'number' && !Number.isNaN(v)) return v;
    const s = String(v).trim().replace(/\s/g, '').replace(',', '.');
    const n = parseFloat(s);
    return Number.isFinite(n) ? n : NaN;
  }

  function inferNumericColumnIndex(matrix, colIdx) {
    let ok = 0;
    let total = 0;
    const max = Math.min(matrix.length, 200);
    for (let r = 1; r < max; r++) {
      const row = matrix[r];
      if (!row || row[colIdx] === undefined || row[colIdx] === '') continue;
      total++;
      if (!Number.isNaN(parseNumber(row[colIdx]))) ok++;
    }
    if (total === 0) return false;
    return ok / total >= 0.6;
  }

  function fillSelect(el, options, selected) {
    el.innerHTML = '';
    options.forEach((opt, i) => {
      const o = document.createElement('option');
      o.value = String(i);
      o.textContent = opt === '' ? '(vacío)' : String(opt);
      el.appendChild(o);
    });
    if (selected !== undefined) el.value = String(selected);
  }

  function buildTablePreview(matrix, maxRows) {
    if (!matrix.length) {
      tablaPreview.innerHTML = '<p class="text-muted m-2">Sin datos.</p>';
      return;
    }
    const h = matrix[0] || [];
    let html = '<table class="table table-sm table-bordered table-striped mb-0"><thead><tr>';
    h.forEach((c) => {
      html += '<th>' + String(c).replace(/</g, '&lt;') + '</th>';
    });
    html += '</tr></thead><tbody>';
    const n = Math.min(matrix.length - 1, maxRows);
    for (let r = 1; r <= n; r++) {
      html += '<tr>';
      const row = matrix[r] || [];
      for (let c = 0; c < h.length; c++) {
        const cell = row[c] !== undefined ? row[c] : '';
        html += '<td>' + String(cell).replace(/</g, '&lt;') + '</td>';
      }
      html += '</tr>';
    }
    html += '</tbody></table>';
    if (matrix.length - 1 > maxRows) {
      html += '<p class="text-muted small m-2">… y más filas</p>';
    }
    tablaPreview.innerHTML = html;
  }

  function getFilteredRows(catIdx, valIdx, filtIdx, filtVal) {
    const out = [];
    for (let r = 1; r < rawRows.length; r++) {
      const row = rawRows[r];
      if (!row) continue;
      if (filtIdx !== '' && filtIdx !== null) {
        const fi = parseInt(filtIdx, 10);
        const cellF = row[fi];
        if (String(cellF) !== String(filtVal)) continue;
      }
      const cat = row[catIdx];
      const num = parseNumber(row[valIdx]);
      if (cat === undefined || cat === '' || Number.isNaN(num)) continue;
      out.push({ cat: String(cat), val: num });
    }
    return out;
  }

  function aggregate(rows) {
    const map = new Map();
    rows.forEach(({ cat, val }) => {
      map.set(cat, (map.get(cat) || 0) + val);
    });
    const cats = [];
    const vals = [];
    map.forEach((v, k) => {
      cats.push(k);
      vals.push(Math.round(v * 1000) / 1000);
    });
    return { cats, vals };
  }

  function renderCharts(catIdx, valIdx, filtIdx, filtVal) {
    const rows = getFilteredRows(catIdx, valIdx, filtIdx, filtVal);
    const { cats, vals } = aggregate(rows);

    const commonGrid = { left: '3%', right: '4%', bottom: '12%', containLabel: true };
    const commonTooltip = { trigger: 'axis' };

    if (chartBarras) {
      chartBarras.setOption({
        tooltip: commonTooltip,
        grid: commonGrid,
        xAxis: { type: 'category', data: cats, axisLabel: { rotate: cats.length > 8 ? 35 : 0 } },
        yAxis: { type: 'value' },
        series: [{ type: 'bar', data: vals, itemStyle: { color: '#17a2b8' } }],
      }, true);
    }
    if (chartLineas) {
      chartLineas.setOption({
        tooltip: commonTooltip,
        grid: commonGrid,
        xAxis: { type: 'category', data: cats },
        yAxis: { type: 'value' },
        series: [{ type: 'line', data: vals, smooth: true, areaStyle: { opacity: 0.12 }, itemStyle: { color: '#28a745' } }],
      }, true);
    }
    if (chartPastel) {
      const pieData = cats.map((name, i) => ({ name, value: vals[i] }));
      chartPastel.setOption({
        tooltip: { trigger: 'item' },
        legend: { bottom: 0, type: 'scroll' },
        series: [{
          type: 'pie',
          radius: ['35%', '62%'],
          data: pieData,
          emphasis: { itemStyle: { shadowBlur: 10 } },
        }],
      }, true);
    }
  }

  function updateFiltroValues() {
    const fIdx = elColFiltro.value;
    elValorFiltro.innerHTML = '';
    if (fIdx === '') {
      elValorFiltro.disabled = true;
      const o = document.createElement('option');
      o.value = '';
      o.textContent = '—';
      elValorFiltro.appendChild(o);
      return;
    }
    const col = parseInt(fIdx, 10);
    const set = new Set();
    for (let r = 1; r < rawRows.length; r++) {
      const row = rawRows[r];
      if (!row || row[col] === undefined || row[col] === '') continue;
      set.add(String(row[col]));
    }
    const list = Array.from(set).sort();
    list.forEach((v) => {
      const o = document.createElement('option');
      o.value = v;
      o.textContent = v.length > 60 ? v.slice(0, 57) + '…' : v;
      elValorFiltro.appendChild(o);
    });
    elValorFiltro.disabled = list.length === 0;
  }

  function onFile(e) {
    const file = e.target.files && e.target.files[0];
    if (!file || typeof XLSX === 'undefined') return;

    const reader = new FileReader();
    reader.onload = function (ev) {
      const data = new Uint8Array(ev.target.result);
      let wb;
      try {
        wb = XLSX.read(data, { type: 'array' });
      } catch (err) {
        console.error(err);
        alert('No se pudo leer el archivo.');
        return;
      }
      elHoja.innerHTML = '';
      wb.SheetNames.forEach((name) => {
        const o = document.createElement('option');
        o.value = name;
        o.textContent = name;
        elHoja.appendChild(o);
      });
      elHoja.disabled = wb.SheetNames.length === 0;
      if (wb.SheetNames.length) elHoja.value = wb.SheetNames[0];

      function applySheet() {
        const name = elHoja.value;
        const sheet = wb.Sheets[name];
        const matrix = sheetToMatrix(sheet);
        rawRows = matrix;
        if (!matrix.length) {
          headers = [];
          return;
        }
        headers = (matrix[0] || []).map((h, i) => (h === '' || h === undefined ? 'Col ' + (i + 1) : String(h)));

        buildTablePreview(matrix, 15);

        const numCols = [];
        for (let c = 0; c < headers.length; c++) {
          if (inferNumericColumnIndex(matrix, c)) numCols.push(c);
        }
        const firstCat = 0;
        let firstNum = numCols.find(function (c) { return c !== firstCat; });
        if (firstNum === undefined) {
          firstNum = numCols.length ? numCols[0] : Math.min(1, Math.max(0, headers.length - 1));
        }

        fillSelect(elColCat, headers, firstCat);
        fillSelect(elColVal, headers, firstNum);
        elColFiltro.innerHTML = '<option value="">— Ninguna —</option>';
        headers.forEach((h, i) => {
          const o = document.createElement('option');
          o.value = String(i);
          o.textContent = h;
          elColFiltro.appendChild(o);
        });

        elColCategoria.disabled = false;
        elColValor.disabled = false;
        elColFiltro.disabled = false;
        btnAplicar.disabled = false;
        btnLimpiar.disabled = false;
        updateFiltroValues();
        renderCharts(firstCat, firstNum, '', '');
      }

      elHoja.onchange = applySheet;
      applySheet();
    };
    reader.readAsArrayBuffer(file);
  }

  function onAplicar() {
    const ci = parseInt(elColCategoria.value, 10);
    const vi = parseInt(elColValor.value, 10);
    const fi = elColFiltro.value;
    const fv = elValorFiltro.value;
    renderCharts(ci, vi, fi, fv);
  }

  function onFiltroColChange() {
    updateFiltroValues();
  }

  function onLimpiar() {
    elArchivo.value = '';
    rawRows = [];
    headers = [];
    elHoja.innerHTML = '<option value="">—</option>';
    elHoja.disabled = true;
    elColCategoria.innerHTML = '';
    elColValor.innerHTML = '';
    elColFiltro.innerHTML = '<option value="">— Ninguna —</option>';
    elValorFiltro.innerHTML = '<option value="">—</option>';
    elColCategoria.disabled = true;
    elColValor.disabled = true;
    elColFiltro.disabled = true;
    elValorFiltro.disabled = true;
    btnAplicar.disabled = true;
    btnLimpiar.disabled = true;
    tablaPreview.innerHTML = '<p class="text-muted m-2">Cargue un archivo para ver la tabla.</p>';
    if (chartBarras) chartBarras.clear();
    if (chartLineas) chartLineas.clear();
    if (chartPastel) chartPastel.clear();
  }

  document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    if (elArchivo) elArchivo.addEventListener('change', onFile);
    if (btnAplicar) btnAplicar.addEventListener('click', onAplicar);
    if (btnLimpiar) btnLimpiar.addEventListener('click', onLimpiar);
    if (elColFiltro) elColFiltro.addEventListener('change', onFiltroColChange);
  });
})();
