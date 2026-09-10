class Chart1 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart2 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart3 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart4 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart5 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
	getType(div){ return new google.visualization.LineChart(div); }
}

class Chart6 extends AbstractChart{
	getOptions(info){ return { hAxis:{title:info['horizontal'],format:'####'}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
	getType(div){ return new google.visualization.LineChart(div); }
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4,Chart5,Chart6]);
	}
}
