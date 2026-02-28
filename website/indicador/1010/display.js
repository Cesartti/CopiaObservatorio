class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Producción (t)','AÑO');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}