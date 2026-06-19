class Chart1 extends AbstractChart{
    getOptions(info){
        return { hAxis: {title: info['horizontal'], format: Patterns.year},
                 vAxis: {title: info['vertical']}, curveType: 'function', pointSize: 6 };
    }
    getType(div){ return new google.visualization.LineChart(div); }
}

class Chart2 extends AbstractChart{
    getOptions(info){
        return { hAxis: {title: info['horizontal'], format: Patterns.year},
                 vAxis: {title: info['vertical']}, curveType: 'function', pointSize: 6 };
    }
    getType(div){ return new google.visualization.LineChart(div); }
}

class Display extends AbstractDisplay{
    constructor(){ super('corechart',[Chart1,Chart2]); }
}
