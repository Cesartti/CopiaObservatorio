class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Área sembrada','Año',['Grupo cultivo','Desagregación cultivo','Ciclo del cultivo']);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}