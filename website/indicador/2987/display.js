class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Tasa','Periodo del Indicador','Grupo');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}