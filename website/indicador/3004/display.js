class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Porcentaje','Año',null,'geo',false);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}