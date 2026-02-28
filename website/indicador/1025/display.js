class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Área Cosechada(ha)','Año','Desagregación');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}