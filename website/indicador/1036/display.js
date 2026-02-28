class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Área Sembrada','Año',['Subgrupo','Cultivo']);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}