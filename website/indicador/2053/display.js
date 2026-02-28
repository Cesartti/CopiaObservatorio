class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Número de victimas');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}