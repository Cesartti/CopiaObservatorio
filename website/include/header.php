<?php
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$current_url = trim(end($components), ".php");
$current_css = '';
switch ($current_url) {
    case '':
        $current_css = 'home';
        break;
    case 'index':
        $current_css = 'home';
        break;
    case 'about':
        $current_css = 'about';
        break;
    case 'indic-economico':
        $current_css = 'indic-economico';
        break;
    case 'indic-social':
        $current_css = 'indic-social';
        break;
    case 'indic-ambiental':
        $current_css = 'indic-ambiental';
        break;
    case 'indic-tecnologia':
        $current_css = 'indic-tecnologia';
        break;
    case 'indic-genero':
        $current_css = 'indic-genero';
        break;
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Observatorio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS (única referencia correcta a Bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&family=Quicksand:wght@300;400;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <!-- Favicon -->
    <link rel="icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png" sizes="192x192">
    <link rel="apple-touch-icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-180x180.png">

    <?php
    if ($current_url === '' || $current_url === 'index') {
        echo '<link rel="stylesheet" href="assets/css/owl.carousel.css" />';
        echo '<link rel="stylesheet" href="assets/css/owl.theme.default.css" />';
    }
    ?>

    <!-- CSS personalizados -->
     <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="assets/css/indic.css">
    <link rel="stylesheet" type="text/css" href="assets/css/<?php echo $current_css ?>.css">

    <!-- Bootstrap 5 JS (al final de <head> si es absolutamente necesario aquí) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

        <!-- En HEAD -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

        <!-- Al final del body -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
        <script src="assets/js/main.js"></script>


</head>

<body>
    <?php include 'include/body.php' ?>
