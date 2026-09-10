class Chart1 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart2 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, legend:{position:'none'}, bar:{groupWidth:'70%'} }; }
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Chart3 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Incendios','Año',null,'geo',false);
	}
}

class Chart4 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Hectareas',null,null,'geo',false);
	}
}

class Chart5 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['vertical']}, vAxis:{title:info['horizontal']}, legend:{position:'none'}, chartArea:{left:230,top:20,width:'62%%',height:'85%%'} }; }
	getType(div){ return new google.visualization.BarChart(div); }
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4,Chart5]);
	}
}
