<?php
session_start();
$_SESSION['claseind']=$_POST['claseind'];
$_SESSION['indicador']=$_POST['indicador'];
$_SESSION['type']=0;
if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Enviar') {
	if( $_POST['claseind']==0){
        $_SESSION['message'] = "Error no ha seleccionado el observatorio";
		$_SESSION['type']=1;
	}elseif ((strlen($_POST['indicador'])==0) || strlen($_POST['indicador'])==''){
        $_SESSION['message'] = "debe digitar un código del indicador (máximo 3 dígitos)";
		$_SESSION['type']=1;
	}elseif (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
		$fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
		$fileName = $_FILES['uploadedFile']['name'];
		$fileSize = $_FILES['uploadedFile']['size'];
		$fileType = $_FILES['uploadedFile']['type'];
		$clase_ind  = $_POST['claseind'];
		$indicador = str_pad($_POST['indicador'], 3, "0", STR_PAD_LEFT);
		$fileNew  = $clase_ind.$indicador;
		$fileNameCmps = explode(".", $fileName);
		$fileExtension = strtolower(end($fileNameCmps));
		$newFileName = $fileNew.'.'. $fileExtension;
		$allowedfileExtensions = array('xlsx');
		if (in_array($fileExtension, $allowedfileExtensions)) {
			$uploadFileDir = '/root/flow/datalake/';
			$dest_path = $uploadFileDir . $newFileName;
			if(move_uploaded_file($fileTmpPath, $dest_path))
				$_SESSION['message']="Archivo cargado con éxito para el indicador No.... ".$fileNew;	
			else{
				$_SESSION['message'] = 'Error carga en indicador No. '.$fileNew.' error al mover el archivo al directorio de carga ('.$fileTmpPath.','.$dest_path.').';
				$_SESSION['type']=1;
			}
		}else{
			$_SESSION['message'] = "Error en la extensión del archivo, debe ser xlsx ";
			$_SESSION['type']=1;
		}
	}else{
		$_SESSION['message'] = "Error no ha seleccionado el archivo";
		$_SESSION['type']=1;
	}
}
header("Location: index.php");
?>
