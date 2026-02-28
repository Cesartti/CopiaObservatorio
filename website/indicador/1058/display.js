class Chart1 extends AbstractChart{
	getOptions(info){
		return {
			'chartArea': {'left':'18%','width': '80%', 'height': '80%'},
			hAxis: {title: info['horizontal']},
			vAxis: {title: info['vertical']},
			isStacked: true
		};
	}
	
	getType(div){
		div.style.height='600px';
		return new google.visualization.BarChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super('corechart',[Chart1]);
	}
}