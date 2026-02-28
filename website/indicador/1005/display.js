class Chart1 extends AbstractChart{
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}

	getType(div){
		return new google.visualization.LineChart(div);
	}
}

class Chart2 extends AbstractChart{
	format(){
		var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
		percentFormatter.format(this._data, 2);
		var currencyFormatter = new google.visualization.NumberFormat({pattern: Patterns.currency});
		currencyFormatter.format(this._data, 3);
	}
	
	getOptions(info){
		return {
			colors: [AbstractColors.oppositeEcono,AbstractColors.mainEcono],
			backgroundColor: AbstractColors.ocean,
			datalessRegionColor: '#dfdfdf',
			tooltip: {textStyle:{fontSize: 16}}
		};
	}

	getType(div){
		return new google.visualization.GeoChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart','geochart'],[Chart1,Chart2]);
	}
}