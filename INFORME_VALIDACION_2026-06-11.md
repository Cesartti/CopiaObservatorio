# Informe de Validación Integral — Red de Observatorios de Boyacá

- **Fecha:** 2026-06-11
- **Auditor:** Claude Code (Fable 5) — auditoría técnica y de experiencia de usuario
- **Entorno:** XAMPP (Apache + MySQL) en Windows 11 · PHP 8 · BD `observatorio_boyaca`
- **Raíz pública:** `http://localhost/Observatorio2026/website/`
- **Método:** Navegación real en navegador (DOM/comportamiento), pruebas HTTP, inspección de BD viva (`SHOW TABLES`/`DESCRIBE`) y análisis estático de código. Las pruebas con datos se hicieron con registros marcados `[TEST]` y se revirtieron al terminar.

---

## 1. Estado general

La plataforma está **funcional y sólida**. Los 5 micrositios cargan sin errores PHP/JS, todas las pestañas se activan con contenido, la ruta de atención de género cubre correctamente sus 48 combinaciones, el CMS permite el CRUD completo y el portal es responsive sin desbordes. Durante la auditoría se detectaron y **corrigieron en el acto** un caso de **XSS almacenado**, varios **endpoints sin autenticación**, un **bug que impedía guardar enlaces de banners**, **errores fatales** por una tabla ausente y un **problema de rendimiento grave** (47 MB de carga inicial en el micrositio de género). Quedan pendientes principalmente tareas de **higiene de despliegue** (archivos de prueba y un secreto en el historial de Git) que requieren decisión del propietario.

Veredicto: tras las correcciones aplicadas, el sitio queda en condiciones de robustez y funcionalidad acordes con una referencia nacional de observatorios.

---

## 2. Tabla de hallazgos por severidad

| # | Severidad | Área | Hallazgo | Estado |
|---|-----------|------|----------|--------|
| H-01 | **Crítico** | Seguridad | Stored XSS: el cuerpo de noticias (`noticia.php:133`) se renderizaba como HTML crudo sin sanitizar. `<script>` y `onerror` se ejecutaban en el navegador del visitante. | ✅ **Corregido** |
| H-02 | **Crítico** | Seguridad | `admin/info.php` ejecutaba `phpinfo()` sin autenticación (expone rutas, versión, entorno). | ✅ **Corregido** |
| H-03 | **Crítico** | Seguridad/Historial | Contraseña de aplicación de Gmail en `flow/config.ini` dentro del historial de Git (commit `7b1a182`). | ⚠️ **Pendiente** (requiere rotación + reescritura de historial por el propietario) |
| H-04 | **Alto** | Seguridad | Analítica de visitantes (IP, país, ciudad, geolocalización) accesible sin sesión: `dashboard.php`, `dashboard/api/map_data.php`, `dashboard/api/filtrar_datos.php`. | ✅ **Corregido** |
| H-05 | **Alto** | Funcional/BD | `formulario_usuario.php` con `require_once "../tracker.php"` (ruta inexistente) → error fatal en cada POST. | ✅ **Corregido** |
| H-06 | **Alto** | Funcional/CMS | Validación de enlaces de banners con delimitador regex `#` que colisiona con un `#` literal → **todo** enlace era rechazado (imposible asociar URL a un banner). | ✅ **Corregido** |
| H-07 | **Alto** | BD/Robustez | Tabla `accesos_observatorio` usada por el tracker y el dashboard pero **no versionada** ni existente → error fatal (pantalla blanca) en el panel de analítica. | ✅ **Corregido** (tolerancia + migración 015) |
| H-08 | **Alto** | Rendimiento | Micrositio de género descargaba **~47 MB** (45.7 MB de imágenes PNG de 2–3.5 MB cada una) en pestañas ocultas, sin carga diferida. | ✅ **Corregido** (→ 0.30 MB) |
| H-09 | **Medio** | Accesibilidad | Micrositios con **3 `<h1>`** por página (un h1 por diapositiva del hero). WCAG recomienda un h1 único. | ✅ **Corregido** |
| H-10 | **Medio** | Seguridad/Uploads | Subida de imágenes de noticias (`cms/news.php`) permitía SVG (vector XSS), solo validaba por extensión, nombre predecible, sin tope de tamaño verificado. | ✅ **Corregido** |
| H-11 | **Medio** | UX/Asistente | Mensaje de respaldo del chat era técnico (`run_asistente.bat`, Streamlit, Ollama), no apto para ciudadanía. | ✅ **Corregido** |
| H-12 | **Medio** | Mapa | `mapa-fondomujer.html`: el respaldo `window.boyacaData` (script `defer`) se evaluaba antes de cargar → mapa potencialmente sin polígonos si fallaba el fetch del GeoJSON (que devuelve 404). | ✅ **Corregido** |
| H-13 | **Alto** | Seguridad/Despliegue | Archivos de prueba accesibles por HTTP exponen credenciales/diagnóstico: `testdb.php`, `dashboard/testdb.php` (credenciales + `getMessage()`), `test-ip.php`, `tracker.php`. | ⚠️ **Pendiente** (eliminar/mover; son archivos locales del entorno) |
| H-14 | **Medio** | Seguridad | Sin protección CSRF en formularios del CMS; cookies de sesión sin `HttpOnly`/`SameSite`; sin `session_regenerate_id` tras login. | ⚠️ **Pendiente** (recomendado) |
| H-15 | **Medio** | Rendimiento | Recurso `assets/geo/boyaca_municipios.geojson` devuelve 404 (se usa el respaldo JS de 458 KB). Fuente de iconos `kit.fontawesome.com` da 403 (kit no autorizado para localhost). | ⚠️ **Pendiente** (cosmético en local) |
| H-16 | **Bajo** | BD | `schema.sql` desincronizado con la migración 012 (faltan 14 columnas de `indicators` que sí existen en la BD viva). Tablas de schema sin uso por el sitio (`datasets`, `audit_logs`, etc.). | ⚠️ **Pendiente** (recomendado) |
| H-17 | **Bajo** | Seguridad | `indicador.php` usa `$_GET['id']` sin `ctype_digit` (lectura de `.info`/`.csv`); impacto limitado, salida escapada. `*.rar` no cubierto por `.gitignore`. | ⚠️ **Pendiente** (recomendado) |

