class Chart1 extends AbstractChart{
	prepareCsv(csv){
		//Ordena las filas por año y dominio
		csv=csv.sort(
			function(a, b) {
				var res=a[0]-b[0];
				if(res==0){
					if(a[1]<b[1])
						res=-1;
					else if(a[1]<b[1])
						res=1;
				}
				return res;
			}
		);
		//Elimina años seguidos y transforma en String
		var ant=null;
		for(var i=1;i<csv.length;i++){
			if(ant!=csv[i][0]){
				ant=csv[i][0];
				csv[i][0]=csv[i][0].toString();
			}
		}
		return csv;
	}
	
	prepareView(){
		var view = new google.visualization.DataView(this._data);
		var cols=[0,
			{calc: function (dt, row) {
					return dt.getValue(row,1);
				},type: 'string',role: 'annotation'
			}
		];
		for(var col=2;col<this._data.bf.length;col++)
			cols.push(col);
		view.setColumns(cols);
		return view;
	}
	
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}
class Chart2 extends DummyChart{}

class Chart3 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Valor',null,'Atributo');
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super('corechart',[Chart1,Chart2,Chart3] );
	}
}
