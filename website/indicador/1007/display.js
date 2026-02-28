class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Porcentaje','Año');
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Pesos','Año');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1,Chart2]);
	}
}