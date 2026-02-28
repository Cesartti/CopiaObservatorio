class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Índice','Año','Trimestre');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}