class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Tasa','Año',['Grupo','Edad']);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}