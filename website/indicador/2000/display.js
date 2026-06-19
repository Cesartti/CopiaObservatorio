class Chart1 extends AbstractChart{
    format(){
        var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
        yearFormatter.format(this._data, 0);
    }
    getOptions(info){
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']},
            curveType: 'function',
            pointSize: 6
        };
    }
    getType(div){ return new google.visualization.LineChart(div); }
}

class Chart2 extends AbstractChart{
    format(){
        var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
        yearFormatter.format(this._data, 0);
    }
    getOptions(info){
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']},
            isStacked: false,
            bar: {groupWidth: '70%'}
        };
    }
    getType(div){ return new google.visualization.ColumnChart(div); }
}

class Display extends AbstractDisplay{
    constructor(){ super('corechart',[Chart1,Chart2]); }
}
