class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Porcentaje','Año',null,'geo',false);
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Porcentaje','Año',null,'geo',false);
	}
}

class Chart3 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart4 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, legend:{position:'top'}, bar:{groupWidth:'70%'}, isStacked:true }; }
	getType(div){ return new google.visualization.ColumnChart(div); }
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4]);
	}
}
