' Inicia el asistente de chat (Streamlit) sin ventana de consola.
' Usado por la tarea programada "ObservatorioBoyaca_Asistente" (al iniciar sesión)
' y se puede ejecutar a mano con doble clic.
Set sh = CreateObject("WScript.Shell")
sh.CurrentDirectory = "C:\xampp\htdocs\Observatorio2026\AsistenteOllama"
sh.Run """C:\xampp\htdocs\Observatorio2026\app_asistente\.venv\Scripts\python.exe"" -m streamlit run app.py --server.address localhost --server.port 8501 --server.headless true", 0, False
