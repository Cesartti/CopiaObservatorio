<?php
// phpinfo() expone configuración sensible del servidor: restringir solo a administradores autenticados.
require_once __DIR__ . '/auth/bootstrap.php';
auth_require_login();
phpinfo();
?>
