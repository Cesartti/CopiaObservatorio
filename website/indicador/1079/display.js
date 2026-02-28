class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Índice','Año');
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Índice promedio 2016-2020');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1,Chart2]);
	}
}