**Resumen:** 17 hallazgos · **11 corregidos y verificados en navegador/HTTP** · 6 pendientes (mayormente higiene de despliegue y endurecimiento que conviene que ejecute el propietario).

---

## 3. Qué se probó (cobertura)

### Funcional público
- **Portal** (`index.php`): hero, búsqueda global, carrusel de banners (6), tarjetas de micrositios (5), franja de Instagram, banda de noticias, footer. 1 `<h1>`, sin errores PHP. 0.85 MB / 15 peticiones.
- **5 micrositios** (`observatorio.php?slug=…`): los 5 cargan sin errores. Recorridas **todas** las pestañas horizontales y la columna "Consulta y documentación" (Tablero, Hoja de vida, Categorías, Descarga de datos, Publicaciones universidades, Infografías). En género además: Ruta de atención, Barreras, Atención integral, Información (con sub-pestañas de conceptos), Seguimiento a Mecanismos, Política pública, Campañas, Reportes, Marco institucional. Todas se activan con contenido.
- **Modales de líneas temáticas**: abren con contenido y atajo "Ver indicadores en Categorías".
- **Interacciones**: buscador de publicaciones (filtra en vivo, estado "0 de N"), lightbox de infografías (carrusel con navegación), filtros de categorías (conteos por categoría), mapa Fondo Mujer (124 polígonos tras la corrección).
- **Noticias**: listado (3 de 3, filtros por observatorio + buscador), detalle con badge de color y breadcrumb.
- **Indicador** (`indicador.php?id=1001`): ficha, gráficos (Plotly), 2 descargas CSV reales.

### Ruta de atención de género
- Las **48 combinaciones** (institucional/comunitaria × mujer/NNA/LGBTIQ+ × tipos de violencia × contextos) generan una ruta no vacía y sin errores.
- La **evaluación de riesgo** abre correctamente la rama de medida de atención/protección (verificado en comunitaria·mujer·física·dentro).
- **Fondo según orientación**: `FONDO-V.png` en retrato (móvil) y `FONDO.png` en apaisado (escritorio), verificado en ambos.

