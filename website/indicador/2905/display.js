class Chart1 extends AbstractChart{
  getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
  getType(div){ return new google.visualization.LineChart(div); }
}

class Chart2 extends AbstractChart{
  getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }
  getType(div){ return new google.visualization.LineChart(div); }
}

class Display extends AbstractDisplay{
  constructor(){ super('corechart',[Chart1,Chart2]); }
}
