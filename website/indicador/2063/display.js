class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Valor','Periodo','Indicador','geo',true,'%');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}