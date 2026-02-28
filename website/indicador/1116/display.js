class Chart1 extends AbstractChart{
	prepareView(){
		var view = new google.visualization.DataView(this._data);
		view.setColumns([1,2]);
		return view;
	}
	
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