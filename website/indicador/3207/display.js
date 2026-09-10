class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Acueductos',null,null,'geo',false);
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Suscriptores',null,null,'geo',false);
	}
}

class Chart3 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, legend:{position:'none'}, bar:{groupWidth:'70%'} }; }
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Chart4 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, legend:{position:'top'}, bar:{groupWidth:'70%'}, isStacked:true }; }
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Chart5 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, legend:{position:'none'}, bar:{groupWidth:'70%'} }; }
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4,Chart5]);
	}
}
