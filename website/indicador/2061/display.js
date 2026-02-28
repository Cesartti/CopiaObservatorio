class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Tasa','Año',['Nivel'],'geo',true,'%');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}