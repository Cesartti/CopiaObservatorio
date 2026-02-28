class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Producción','Año','Tipo');
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Porcentaje','Año','Tipo');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1,Chart2]);
	}
}