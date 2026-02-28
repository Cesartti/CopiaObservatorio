class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Año','Género','Número');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}