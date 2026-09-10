/* PIB departamental: 4 gráficas (faltaba el display.js y los paneles salían vacíos). */

class Chart1 extends AbstractChart{
	format(){
		var f = new google.visualization.NumberFormat({pattern: Patterns.year});
		f.format(this._data, 0);
	}
	prepareView(){
		var v = new google.visualization.DataView(this._data);
		v.setColumns([0, 1, 2]);
		return v;
	}
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'], format: Patterns.year},
			vAxis: {title: 'Miles de millones de pesos'},
			curveType: 'function',
			pointSize: 5
		};
	}
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart2 extends AbstractChart{
	format(){
		var f = new google.visualization.NumberFormat({pattern: Patterns.year});
		f.format(this._data, 0);
	}
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'], format: Patterns.year},
			vAxis: {title: info['vertical']},
			curveType: 'function',
			pointSize: 5
		};
	}
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart3 extends AbstractChart{
	format(){
		var f = new google.visualization.NumberFormat({pattern: Patterns.year});
		f.format(this._data, 0);
	}
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'], format: Patterns.year},
			vAxis: {title: info['vertical']},
			bar: {groupWidth: '75%'}
		};
	}
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Chart4 extends AbstractChart{
	format(){
		var f = new google.visualization.NumberFormat({pattern: Patterns.year});
		f.format(this._data, 0);
	}
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'], format: Patterns.year},
			vAxis: {title: info['vertical']},
			isStacked: true,
			bar: {groupWidth: '75%'},
			legend: {position: 'right', textStyle: {fontSize: 11}},
			chartArea: {left: 80, right: 260, top: 20, bottom: 50}
		};
	}
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4]);
	}
}
