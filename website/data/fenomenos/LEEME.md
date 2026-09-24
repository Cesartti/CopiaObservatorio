# Datos del estado del agua

Archivos que alimentan la subpestaña «Estado del agua» del Observatorio
Ambiental. Todos se generan con scripts del repositorio: ninguno se edita a
mano.

| Archivo | Qué contiene | Cómo se regenera | Cada cuánto |
|---|---|---|---|
| `desabastecimiento_boyaca.json` | Riesgo de desabastecimiento por municipio (IDEAM, estudio 1998-2021) | `python scripts/gen_desabastecimiento_boyaca.py` | Solo si el IDEAM publica un estudio nuevo |
| `vhi_boyaca.json` | VHI de la semana más reciente, por municipio (NOAA) | `python scripts/gen_vhi_boyaca.py` | Semanal |
| `historico_agua.json` | Serie semanal del VHI y serie diaria del embalse | `python scripts/gen_historico_agua.py` | Semanal (es incremental: solo pide lo que falta) |
| `historico_calor.json` | Focos de calor diarios por municipio (NASA FIRMS) | `python scripts/gen_historico_calor.py` | Semanal (incremental) |
| `provincias_boyaca.json` | Municipio → provincia, para los filtros | `python scripts/gen_provincias_boyaca.py` | Solo si cambia la división provincial |
| `certs/ideam_ca_bundle.pem` | Certificado intermedio que el IDEAM omite en su cadena TLS | Manual | Vence el 21/03/2036 |

El resto —embalse del día, nivel de los ríos, caudal— se consulta en vivo al
visor FEWS del IDEAM y se guarda en `cache/`, que no se versiona.

## Nota sobre el histórico

`gen_historico_agua.py` no descarga los mosaicos globales completos de la NOAA
(32 MB por semana): pide por rango de bytes solo las filas que cubren Boyacá,
unos 570 KB. Por eso reconstruir dos años cuesta minutos y no horas. La lógica
está en `scripts/vhp_remoto.py`.

## Nota sobre los focos de calor

FIRMS acepta un rango de días por petición, pero **con 7 o más devuelve vacío
sin avisar**; con 5 responde bien, así que `gen_historico_calor.py` recorre en
tramos de cinco días. Usa el archivo procesado de VIIRS (disponible desde 2012)
para las fechas viejas y la fuente en tiempo casi real para las recientes.

La clave de FIRMS no está en el repositorio: el script la lee de la variable de
entorno `OBS_FIRMS_MAP_KEY` o de `website/config/fenomenos.local.php`. En el
sitio se configura desde el CMS, en Fenómenos → Configuración.

## Para actualizar todo de una vez

    python scripts/gen_vhi_boyaca.py
    python scripts/gen_historico_agua.py
    python scripts/gen_historico_calor.py

y luego confirmar los cambios en git, que es lo que los publica.
