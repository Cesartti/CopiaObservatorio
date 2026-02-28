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

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Producción','Año','Desagregación');
	}
}

class Chart3 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Producción','Año','Desagregación');
	}
}

class Chart4 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Producción','Año','Desagregación');
	}
}

class Chart5 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Producción','Año','Desagregación');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4,Chart5]);
	}
}