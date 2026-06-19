"""
Plantillas para generar `display.js` con clases que extienden AbstractChart.

Cada clase corresponde a un tipo de visualización:
  - 'Line'       → LineChart con eje X de año
  - 'Column'     → ColumnChart (barras verticales)
  - 'Bar'        → BarChart (barras horizontales)
  - 'ColumnPct'  → ColumnChart con eje Y porcentual
  - 'Combo'      → ComboChart (barras + línea)
  - 'Pie'        → PieChart
  - 'Map'        → mapa coroplético usando AbstractMap
"""

CHART_BLOCK_LINE = """class {name} extends AbstractChart{{
    format(){{
        var f = new google.visualization.NumberFormat({{pattern: Patterns.year}});
        f.format(this._data, 0);
    }}
    getOptions(info){{
        return {{
            hAxis: {{title: info['horizontal'], format: Patterns.year}},
            vAxis: {{title: info['vertical']}},
            curveType: 'function',
            pointSize: 6,
            chartArea: {{left: 80, right: 30, top: 50, bottom: 50}}
        }};
    }}
    getType(div){{ return new google.visualization.LineChart(div); }}
}}"""

CHART_BLOCK_COLUMN = """class {name} extends AbstractChart{{
    format(){{
        var f = new google.visualization.NumberFormat({{pattern: Patterns.year}});
        f.format(this._data, 0);
    }}
    getOptions(info){{
        return {{
            hAxis: {{title: info['horizontal'], format: Patterns.year}},
            vAxis: {{title: info['vertical']}},
            bar: {{groupWidth: '70%'}},
            chartArea: {{left: 80, right: 30, top: 50, bottom: 50}}
        }};
    }}
    getType(div){{ return new google.visualization.ColumnChart(div); }}
}}"""

CHART_BLOCK_BAR = """class {name} extends AbstractChart{{
    getOptions(info){{
        return {{
            hAxis: {{title: info['vertical']}},
            vAxis: {{title: info['horizontal']}},
            bar: {{groupWidth: '60%'}},
            legend: {{position: 'none'}},
            chartArea: {{left: 200, right: 30, top: 30, bottom: 50}}
        }};
    }}
    getType(div){{ return new google.visualization.BarChart(div); }}
}}"""

CHART_BLOCK_COLUMN_PCT = """class {name} extends AbstractChart{{
    format(){{
        var f = new google.visualization.NumberFormat({{pattern: Patterns.year}});
        f.format(this._data, 0);
        var pf = new google.visualization.NumberFormat({{pattern: Patterns.percent}});
        for (var i=1;i<this._data.bf.length;i++) pf.format(this._data, i);
    }}
    getOptions(info){{
        return {{
            hAxis: {{title: info['horizontal'], format: Patterns.year}},
            vAxis: {{title: info['vertical'], format: Patterns.percent}},
            bar: {{groupWidth: '70%'}},
            chartArea: {{left: 80, right: 30, top: 50, bottom: 50}}
        }};
    }}
    getType(div){{ return new google.visualization.ColumnChart(div); }}
}}"""

CHART_BLOCK_COMBO = """class {name} extends AbstractChart{{
    format(){{
        var f = new google.visualization.NumberFormat({{pattern: Patterns.year}});
        f.format(this._data, 0);
    }}
    getOptions(info){{
        return {{
            hAxis: {{title: info['horizontal'], format: Patterns.year}},
            vAxes: {{0: {{title: info['vertical']}}, 1: {{title: info['vertical']}}}},
            seriesType: 'bars',
            series: {{0: {{type: 'line', targetAxisIndex: 0}}}},
            chartArea: {{left: 80, right: 60, top: 50, bottom: 50}}
        }};
    }}
    getType(div){{ return new google.visualization.ComboChart(div); }}
}}"""

CHART_BLOCK_PIE = """class {name} extends AbstractChart{{
    getOptions(info){{
        return {{
            pieHole: 0.35,
            chartArea: {{width: '90%', height: '85%'}},
            legend: {{position: 'right'}}
        }};
    }}
    getType(div){{ return new google.visualization.PieChart(div); }}
}}"""

# Para mapas: el CSV debe tener columna "Cod mun" + valores. Usa AbstractMap (Leaflet).
CHART_BLOCK_MAP = """class {name} extends AbstractMap{{
    constructor(info,csv,chart){{
        // val=2 (col valor), time=null, cat=null, geo=0 (col código municipio)
        super(info, csv, chart, 2, null, null, 0, true, null);
    }}
}}"""

CHART_BLOCK_MAP_TIME = """class {name} extends AbstractMap{{
    constructor(info,csv,chart){{
        // geo=col 0 (código mun), val=col 2 (valor), time=col 1 (año)
        super(info, csv, chart, 2, 1, null, 0, true, null);
    }}
}}"""

BLOCK_BY_KIND = {
    'Line':       CHART_BLOCK_LINE,
    'Column':     CHART_BLOCK_COLUMN,
    'Bar':        CHART_BLOCK_BAR,
    'ColumnPct':  CHART_BLOCK_COLUMN_PCT,
    'Combo':      CHART_BLOCK_COMBO,
    'Pie':        CHART_BLOCK_PIE,
    'Map':        CHART_BLOCK_MAP,
    'MapTime':    CHART_BLOCK_MAP_TIME,
}

def build_display_js(kinds: list[str]) -> str:
    """
    kinds: lista con el tipo de cada chart (1, 2, 3...).
    Genera el `display.js` completo con las clases ChartN y la clase Display.
    """
    blocks = []
    class_names = []
    for i, kind in enumerate(kinds, start=1):
        name = f"Chart{i}"
        class_names.append(name)
        tmpl = BLOCK_BY_KIND.get(kind, CHART_BLOCK_LINE)
        blocks.append(tmpl.format(name=name))
    blocks.append(
        "class Display extends AbstractDisplay{\n"
        f"    constructor(){{ super('corechart',[{','.join(class_names)}]); }}\n"
        "}"
    )
    return "\n\n".join(blocks) + "\n"
