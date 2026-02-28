class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Valor','Periodo',null,'geo',false);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}