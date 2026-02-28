class Chart1 extends AbstractChart{
	getOptions(info){
		return {
			hAxis: {title: info['horizontal']}
		};
	}
	
	getType(div){
		return new google.visualization.BarChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1]);
	}
}