class Chart1 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	getOptions(info){
		var total=this._csv[0].length-2;
		var res={
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical']},
			pointSize: 4,
			series: {}
		};
		res['series'][total]={lineDashStyle: [4,4]};
		return res;
	}
	
	getType(div){
		return new google.visualization.LineChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super('corechart',[Chart1]);
	}
}