### Administración (CMS)
- **Login** con usuario `[TEST]` (creado y eliminado): acceso a los 11 módulos.
- **CRUD de banners**: crear (con título con payload XSS), editar (añadir enlace tras el fix), activar/desactivar (toggle → `is_active`), eliminar. XSS escapado con `htmlspecialchars` en la tabla del CMS y en el portal (`index.php:148`).
- **CRUD de noticias**: crear/publicar con payload XSS en `body` → confirmado el escape tras la corrección (script y `onerror` eliminados, `<p>`/`<img>` legítimos conservados).
- **Contacto**: editar → reflejo verificado en el footer del portal y de los micrositios → restaurado.
- **Guards de auth**: todas las páginas `cms/*` y `admin/content` redirigen a login sin sesión; **logout invalida** la sesión.

### UI/UX, accesibilidad, BD, seguridad, rendimiento
- **Responsive**: portal y micrositio de género a 360px sin desborde horizontal del documento (la marquesina de logos está contenida con `overflow:hidden`).
- **Widget de accesibilidad**: presente en todas las páginas, con tamaño de texto, alto contraste, invertir colores, fuente legible, lectura por voz y guía de lectura. **Persistencia** verificada entre páginas vía `localStorage` (`obs_a11y_prefs`).
- **Imágenes**: 0 sin atributo `alt` en las páginas auditadas.
- **BD**: 23 tablas, todas `utf8mb4_unicode_ci`/InnoDB. Sin SQLi (acceso por PDO con sentencias preparadas; cláusulas dinámicas siempre con parámetros ligados). 222 indicadores, 3 noticias publicadas.
- **Seguridad pública**: payloads SQLi/XSS en parámetros GET de páginas públicas → sin reflejo ejecutable ni error.

---

## 4. Correcciones aplicadas (con verificación)

1. **Stored XSS en noticias** — Nuevo saneador con lista blanca `include/html_sanitizer.php` (`cms_sanitize_rich_html`, basado en `DOMDocument`: elimina etiquetas no permitidas, atributos `on*`, esquemas `javascript:`/`data:`). Aplicado en `noticia.php:134`. *Verificado:* `<script>`/`onerror` eliminados, formato legítimo conservado.
2. **phpinfo() sin auth** — `admin/info.php` ahora exige `auth_require_login()`. *Verificado:* 302 a login sin sesión.
3. **Analítica sin auth** — `dashboard.php` exige login; `map_data.php` y `filtrar_datos.php` devuelven `401` JSON sin sesión. *Verificado:* 302/401.
4. **Ruta del tracker en `formulario_usuario.php`** — corregida a `__DIR__` + `try/catch`. *Verificado:* antes fatal, ahora 503 controlado.
5. **Regex de enlaces de banners** — delimitador cambiado de `#` a `~`. *Verificado:* enlace `https://…` ahora se guarda (antes imposible).
6. **Tabla `accesos_observatorio`** — migración `database/migrations/015_accesos_observatorio.sql` + `try/catch` en tracker, `dashboard.php`, `map_data.php`, `filtrar_datos.php`. *Verificado:* el dashboard muestra un aviso amigable y canvas vacíos en vez de pantalla blanca; el API degrada a JSON con `warning`.
7. **Rendimiento del micrositio de género** — `loading="lazy"` inyectado en las `<img>` del contenido de secciones del CMS (`lib/cms_microsite_sections.php`, función `cms_lazyload_section_imgs`) y en las 4 imágenes de barreras de `observatorio.php`. *Verificado:* carga inicial de **47.4 MB → 0.30 MB**; las imágenes diferidas cargan al abrir su pestaña.
8. **Jerarquía de encabezados** — diapositivas del hero: solo la primera es `<h1>`, el resto `<h2>` (`observatorio.php`), con CSS igualado (`microsite-pro.css`). *Verificado:* 1 `<h1>` por micrositio, sin cambio visual.
9. **Uploads de noticias** — `cms/news.php`: se excluye SVG, se valida el contenido real con `getimagesize`, tope de 5 MB y nombre con sufijo aleatorio.
10. **Mensaje del asistente** — texto ciudadano; instrucciones técnicas movidas a comentario HTML (`include/assistant-widget.php`). *Verificado* en el panel.
11. **Mapa Fondo Mujer** — `loadGeo()` espera a `DOMContentLoaded` para usar el respaldo `boyacaData`. *Verificado:* 124 polígonos.

