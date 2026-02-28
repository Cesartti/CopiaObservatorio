class Chart1 extends AbstractMap{
	constructor(info,csv,chart){
		super(info,csv,chart,'Número','Año','Género');
	}
}

class Chart2 extends AbstractChart{
    
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

class Chart3 extends AbstractChart{
    
    format(){
        var yearFormatter = new google.visualization.NumberFormat({pattern: Patterns.year});
        yearFormatter.format(this._data, 0);
        var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
        for(var i=1;i<this._data.bf.length;i++)
            percentFormatter.format(this._data, i);
    }
    
    getOptions(info){
        return {
            hAxis: {title: info['horizontal'],format: Patterns.year},
            vAxis: {title: info['vertical'], format: Patterns.percent}
        };
    }
    
    getType(div){
        return new google.visualization.LineChart(div);
    }
}

class Chart4 extends DummyChart{}

class Chart5 extends AbstractChart{
     format(){
          var percentFormatter = new google.visualization.NumberFormat({pattern: Patterns.percent});
          percentFormatter.format(this._data, 2);
          var currencyFormatter = new google.visualization.NumberFormat({pattern: Patterns.currency});
          currencyFormatter.format(this._data, 3);
     }
     
     getOptions(info){
          return {
               colors: [AbstractColors.oppositeEcono,AbstractColors.mainEcono],
               backgroundColor: AbstractColors.ocean,
               datalessRegionColor: '#dfdfdf',
               tooltip: {textStyle:{fontSize: 16}}
          };
     }

     getType(div){
          return new google.visualization.GeoChart(div);
     }
}

class Chart6 extends AbstractChart{

     prepareView(){
          this._data.insertColumn(1,'string', 'Global');  
          for(var i=0;i<this._data.getNumberOfRows();i++)
               this._data.setValue(i,1,'Global');
          this._data.insertRows(0, [['Global', null, 0, 0]]);
          return null;
     }
     
     getOptions(info){
          let dollarUS = Intl.NumberFormat("en-US", {
               style: "currency",
               currency: "USD",
               maximumFractionDigits: 0
          });
          var _this=this;
          function showStaticTooltip(row, size, value) {
               var res='';
               if(row>0)
                    res='<div class="popup">'+
                         _this._data.getColumnLabel(0)+': '+
                         _this._data.getValue(row,0)+
                         '<br/>'+_this._data.getColumnLabel(2)+': '+
                         parseFloat(_this._data.getValue(row,2)*100).toFixed(0)+"%"+
                         '<br/>'+_this._data.getColumnLabel(3)+': '+
                         dollarUS.format(_this._data.getValue(row,3))+'</div>';
               return res;
          }
          
          //Color dinámico de label necesario para rectángulos oscuros
          var observer=new MutationObserver(textColors);
          var container=document.getElementById(this._chart);
          observer.observe(container, {
               childList: true,
               subtree: true
          });
          
          function textColors() {
               Array.prototype.forEach.call(container.getElementsByTagName('rect'), function(rect) {
                    var textElements = rect.parentNode.getElementsByTagName('text');
                    if (textElements.length > 0) {
                         var fill=rect.getAttribute('fill').substring(1);
                         fill=(parseInt(fill.substring(0,2),16)+parseInt(fill.substring(2,4),16)+parseInt(fill.substring(4),16))/3;
                         var color=CustomColors.main;
                         if(fill<190)
                              color=CustomColors.opposite;
                         textElements[0].setAttribute('fill',color);
                    }
               });
          }
          return {
               maxDepth: 1,
               maxColor: CustomColors.main,
               minColor: CustomColors.opposite,
               fontColor: 'black',
               fontSize: 18,
               showScale: true,
               headerHeight: 0,
               generateTooltip: showStaticTooltip,
               eventsConfig: {
                    drilldown: []
               }
          };
     }

     getType(div){
          var treemap=new google.visualization.TreeMap(div);
          google.visualization.events.addListener(treemap, 'select', function () {
               console.log('SELECT!!!');
               treemap.setSelection([]);
          });
          return treemap;
     }
}

class Display extends AbstractDisplay{
	constructor(){
		super(['corechart','geochart','treemap'],[Chart1,Chart2,Chart3,Chart4,Chart5,Chart6]);
	}
}
