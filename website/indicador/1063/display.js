class Chart1 extends AbstractChart{
	getOptions(info){
		return {
			hAxis: {title: info['horizontal']},
			vAxis: {title: info['vertical']},
			isStacked: true
		};
	}
	
	getType(div){
		return new google.visualization.BarChart(div);
	}
}

class Chart2 extends AbstractChart{
	getOptions(info){
		return {
			hAxis: {title: info['horizontal']},
			vAxis: {title: info['vertical']},
			isStacked: true
		};
	}
	
	getType(div){
		return new google.visualization.BarChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super('corechart',[Chart1,Chart2]);
	}
}