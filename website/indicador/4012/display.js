class Chart1 extends AbstractChart{
	getOptions(info){
		return {width: '100%'};
	}
	
	getType(div){
		return new google.visualization.Table(div);
	}
}

class Chart2 extends AbstractChart{
	getOptions(info){
		return {width: '100%'};
	}
	getType(div){
		return new google.visualization.Table(div);
	}
}

class Chart3 extends AbstractChart{
	getOptions(info){
		return {width: '100%'};
	}
	getType(div){
		return new google.visualization.Table(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['table'],[Chart1,Chart2,Chart3]);
	}
}