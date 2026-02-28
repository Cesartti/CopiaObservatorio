class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'CONTINUIDAD ACUEDUCTO DIAS A LA SEMANA URBANA','Año');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(null,[Chart1]);
	}
}