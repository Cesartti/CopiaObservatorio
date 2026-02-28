class Chart1 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
		var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
		for(var i=1;i<this._data.bf.length;i++)
			percentFormatter.format(this._data, i);
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical'], format: Patterns.percent}
		};
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