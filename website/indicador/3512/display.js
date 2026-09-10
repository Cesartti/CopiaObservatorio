class Chart1 extends AbstractChart{
	format(){
		var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
		percentFormatter.format(this._data, 1);		
	}

	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.percent}
		};
	}
	
	getType(div){
		return new google.visualization.BarChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1]);
	}
}