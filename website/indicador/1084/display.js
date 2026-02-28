class Chart1 extends AbstractChart{
	getOptions(info){
		return {width: '100%'};
	}
	
	getType(div){
		return new google.visualization.Table(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['table'],[Chart1]);
	}
}