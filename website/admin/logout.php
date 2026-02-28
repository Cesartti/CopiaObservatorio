<?php
session_start();
session_destroy();
clearstatcache();
header('HTTP/1.1 401 Unauthorized', true, 401);
?>
<html>
  <head>
    <meta http-equiv="refresh" content="0; url='.'" />
  </head>
  <body>
    Cerrando sesión...
  </body>
</html>
