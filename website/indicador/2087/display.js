class Chart1 extends AbstractChart{
	getOptions(info){
		return {width: '100%'};
	}
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 1);
		var numberFormatter = new google.visualization.NumberFormat({fractionDigits: 2});
		numberFormatter.format(this._data, 3);
	}
	
	getType(div){
		return new google.visualization.Table(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super('table',[Chart1]);
	}
}