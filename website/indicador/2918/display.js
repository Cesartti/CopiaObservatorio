class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Componente servicio','Año','Categoria','geo',true,'%');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}