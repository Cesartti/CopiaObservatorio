class Chart1 extends AbstractChart{
	
	format(){
		var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
		for(var i=2;i<this._data.bf.length;i++)
			percentFormatter.format(this._data, i);
	}
	
	prepareCsv(csv){
		//Ordena las filas por año y dominio
		csv=csv.sort(
			function(a, b) {
				var res=a[1]-b[1];
				if(res==0){
					if(a[0]<b[0])
						res=-1;
					else if(a[0]>b[0])
						res=1;
				}
				return res;
			}
		);
		//Elimina años seguidos y transforma en String
		var ant=null;
		for(var i=1;i<csv.length;i++){
			if(ant!=csv[i][1]){
				ant=csv[i][1];
				csv[i][1]=csv[i][1].toString();
			}
		}
		return csv;
	}
	
	prepareView(){
		var view = new google.visualization.DataView(this._data);
		var cols=[1,
			{calc: function (dt, row) {
					return dt.getValue(row,0);
				},type: 'string',role: 'annotation'
			}
		];
		for(var col=2;col<this._data.bf.length;col++)
			cols.push(col);
		view.setColumns(cols);
		return view;
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical'], format: Patterns.percent}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super('corechart',[Chart1]);
	}
}