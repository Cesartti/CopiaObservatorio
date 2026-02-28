class Chart1 extends AbstractChart{
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical']},
			isStacked: true
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1]);
	}
}