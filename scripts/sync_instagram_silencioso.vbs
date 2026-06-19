' Ejecuta la sincronización de Instagram sin abrir ventana de consola.
' Usado por la tarea programada "ObservatorioBoyaca_SyncInstagram".
Set sh = CreateObject("WScript.Shell")
sh.Run """C:\xampp\php\php.exe"" ""C:\xampp\htdocs\Observatorio2026\scripts\sync_instagram.php""", 0, False
