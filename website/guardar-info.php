<?php
session_start();

$_SESSION["profesion"] = $_POST["profesion"] ?? null;
$_SESSION["edad"] = $_POST["edad"] ?? null;
$_SESSION["otro"] = $_POST["otro"] ?? null;

// Evita mostrar nuevamente el modal
echo "<script>localStorage.setItem('infoUsuarioRegistrada', '1');</script>";

// Volver a la página anterior
echo "<script>history.back();</script>";
?>
