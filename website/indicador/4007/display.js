class Chart1 extends AbstractChart{
	getOptions(info){
		return {width: '100%'};
	}
	
	getType(div){
		return new google.visualization.Table(div);
	}
}

class Chart2 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
		var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
		percentFormatter.format(this._data, 1);		
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical'],format: Patterns.percent}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart','table'],[Chart1,Chart2]);
	}
}