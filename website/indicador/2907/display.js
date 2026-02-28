class Chart1 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	prepareCsv(csv){
		//Ordena las filas por año y dominio
		csv=csv.sort(
			function(a, b) {
				var res=a[0]-b[0];
				if(res==0){
					if(a[1]<b[1])
						res=-1;
					else if(a[1]>b[1])
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
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Chart2 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Casos','Año',['Sexo','Rango de edad']);
	}
}

class Chart3 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	prepareCsv(csv){
		//Ordena las filas por año y dominio
		csv=csv.sort(
			function(a, b) {
				var res=a[0]-b[0];
				if(res==0){
					if(a[1]<b[1])
						res=-1;
					else if(a[1]>b[1])
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
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Chart4 extends DummyChart{}

class Chart5 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	prepareCsv(csv){
		//Ordena las filas por año y dominio
		csv=csv.sort(
			function(a, b) {
				var res=a[0]-b[0];
				if(res==0){
					if(a[1]<b[1])
						res=-1;
					else if(a[1]>b[1])
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
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Chart6 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	prepareCsv(csv){
		//Ordena las filas por año y dominio
		csv=csv.sort(
			function(a, b) {
				var res=a[0]-b[0];
				if(res==0){
					if(a[1]<b[1])
						res=-1;
					else if(a[1]>b[1])
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
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Chart7 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);	
	}
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format:Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.LineChart(div);
	}
}

class Chart8 extends AbstractChart{
	
	format(){
		var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
		yearFormatter.format(this._data, 0);
	}
	
	prepareCsv(csv){
		//Ordena las filas por año y dominio
		csv=csv.sort(
			function(a, b) {
				var res=a[0]-b[0];
				if(res==0){
					if(a[1]<b[1])
						res=-1;
					else if(a[1]>b[1])
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
	
	getOptions(info){
		return {
			hAxis: {title: info['horizontal'],format: Patterns.year},
			vAxis: {title: info['vertical']}
		};
	}
	
	getType(div){
		return new google.visualization.ColumnChart(div);
	}
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart'],[Chart1,Chart2,Chart3,Chart4,Chart5,Chart6,Chart7,Chart8]);
	}
}