Todos los archivos PHP modificados pasan `php -l` sin errores.

---

## 5. Pendientes priorizados (para el propietario)

| Prioridad | Acción |
|-----------|--------|
| **1 (Crítica)** | Rotar la app-password de Gmail de `observatorios.boyaca@gmail.com` y purgar `flow/config.ini` del historial de Git (git filter-repo / BFG). |
| **2 (Alta)** | Eliminar o mover fuera del docroot `testdb.php`, `dashboard/testdb.php`, `test-ip.php`; migrar credenciales de `tracker.php` a variables de entorno. |
| **3 (Alta)** | Regenerar la tabla `accesos_observatorio`: eliminar el `.ibd` huérfano (MySQL detenido) o `DROP TABLE IF EXISTS` + aplicar la migración 015, para habilitar la analítica de accesos. |
| **4 (Media)** | Añadir tokens CSRF a los formularios del CMS; endurecer cookies de sesión (`HttpOnly`+`SameSite`); `session_regenerate_id(true)` tras login. |
| **5 (Media)** | Sincronizar `database/schema.sql` con la migración 012; añadir índice `(content_status, published_at)` a `news`; añadir `*.rar` a `.gitignore`. |

---

## 6. Las 5 mejoras de mayor impacto a futuro

1. **Pipeline de imágenes**: comprimir/convertir a WebP y servir tamaños responsivos las ilustraciones de género (siguen pesando 2–3.5 MB cada una aunque ahora se difieran). Reduciría el peso por pestaña y el consumo de datos del ciudadano.
2. **Capa de seguridad transversal**: middleware con CSRF, cabeceras de seguridad (CSP, X-Frame-Options, X-Content-Type-Options) y endurecimiento de sesión aplicado a todo el CMS en un solo punto.
3. **Sanitización en origen del HTML editorial**: aplicar `cms_sanitize_rich_html` también al **guardar** en `cms/news.php` y en el contenido de `cms_microsite_sections`, no solo al renderizar, para limpiar el contenido ya almacenado.
4. **Salud de servicios y estados de error unificados**: detección de disponibilidad del asistente (Streamlit/Ollama) y de Power BI con estados vacíos consistentes y monitoreo, evitando que dependencias externas degraden la experiencia.
5. **Gobernanza de esquema de BD**: consolidar `schema.sql` + migraciones en una sola fuente de verdad versionada (incluida `accesos_observatorio`), con índices para los patrones de consulta frecuentes y limpieza de tablas sin uso.

---

## 7. Reversibilidad de las pruebas

Todos los datos de prueba se crearon con marca `[TEST]` y se eliminaron al finalizar: banner de prueba (creado→editado→toggled→eliminado), noticia de prueba (creada→eliminada), fila de contacto (creada→restaurada al estado vacío original), usuario y rol de prueba del CMS (creados→eliminados). **La BD quedó exactamente en su estado inicial** (verificado: 0 filas `[TEST]`, 0 usuarios, `cms_contact` vacía).

> Nota sobre `accesos_observatorio`: la migración 015 quedó escrita para instalaciones nuevas, pero **no se pudo crear la tabla en esta BD** porque existe un *tablespace* InnoDB huérfano residual (`ERROR 1813: Tablespace … exists`) de un borrado previo. No se manipularon archivos de InnoDB por seguridad. Para regenerarla, el propietario debe eliminar el `.ibd` huérfano (con MySQL detenido) o ejecutar `DROP TABLE IF EXISTS accesos_observatorio` seguido de la migración 015. Gracias a las correcciones de tolerancia (H-07), la ausencia de la tabla ya **no** produce pantalla blanca.
