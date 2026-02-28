class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Numerador (casos)','Año','Grupo');
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Tasa','Año','Grupo');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1,Chart2]);
	}
}