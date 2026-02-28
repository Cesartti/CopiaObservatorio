class Chart1 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		//yearFormatter.format(this._data, 0);
		var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
		percentFormatter.format(this._data, 3);		
	}
	
	getOptions(info){
		var y=info['vertical'].split(';');
		return {
			hAxis:{title: info['horizontal']},
			//Para multi Axis se debe utilizar Axes con e, de lo contrario no sale el título 2, el formato tiene truco incluyendo vAxis
			vAxes:{
				0: {title: y[0],format:'decimal'},
				1: {title: y[1]}
			},
			vAxis:{format: Patterns.percent},
			seriesType:'bars',
			series:{2: {type: 'line',targetAxisIndex: 1}}
		};
	}
	
	getType(div){
		return new google.visualization.ComboChart(div);
	}
}

class Chart2 extends AbstractChart{
	
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
		return new google.visualization.ColumnChart(div);
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
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.LineChart(div);
	}
}

class Chart4 extends AbstractChart{
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
		return new google.visualization.LineChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super('corechart',[Chart1,Chart2,Chart3,Chart4]);
	}
}
