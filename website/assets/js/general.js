class Patterns{
	static percent='#.##%';
	static year='####';
	static currency='$#,###';
}

class AbstractChart{	
	_info;
	_csv;
	_chart;
	_data;
	_view;
	_options;
	
	constructor(info,csv,chart){
		this._info=info;
		for(var i=0;i<csv[0].length;i++)
			csv[0][i]=csv[0][i].toString();
		this._csv=this.prepareCsv(csv);
		this._chart=chart;
		var options=this.getOptions(info);
		if(options){
			this._options=Object.assign({},{
				colors: CustomColors.palette,
				'legend': {position: 'top'}
			}, options);
		}
	}

	getOptions(info){return {};}
	
	prepareCsv(csv){return csv;}
	prepareView(){return null;}
	format(){}
	getType(div){return null;}
	
	draw(){
		if(!this._data){
			this._data=google.visualization.arrayToDataTable(this._csv);
			this._view=this.prepareView();
			this.format();
			this._chart=this.getType(document.getElementById(this._chart));

		}
		var dv=this._data;
		if(this._view)
			dv=this._view;
		this._chart.draw(dv, this._options);
	}
	
}

class DummyChart extends AbstractChart{
	draw(){
		var div=document.getElementById(this._chart);
		div.style.height='auto';
		var dict={};
		var max=0;
		for(var i=1;i<this._csv.length;i++){
			dict[this._csv[i][0]]=this._csv[i][1];
			if(this._csv[i][1]>max)
				max=this._csv[i][1];
		}
		
		div.innerHTML='<span style="color:'+CustomColors.palette[0]+';">'+
				'<span class="dummies">'+dict['Mujer']+'</span>'+
				'<i class="fa fa-female dummies" aria-hidden="true" style="font-size:'+Math.round(150*dict['Mujer']/max)+'px;"></i>'+
			'</span>'+
			'<span style="color:'+CustomColors.palette[1]+';">'+
				'<i class="fa fa-male dummies" aria-hidden="true" style="font-size:'+Math.round(150*dict['Hombre']/max)+'px;"></i>'+
				'<span class="dummies">'+dict['Hombre']+'</span>'+
			'</span>';
	}
}

class AbstractMap extends AbstractChart{
	#map;
	
	constructor(info,csv,chart,val,time=null,cat=null,geo='geo',scaleAdjust=true,symbol=null){
		super(info,csv,chart);
		if(!this.#map)
			this.#map=new MapChart(csv,chart,val,time,cat,geo,scaleAdjust,symbol);
	}
	
	draw(){}
	
	getOptions(info){return null;}
}

class AbstractDisplay{

	_charts=[];
	
	constructor(_packages,charts){
		if(_packages){
			var _this=this;
			google.charts.load('current', {
			  callback: function () {
				_this.drawCharts();
				$(window).resize(function(){_this.redrawChart();});
			  },
			  packages:_packages
			});
		}
			
		for (var i=0;i<info.length;i++)
			this._charts.push(new charts[i](info[i],csv[i],'chart'+(i+1)));
		
		if(!_packages)
			this.drawCharts();
	}
	
	openTab(but,div='tab1',redraw=false){
		openTabStatic(but,div);
		if(redraw)
			this.redrawChart();
	}

	drawCharts() {
		for (var i=0;i<info.length;i++)
			this._charts[i].draw();
		this.openTab(document.getElementById("firstButton"),'tab1');
	}

	redrawChart() {
		var tabcontents = tab.getElementsByClassName("tabContent");
		for (var i = 0; i < tabcontents.length; i++){
			if(info[i]['tipo']!='Mapa' && tabcontents[i].style.display == "block")
				this._charts[i].draw();
		}
	}
	
	
}
