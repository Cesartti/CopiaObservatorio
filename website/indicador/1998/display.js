class Chart1 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Chart2 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Chart3 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);		
	}
	
	getOptions(info){
		//TODO: leer eje con ; y partir en 2 variables
		var y=info['vertical'];
		return {
			hAxis:{title: info['horizontal'],format:Patterns.year},
			vAxes:{
				0: {title: y},
				1: {title: y}
			},
			seriesType:'bars',
			series:{1: {type: 'line',targetAxisIndex: 1}}
		};
	}
	
	getType(div){
		return new google.visualization.ComboChart(div);
	}
}

class Chart4 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Producción (ton)','Año','Ciclo del cultivo');
	}
}

class Chart5 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Hectareas','Año','Área');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4,Chart5]);
	}
}