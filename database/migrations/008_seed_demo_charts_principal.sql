-- Demo charts for principal dashboards (only if dashboard has zero charts).
-- Replace from CMS when you upload real CSV.

INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json, sort_order, layout_span, tile_height_px, is_active, created_by)
SELECT d.id, t.title, t.chart_type, t.options_json, t.data_json, t.sort_order, t.layout_span, t.tile_height_px, 1, NULL
FROM cms_dashboards d
INNER JOIN observatories o ON o.id = d.observatory_id
CROSS JOIN (
  SELECT 'Demostracion: categorias' AS title, 'ColumnChart' AS chart_type,
    '{"colors":["#5f2a8a"],"legend":{"position":"none"}}' AS options_json,
    '[["Categoria","Casos"],["A",48],["B",72],["C",35],["D",61]]' AS data_json,
    1 AS sort_order, 6 AS layout_span, 300 AS tile_height_px
  UNION ALL
  SELECT 'Demostracion: barras horizontales', 'BarChart',
    '{"colors":["#5f2a8a"],"legend":{"position":"none"}}',
    '[["Grupo","Valor"],["Etapa 1",210],["Etapa 2",450],["Etapa 3",890],["Etapa 4",320]]',
    2, 6, 300
  UNION ALL
  SELECT 'Demostracion: serie temporal', 'LineChart',
    '{"colors":["#5f2a8a"],"pointSize":5,"legend":{"position":"none"}}',
    '[["Periodo","Total"],["2020-01",42],["2020-06",55],["2021-01",61],["2021-06",58],["2022-01",70],["2022-06",66],["2023-01",63]]',
    3, 12, 340
) AS t
WHERE o.slug = 'social' AND d.slug = 'principal'
  AND NOT EXISTS (SELECT 1 FROM cms_charts c WHERE c.dashboard_id = d.id);

INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json, sort_order, layout_span, tile_height_px, is_active, created_by)
SELECT d.id, t.title, t.chart_type, t.options_json, t.data_json, t.sort_order, t.layout_span, t.tile_height_px, 1, NULL
FROM cms_dashboards d
INNER JOIN observatories o ON o.id = d.observatory_id
CROSS JOIN (
  SELECT 'Demostracion: indicadores anuales' AS title, 'ColumnChart' AS chart_type,
    '{"colors":["#0f3557"],"legend":{"position":"none"}}' AS options_json,
    '[["Anio","Indice"],["2020",100],["2021",103],["2022",107],["2023",105],["2024",109]]' AS data_json,
    1 AS sort_order, 6 AS layout_span, 300 AS tile_height_px
  UNION ALL
  SELECT 'Demostracion: tendencia', 'AreaChart',
    '{"colors":["#d8a21d"],"legend":{"position":"none"}}',
    '[["Trimestre","Variacion"],["T1",0.8],["T2",1.1],["T3",0.9],["T4",1.2]]',
    2, 6, 300
  UNION ALL
  SELECT 'Demostracion: composicion', 'PieChart',
    '{"colors":["#0f3557","#1a4a73","#d8a21d","#2a5f8f","#e8b84a"]}',
    '[["Rubro","Participacion"],["Rubro A",35],["Rubro B",28],["Rubro C",22],["Rubro D",15]]',
    3, 12, 320
) AS t
WHERE o.slug = 'economico' AND d.slug = 'principal'
  AND NOT EXISTS (SELECT 1 FROM cms_charts c WHERE c.dashboard_id = d.id);
