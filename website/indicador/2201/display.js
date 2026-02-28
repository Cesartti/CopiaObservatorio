class Chart1 extends AbstractChart {
    format() {
        // Formatea el año en la primera columna
        var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
        yearFormatter.format(this._data, 0);
    }

    prepareCsv(csv) {
        // Ordena las filas por año
        csv = csv.sort((a, b) => a[0] - b[0]);
        return csv;
    }

    getOptions(info) {
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']}
        };
    }

    getType(div) {
        return new google.visualization.ColumnChart(div);
    }
}

class Chart2 extends AbstractChart {
    format() {
        // Formatea el año en la primera columna
        var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
        yearFormatter.format(this._data, 0);
    }

    prepareCsv(csv) {
        // Ordena las filas por año
        csv = csv.sort((a, b) => a[0] - b[0]);
        return csv;
    }

    getOptions(info) {
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']}
        };
    }

    getType(div) {
        return new google.visualization.ColumnChart(div);
    }
}

class Chart3 extends AbstractChart {
    format() {
        // Formatea el año en la primera columna
        var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
        yearFormatter.format(this._data, 0);
    }

    prepareCsv(csv) {
        // Ordena las filas por año
        csv = csv.sort((a, b) => a[0] - b[0]);
        return csv;
    }

    getOptions(info) {
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']}
        };
    }

    getType(div) {
        return new google.visualization.ColumnChart(div);
    }
}

class Chart4 extends AbstractChart {
    format() {
        // Formatea el año en la primera columna
        var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
        yearFormatter.format(this._data, 0);
    }

    prepareCsv(csv) {
        // Ordena las filas por año
        csv = csv.sort((a, b) => a[0] - b[0]);
        return csv;
    }

    getOptions(info) {
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']}
        };
    }

    getType(div) {
        return new google.visualization.ColumnChart(div);
    }
}

class Chart5 extends AbstractMap {
    constructor(info, csv, chart) {
        // Mapa: Define las columnas de DANE, población alfabetizada, población analfabeta, y tasa
        super(info, csv, chart, 'Tasa de analfabetismo', 'Año', ['Población', 'Alfabetizados']);
    }
}

class Display extends AbstractDisplay {
    constructor() {
        // Incluye las clases de los gráficos y el componente de Google Charts requerido
        super(['corechart'], [Chart1, Chart2, Chart3, Chart4, Chart5]);
    }
}