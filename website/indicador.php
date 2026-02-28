<?php include 'functions.php';?>

<!doctype html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preconnect" href="https://fonts.googleapis.com">

	<link rel="icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png" sizes="192x192">
	<link rel="apple-touch-icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-180x180.png">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&family=Quicksand:wght@300;400;600;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="assets/css/main.css">
	<link rel="stylesheet" type="text/css" href="assets/css/indic.css">
	<link rel="stylesheet" href="assets/css/general.css" />
<?php
$title = 'Observatorio';
$error = NULL;
$indicador = [];
$maps = false;
$charts = false;
if (isset($_GET['id']) && !empty($_GET['id'])) {
	$id = $_GET['id'];

	echo '<script type="text/javascript" src="assets/js/colors.js"></script>';
	echo '<script type="text/javascript" src="assets/js/tabs.js"></script>';
	echo '<script type="text/javascript" src="assets/js/general.js"></script>';
	
	$idStart = substr($id, 0, 1);
	$filename = '';
	if (strcmp($idStart, '1') == 0)
		$filename = 'economico';
	else if (strcmp($idStart, '2') == 0)
		$filename = 'social';
	else if (strcmp($idStart, '3') == 0)
		$filename = 'ambiental';
	else if (strcmp($idStart, '4') == 0)
		$filename = 'tecnologia';

	echo '<link rel="stylesheet" type="text/css" href="assets/css/indic-' . $filename . '.css">';
	
	echo '<script type="text/javascript" src="assets/js/' . $filename . '.js"></script>';

	echo '<link rel="stylesheet" href="assets/css/tabs.css"/>';

	if (file_exists('indicador/' . $id . '/indicador.info')) {
		$indicador = getInfo('indicador/' . $id . '/indicador.info');
		$indicador['id'] = $id;
		$title=$title.' - '.$indicador['titulo'];
		if (count($indicador) > 0) {
			$i = 1;
			$indicador['charts'] = [];
			echo '<script type="text/javascript">info=[];csv=[];';
			while (true) {
				if (file_exists('indicador/' . $id . '/' . $i . '.info')) {
					$info = getInfo('indicador/' . $id . '/' . $i . '.info');
					if (count($info) > 0) {
						$tipo = 'None';
						if (array_key_exists('tipo', $info))
							$tipo = $info['tipo'];

						if (strcmp($tipo, 'Mapa') == 0)
							$maps = true;
						else
							$charts = true;
						array_push($indicador['charts'], $info);
						unset($info['titulo']);
						unset($info['descripcion']);
						unset($info['fuentes']);
						$info = json_encode($info);
						echo 'info.push(' . $info . ');';
					} else {
						$error = 'info de gráfico ' . $id . '/' . $i . ' sin datos';
						break;
					}
					if (file_exists('indicador/' . $id . '/' . $i . '.csv')) {
						$csv = array_map('str_getcsv', file('indicador/' . $id . '/' . $i . '.csv'));

						$csv = json_encode($csv, JSON_NUMERIC_CHECK);
						echo 'csv.push(' . str_replace('\u00ac', ',', $csv) . ');';
					} else {
						$error = 'csv ' . $id . '/' . $i . ' no encontrado';
						break;
					}
					$i++;
				} else
					break;
			}
			echo '</script>';
		} else
			$error = 'info de indicador ' . $id . ' sin datos';
	} else
		$error = 'info de indicador ' . $id . ' no encontrado';
} else
	$error = "id de indicador no encontrado";
echo '<title>'.$title.'</title>';
?>
</head>

<?php include  'include/body.php' ?>
	<div id="main-body" class="main-body">
		<section class="section-indicador">
<?php
if (is_null($error)) {
	$idStart = substr($id, 0, 1);
	$link = 'indic-';
	if (strcmp($idStart, '1') == 0)
		$link= $link.'economico';
	else if (strcmp($idStart, '2') == 0)
		$link = $link.'social';
	else if (strcmp($idStart, '3') == 0)
		$link = $link.'ambiental';
	else if (strcmp($idStart, '4') == 0)
		$link = $link.'tecnologia';
	$link = $link.'.php';
				
?>
			
			<section class="section-indicador-top">
				<a href="<?php print($link);?>">
					&larr; Volver al índice
				</a>
			</section>				
			<section class="container-fluid">
<?php
	if (file_exists('indicador/' . $id . '/display.js')) {
		if ($charts)
			include('include/charts.html');
		if ($maps)
			include('include/maps.html');
		displayIntro($indicador);
		displayTabs($indicador);
		displayFuentes($indicador);
		echo ('<script type="text/javascript" src="indicador/' . $id . '/display.js"></script>');
		echo ('<script type="text/javascript">display=new Display();</script>');
	} else
		$error = $id . '/display.js no encontrado';
}
if (!is_null($error))
	echo "<div>" . $error . "</div>";
?>
			</section>
		</section>
	</div>
<?php include  'include/footer.php' ?>