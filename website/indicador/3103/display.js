class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Hectareas',null,null,'geo',false);
	}
}

class Chart2 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, legend:{position:'none'}, bar:{groupWidth:'70%'} }; }
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Chart3 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, legend:{position:'none'}, bar:{groupWidth:'70%'} }; }
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3]);
	}
}
