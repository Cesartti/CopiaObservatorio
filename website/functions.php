<?php
	function cleanParam($str){
		$accents=array('á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u');
		$str=strtr(trim($str),$accents);
		return strtolower($str);
	}
	
	function getInfo($path){
		$info=[];
		if(file_exists($path)){
			$file=new SplFileObject($path);
			$aux=[];
			while(!$file->eof())
				array_push($aux,$file->fgets());
			array_walk($aux, function($row) use (&$info) {
				$cut=strpos($row,':');
				if(strlen($row)>0)
					$info[cleanParam(substr($row,0,$cut))] = trim(substr($row,$cut+1));
			});
		}
		return $info;
	}
	
	function displayIntro($indicador){
		echo('<div>
				<h2 class="indicador-title">'.$indicador["titulo"].'</h2><p class="description">'.$indicador["descripcion"].'</p></div>');
		echo('<table class="clasificacion">
				<tr>
					<th class="titulo">Categor&iacute;a</th>
					<th class="titulo">Sub-categor&iacute;a</th>');
		if($indicador["etiquetas"]!='')
			echo('<th class="titulo">Etiquetas</th>');
		
		echo('	</tr>
				<tr>
					<td>'.$indicador["categoria"].'</td>
					<td>'.$indicador["subcategoria"].'</td>');
		if($indicador["etiquetas"]!='')
			echo('<td>'.str_replace(
				'/','<br/>',str_replace('|','<br/>',$indicador["etiquetas"])
				).'</td>');
		echo('</tr></table>');
	}
	
	function displayHead($desc){
		if(strcmp($desc,'')!=0)
			echo('<div class="tabLegend">'.$desc.'</div>');
	}

	function displayFuentes($arr){
		$fuentes='';
		if(array_key_exists('fuentes',$arr))
			$fuentes=$arr['fuentes'];
					
		if(strlen($fuentes)>0){
			echo('<div id="source">');
			if(strcmp(strtolower(substr($fuentes,0,4)),'http')==0)
				echo('<a href="'.$fuentes.'" target="fuentes" title="Fuentes"><button type="button" class="btn btn-ligh btn-fuente">Fuentes <i class="fa fa-link" aria-hidden="true"></i></button></a>');
			else
				echo('<span class="d-block">Fuentes: '. $fuentes .'</span>');
			echo('</div>');
		}
	}

	function displayFoot($id,$arr,$count){
		echo '<div class="d-flex foot-container">';
		displayFuentes($arr);
		echo('<a href="indicador/'.$id.'/'.$count.'.csv" title="Descargar datos"><button type="button" class="btn btn-ligh btn-fuente">Descarga datos <i class="fa fa-download" aria-hidden="true"></i></button></a>');
		echo '</div>';
	}
	
	function displayTabs($indicador){
		echo('<div id="tab">');
		$count=1;
		foreach($indicador['charts'] as $chart){
			$cla='id="firstButton" class="active"';
			if($count>1)
				$cla='class="tabButton"';
			echo('<button '.$cla.' onclick="display.openTab(event.currentTarget,\'tab'.$count.'\',true)">'.$chart['titulo'].'</button>
				<div id="tab'.$count.'" class="tabContent">');
			
			$desc='';
			if(array_key_exists('descripcion',$chart))
				$desc=$chart['descripcion'];
			displayHead($desc);
			
			$tipo='';
			if(array_key_exists('tipo',$chart))
				$tipo=$chart['tipo'];
			
			if(strcmp($tipo,'Mapa')==0)
				$tipo='map';
			else
				$tipo='chart';
			echo('<div id="chart'.$count.'" class="'.$tipo.'"></div>');
			displayFoot($indicador['id'],$chart,$count);
			echo('</div>');
				$count+=1;
		}
		echo('</div>');
	}
?>
