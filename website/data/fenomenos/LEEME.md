# Datos del estado del agua

Archivos que alimentan la subpestaña «Estado del agua» del Observatorio
Ambiental. Todos se generan con scripts del repositorio: ninguno se edita a
mano.

| Archivo | Qué contiene | Cómo se regenera | Cada cuánto |
|---|---|---|---|
| `desabastecimiento_boyaca.json` | Riesgo de desabastecimiento por municipio (IDEAM, estudio 1998-2021) | `python scripts/gen_desabastecimiento_boyaca.py` | Solo si el IDEAM publica un estudio nuevo |
| `vhi_boyaca.json` | VHI de la semana más reciente, por municipio (NOAA) | `python scripts/gen_vhi_boyaca.py` | Semanal |
| `historico_agua.json` | Serie semanal del VHI y serie diaria del embalse | `python scripts/gen_historico_agua.py` | Semanal (es incremental: solo pide lo que falta) |
| `provincias_boyaca.json` | Municipio → provincia, para los filtros | `python scripts/gen_provincias_boyaca.py` | Solo si cambia la división provincial |
| `certs/ideam_ca_bundle.pem` | Certificado intermedio que el IDEAM omite en su cadena TLS | Manual | Vence el 21/03/2036 |

El resto —embalse del día, nivel de los ríos, caudal— se consulta en vivo al
visor FEWS del IDEAM y se guarda en `cache/`, que no se versiona.

## Nota sobre el histórico

`gen_historico_agua.py` no descarga los mosaicos globales completos de la NOAA
(32 MB por semana): pide por rango de bytes solo las filas que cubren Boyacá,
unos 570 KB. Por eso reconstruir dos años cuesta minutos y no horas. La lógica
está en `scripts/vhp_remoto.py`.

Para actualizar todo de una vez:

    python scripts/gen_vhi_boyaca.py
    python scripts/gen_historico_agua.py

y luego confirmar los cambios en git, que es lo que los publica.
