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
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical'],format:Patterns.percent},
			isStacked: true
		};
	}
	
	getType(div){
		return new google.visualization.AreaChart(div);
	}
}

class Chart2 extends AbstractChart{
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

class Chart3 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
		var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
		for(var i=1;i<this._data.bf.length;i++)
			percentFormatter.format(this._data, i);
	}

	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical'],format:Patterns.percent},
			isStacked: true
		};
	}
	
	getType(div){
		return new google.visualization.AreaChart(div);
	}
}

class Chart4 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Valor','Año','Minerales');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super('corechart',[Chart1,Chart2,Chart3,Chart4]);
	}
}