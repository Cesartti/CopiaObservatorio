class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'victima directa',null,'tipo victima');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}