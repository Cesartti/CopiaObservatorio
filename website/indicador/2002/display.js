class Chart1 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);	
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical']},
			seriesType:'bars',
			series:{4: {type: 'line'}},
			isStacked: true
		};
	}
	
	getType(div){
		return new google.visualization.ComboChart(div);
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Casos','Año',['Sexo','Rango de edad']);
	}
}

class Chart3 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);	
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical']},
			seriesType:'bars',
			series:{4: {type: 'line'}},
			isStacked: true
		};
	}
	
	getType(div){
		return new google.visualization.ComboChart(div);
	}
}

class Chart7 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);	
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical']},
			seriesType:'bars',
			series:{4: {type: 'line'}},
			isStacked: true
		};
	}
	
	getType(div){
		return new google.visualization.ComboChart(div);
	}
}

class Chart5 extends AbstractChart{
	
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

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4,Chart5]);
	}
}