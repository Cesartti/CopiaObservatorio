class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Producción','Año');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}