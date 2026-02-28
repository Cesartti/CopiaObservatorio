class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Area Sembrada (ha)','Año','Cultivo');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}