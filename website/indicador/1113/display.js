class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Hembra','Año','Sexo');
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Cantidad','Año','Rango edad');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1,Chart2]);
	}
}