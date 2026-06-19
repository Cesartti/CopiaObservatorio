class Chart1 extends AbstractChart{
    format(){
        var f = new google.visualization.NumberFormat({pattern: Patterns.year});
        f.format(this._data, 0);
    }
    getOptions(info){
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']},
            curveType: 'function',
            pointSize: 6,
            chartArea: {left: 80, right: 30, top: 50, bottom: 50}
        };
    }
    getType(div){ return new google.visualization.LineChart(div); }
}

class Chart2 extends AbstractChart{
    format(){
        var f = new google.visualization.NumberFormat({pattern: Patterns.year});
        f.format(this._data, 0);
    }
    getOptions(info){
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']},
            bar: {groupWidth: '70%'},
            chartArea: {left: 80, right: 30, top: 50, bottom: 50}
        };
    }
    getType(div){ return new google.visualization.ColumnChart(div); }
}

class Display extends AbstractDisplay{
    constructor(){ super('corechart',[Chart1,Chart2]); }
}
