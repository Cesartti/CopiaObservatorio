class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Valor',null,'tipo de victima');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}