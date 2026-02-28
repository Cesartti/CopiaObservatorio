<html>
	<head>
		<title>Actualización de fuentes</title>
	</head>
	<body>
		<?php
			session_start();
			if(!isset($_SESSION['claseind']))
				$_SESSION['claseind']=0;
			if(!isset($_SESSION['indicador']))
				$_SESSION['indicador']='';
			if(isset($_SESSION['message']) && $_SESSION['message']){
				$str='%s';
				if($_SESSION['type']==1)
					$str='<span style="color:#f00;">%s</span>';
				printf($str,$_SESSION['message']);
			}
		?>
		<form method="POST" action="upload.php" enctype="multipart/form-data">
			<select name="claseind">			
				<option value="0">Observatorio</option>
				<option value="1" <?php echo $_SESSION['claseind']==1?'selected="selected"':'';?>>1 - Económico</option>
				<option value="2" <?php echo $_SESSION['claseind']==2?'selected="selected"':'';?>>2 - Social</option>
				<option value="3" <?php echo $_SESSION['claseind']==3?'selected="selected"':'';?>>3 - Ambiental</option>
				<option value="4" <?php echo $_SESSION['claseind']==4?'selected="selected"':'';?>>4 - Tecnología</option>
			</select>
			<input name="indicador" type="text" min="1" maxlength="3" id="indicador" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" value="<?php echo $_SESSION['indicador'];?>"/>
			<br/>
			<input type="file" name="uploadedFile"/>
			<br/>
			<input type="submit" name="uploadBtn" value="Enviar"/>
	    </form>
		<a href="logout.php">Cerrar</a>
	</body>
</html>
