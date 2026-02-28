class MapChart {
	static pres=['','k','M','G','T','P','E','Z','Y'];
	static title=['','Miles','Millones','Miles de millones','Billones','Miles de billones','Trillones','Miles de trillones','Cuatrillones'];
	static legendLevels=50;
	static legendUmbral=.15;
	
	#mainColor;
	#oppositeColor;
	
	#data;
	#map;
	#popup;
	
	#legend;
	#geoJson;
	#geoIndex;
	#valIndex;
	
	#timeIndex;
	#time=null;
	#timeRange;
	
	#catIndex=[];
	#category=[];
	#categoryMax;
	#categoryMin;

	#scaleAdjust;
	#symbol;
	
	bindFunctions(){
		this.style = this.style.bind(this);
		this.onEachFeature = this.onEachFeature.bind(this);
		this.highlightFeature = this.highlightFeature.bind(this);
		this.resetHighlight = this.resetHighlight.bind(this);
		this.zoomToFeature = this.zoomToFeature.bind(this);
	}
	
	decodeRGB(rgb){
		var arr=rgb.substring(1);
		arr=[arr.substring(0,2),arr.substring(2,4),arr.substring(4)];
		for(var i=0;i<arr.length;i++)
			arr[i]=parseInt(arr[i],16);
		return arr;
	}
	
	chargeColors(){
		this.#mainColor=this.decodeRGB(CustomColors.main);
		this.#oppositeColor=this.decodeRGB(CustomColors.opposite);
	}
	
	constructor(data,chart,valColumn,timeColumn=null,catColumn=null,geoColumn='geo',scaleAdjust=true,symbol=null){
		this.chargeColors();
		
		this.bindFunctions();
		this.#data=data;
		//Construye mapa
		this.#map=L.map(chart,{zoomControl: false});
		this.#map.center=function(){this.setView([5.85,-73],8)};
		this.#map.center();
		//Define límites de desplazamiento
		var bounds = L.latLngBounds(L.latLng(4.65, -74.9), L.latLng(7.25, -71.7));
		this.#map.setMaxBounds(bounds);
		
		this.addMap();

		L.control.fullscreen().addTo(this.#map);
		this.addZoomHome();
		
		if(this.#data[0].indexOf(geoColumn)>-1)
			this.#geoIndex=this.#data[0].indexOf(geoColumn);
		else{
			console.log('Columna Geo no encontrada:',geoColumn,'en',this.#data[0]);
			alert('Columna Geo ('+geoColumn+') no encontrada');
		}
		
		if(this.#data[0].indexOf(valColumn)>-1){
			this.#valIndex=this.#data[0].indexOf(valColumn);
			if(symbol=='%'){
				for(var i=1;i<data.length;i++)
					data[i][this.#valIndex]=parseInt(data[i][this.#valIndex]*100);
			}
		}
		else{
			console.log('Columna Valor no encontrada:',valColumn,'en',this.#data[0]);
			alert('Columna Valor ('+valColumn+') no encontrada');
		}
		
		//Agrega infoPopup, time y Legend, carga geoJson de Boyacá
		this.addPopup(catColumn);
		this.addLegend();
		this.#scaleAdjust=scaleAdjust;
		this.#symbol=symbol;
		this.#geoJson=L.geoJson(boyacaData, {style: this.style,onEachFeature: this.onEachFeature}).addTo(this.#map);
		this.addTimeline(timeColumn);		
	}
	
	addMap(){
		//Mapa simple y limpio
		L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}.png',
			{maxZoom: 14, minZoom: 8, attribution: '©OpenStreetMap, ©CartoDB'}).addTo(this.#map);
		L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}.png',
			{maxZoom: 14, minZoom: 8, attribution: '©OpenStreetMap, ©CartoDB'}).addTo(this.#map);
	}
	
	addZoomHome(){
		L.Control.zoomHome = L.Control.extend({
			options: {
				position: 'topleft',
				zoomInText: '+',zoomInTitle: 'Zoom in',
				zoomOutText: '-',zoomOutTitle: 'Zoom out',
				zoomHomeText: '<i class="fa fa-home" style="line-height:1.65;"></i>',zoomHomeTitle: 'Zoom home'
			},

			onAdd: function (map) {
				var controlName = 'gin-control-zoom',
					container = L.DomUtil.create('div', controlName + ' leaflet-bar'),
					options = this.options;

				this._zoomInButton = this._createButton(options.zoomInText, options.zoomInTitle,
				controlName + '-in', container, this._zoomIn);
				this._zoomHomeButton = this._createButton(options.zoomHomeText, options.zoomHomeTitle,
				controlName + '-home', container, this._zoomHome);
				this._zoomOutButton = this._createButton(options.zoomOutText, options.zoomOutTitle,
				controlName + '-out', container, this._zoomOut);

				this._updateDisabled();
				map.on('zoomend zoomlevelschange', this._updateDisabled, this);

				return container;
			},

			onRemove: function (map) {
				map.off('zoomend zoomlevelschange', this._updateDisabled, this);
			},

			_zoomIn: function (e) {
				this._map.zoomIn(e.shiftKey ? 3 : 1);
			},

			_zoomOut: function (e) {
				this._map.zoomOut(e.shiftKey ? 3 : 1);
			},

			_zoomHome: function (e) {
				this._map.center();
			},

			_createButton: function (html, title, className, container, fn) {
				var link = L.DomUtil.create('a', className, container);
				link.innerHTML = html;
				link.href = '#';
				link.title = title;

				L.DomEvent.on(link, 'mousedown dblclick', L.DomEvent.stopPropagation)
					.on(link, 'click', L.DomEvent.stop)
					.on(link, 'click', fn, this)
					.on(link, 'click', this._refocusOnMap, this);

				return link;
			},

			_updateDisabled: function () {
				var map = this._map,
					className = 'leaflet-disabled';

				L.DomUtil.removeClass(this._zoomInButton, className);
				L.DomUtil.removeClass(this._zoomOutButton, className);

				if (map._zoom === map.getMinZoom()) {
					L.DomUtil.addClass(this._zoomOutButton, className);
				}
				if (map._zoom === map.getMaxZoom()) {
					L.DomUtil.addClass(this._zoomInButton, className);
				}
			}
		});
		new L.Control.zoomHome().addTo(this.#map);
	}
	
	addTimeline(timeColumn){
		if(timeColumn!=null){
			if(this.#data[0].indexOf(timeColumn)>-1){
				this.#timeIndex=this.#data[0].indexOf(timeColumn);
				var _super=this;
				var timeline = L.control({position:'bottomleft'});
				timeline.onAdd = function (map) {
					var div = L.DomUtil.create('div', 'popup');
					var code='<input type="range" class="slider"><span></span>';
					div.innerHTML=code;
					_super.#timeRange=div.getElementsByTagName("input")[0];
					var span=div.getElementsByTagName("span")[0];
					_super.#timeRange.onchange=function(){
						var v=parseInt(this.value)
						if(this.last==null)
							this.last=this.min;
						var inc=v-parseInt(this.last);
						if(inc!=0){
							inc=inc/Math.abs(inc);
							while(!this.has.includes(v))
								v+=inc;
						}
						this.last=v;
						span.innerHTML=""+v;
						_super.setTime(v);
						this.value=v;
					};
					return div;
				};
				timeline.addTo(this.#map);
				this.resetTime();
			}else{
				console.log('Columna de tiempo no encontrada:',timeColumn,'en',this.#data[0]);
				alert('Columna de tiempo ('+timeColumn+') no encontrada');
			}
		}
	}
	
	resetTime(){
		if(this.#timeIndex!=null){
			var has=[];
			for(var i=1;i<this.#data.length;i++){
				if(this.#catIndex.length==0 || this.#data[i][this.#catIndex[0]]==this.#category[0]){
					var v=this.#data[i][this.#timeIndex];
					if(!has.includes(v))
						has.push(v);
				}
			}
			var min=Math.min.apply(Math,has);
			var max=Math.max.apply(Math,has);
			this.#time=min;
			this.#timeRange.has=has.toString();
			this.#timeRange.min=min;
			this.#timeRange.max=max;
			//this.#timeRange.value=min;
			this.#timeRange.last=null;
			this.#timeRange.onchange();
		}
	}
	
	setMinMax(){
		this.#categoryMin=Infinity;
		this.#categoryMax=-Infinity;
		for(var i=1;i<this.#data.length;i++){
			if(this.#time==null || this.#data[i][this.#timeIndex]==this.#time){
				var flag=true;
				for(var n=0;n<this.#category.length;n++){
					if(this.#data[i][this.#catIndex[n]]!=this.#category[n]){
						flag=false;
						break;
					}
				}
				if(this.#category.length==0 || flag){
					var v=this.#data[i][this.#valIndex];
					if(v>this.#categoryMax)
						this.#categoryMax=v;
					if(v<this.#categoryMin)
						this.#categoryMin=v;
				}
			}
		}
	}
	
	setTime(time){
		this.#time=time;
		this.setMinMax();
		this.#legend.update();
		this.#geoJson.resetStyle();
	}
	
	addPopup(catColumn){
		var categories=null;
		if(catColumn!=null){
			if(typeof catColumn=='string')
				catColumn=[catColumn]
			categories=[];
			for(var n=0;n<catColumn.length;n++){
				if(this.#data[0].indexOf(catColumn[n])>-1){
					this.#catIndex[n]=this.#data[0].indexOf(catColumn[n]);
					categories[n]=[];
					for(var i=1;i<this.#data.length;i++){
						var category=this.#data[i][this.#catIndex[n]];
						if(this.#category.length==n)
							this.#category[n]=category;
						if(categories[n].indexOf(this.#data[i][this.#catIndex[n]])<0)
							categories[n].push(category);
					}
				}else{
					console.log('Columna de categoría no encontrada:',catColumn[n],'en',this.#data[0]);
					alert('Columna de categoría ('+catColumn[n]+') no encontrada');
				}
			}
		}else
			categories=this.#data[0][this.#valIndex];
		this.setMinMax();
		var _super=this;
		this.#popup = L.control({position:'topright'});
		this.#popup.onAdd = function (map) {
			this.div = L.DomUtil.create('div', 'popup');
			var code='';
			if(typeof categories=='string')
				code+='<h4>'+_super.#data[0][_super.#valIndex]+'</h4>';
			else{
				for(var n=0;n<categories.length;n++){
					code+=_super.#data[0][_super.#catIndex[n]]+'&nbsp';
					code+='<select id="category" class="dropdown">';
					for (var i=0;i<categories[n].length;i++){
						var disabled='';
						code+='<option value="'+categories[n][i]+'"'+disabled+'>'+categories[n][i]+'</option>';
					}
					code+='</select><br/>';
				}
			}
			code+='<div class="message"></div>';
			this.div.innerHTML=code;
			if(typeof categories!='string'){
				for(var n=0;n<categories.length;n++){
					var selector=this.div.getElementsByTagName("select")[n];
					selector.n=n;
					selector.onchange=function(e){
						_super.setCategory(e.target.n,e.target.value);
					};
				}
			}
			this.update();
			return this.div;
		};
		
		this.#popup.update = function (feature,v) {
			var code ='';
			if(feature){
				code+='<div class="municipio">' + feature.properties.name + '</div>';
				if(v!=null){
					code+=v;
					if(_super.#symbol!=null)
						code+=" "+_super.#symbol;
				}else
					code+=' Sin valor';
			}else
				code+='Ubica el mouse sobre el municipio';
			var aux=this.div.getElementsByTagName("div")[0];
			aux.innerHTML = code;
		};
		
		this.#popup.addTo(this.#map);
	}
	
	setCategory(n,category){
		this.#category[n]=category;
		this.setMinMax();
		this.resetTime();
		this.#legend.update();
		this.#geoJson.resetStyle();
	}
	
	addLegend(){
		this.#legend = L.control({position: 'bottomright'});
		var _this=this;
		
		this.#legend.onAdd = function () {
			this.div = L.DomUtil.create('div', 'popup legend');
			this.update();
			return this.div;
		};
		
		this.#legend.update = function () {
			var max=_this.#categoryMax;
			var min=_this.#categoryMin;
			
			var pre='';
			var title='';
			if(_this.#scaleAdjust){
				//Búsqueda de límite cercano
				var cx=0;
				var dif=min-Math.floor(min/(10**(cx+1)))*(10**(cx+1));
				while(Math.floor(min/(10**cx))>0 && dif/(max-min)<MapChart.legendUmbral){
					cx+=1;
					dif=min-Math.floor(min/(10**(cx+1)))*(10**(cx+1));
				}
				min=Math.floor(min/(10**cx))*(10**cx);

				var cx=0;
				var dif=Math.ceil(max/(10**(cx+1)))*(10**(cx+1))-max;
				while(Math.ceil(max/(10**(cx+1)))>0 && dif/(max-min)<MapChart.legendUmbral){
					cx+=1;
					dif=Math.ceil(max/(10**(cx+1)))*(10**(cx+1))-max;
				}
				max=Math.ceil(max/(10**cx))*(10**cx);
				
				//Ajuste de escala
				var cx=0;
				while(Math.ceil(max/(10**((cx+1)*3)))-Math.floor(min/(10**((cx+1)*3)))>5){
					cx+=1;
				}
				pre=MapChart.pres[cx];
				title=MapChart.title[cx];
				cx=10**(cx*3);
				min=Math.floor(min/cx)*cx;
				max=Math.ceil(max/cx)*cx;
			}
			
			var dx=(max-min)/(MapChart.legendLevels-1);
			var labels = [];
			for (var i = 0; i < MapChart.legendLevels; i++) {
				var from = max-i*dx;
				if(_this.#scaleAdjust)
					from=Math.round(from);
				var post='';
				if(i==0 || i==MapChart.legendLevels-1){
					if(i==MapChart.legendLevels-1)
						from=min;
					if(from/cx>=1)
						from=Math.round(from/cx)+pre;						
					post=from;
				}
				var color=_this.getColor(from);
				var str='<i style="background:' + color + '"></i>'+post;
				labels.push(str);
			}
			this.div.innerHTML = labels.join('<br>');
			if(pre!='')
				this.div.title=pre+'='+title;
		};
		
		this.#legend.addTo(this.#map);
	}
	
	pad(str, size) {
		while (str.length < size) str = "0" + str;
		return str;
	}
	
	getColor(d) {
		var max=this.#categoryMax;
		var min=this.#categoryMin;
		
		var r=this.#oppositeColor[0];
		var g=this.#oppositeColor[1];
		var b=this.#oppositeColor[2];
		var dr=(this.#mainColor[0]-r)/(max-min);
		var dg=(this.#mainColor[1]-g)/(max-min);
		var db=(this.#mainColor[2]-b)/(max-min);
		d-=min;
		r+=Math.floor(d*dr);
		g+=Math.floor(d*dg);
		b+=Math.floor(d*db);
		r=this.pad(r.toString(16),2);
		g=this.pad(g.toString(16),2);
		b=this.pad(b.toString(16),2);
		var res='#'+r+g+b;
		return res;
	}
	
	getValue(id){
		var res=null;
		for(var i=0;i<this.#data.length;i++){
			if(this.#data[i][this.#geoIndex]==id){
				if(this.#time==null || this.#data[i][this.#timeIndex]==this.#time){
					var flag=true;
					for(var n=0;n<this.#category.length;n++){
						if(this.#data[i][this.#catIndex[n]]!=this.#category[n]){
							flag=false;
							break;
						}
					}
					if(this.#category.length==0 || flag){
						if(this.#data[i][this.#valIndex]==='')
							this.#data[i][this.#valIndex]=null;
						res=this.#data[i][this.#valIndex];
						break;
					}
				}
			}
		}
		return res;
	}
	
	style(feature) {
		var v=this.getValue(feature.properties.id);
		return {
			weight: 1,
			opacity: v==null?.1:.5,
			color: CustomColors.midOpposite,
			fillOpacity: v==null?.3:.7,
			fillColor: v==null?'#eeeeee':this.getColor(v)
		};
	}
	
	highlightFeature(e) {
		var layer = e.target;
		var v=this.getValue(layer.feature.properties.id);
		layer.setStyle({
			weight: v==null?2:4,
			opacity: v==null?.3:.8,
			color: CustomColors.hardOpposite,
			fillOpacity: v==null?.4:.8
		});

		if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge)
			layer.bringToFront();

		this.#popup.update(layer.feature,v);
	}
	
	onEachFeature(feature, layer) {
		layer.on({
			mouseover: this.highlightFeature,
			mouseout: this.resetHighlight,
			click: this.zoomToFeature
		});
	}
	
	resetHighlight(e) {
		this.#geoJson.resetStyle(e.target);
		this.#popup.update();
	}

	zoomToFeature(e) {
		this.#map.fitBounds(e.target.getBounds());
	}
}