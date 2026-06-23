class Chart1 extends AbstractChart{
  getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, legend:{position:'none'}, bar:{groupWidth:'70%'} }; }
  getType(div){ return new google.visualization.ColumnChart(div); }
}

class Display extends AbstractDisplay{
  constructor(){ super('corechart',[Chart1]); }
}
