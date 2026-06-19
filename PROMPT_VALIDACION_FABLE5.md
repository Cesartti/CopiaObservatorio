# Prompt de validación integral — Red de Observatorios de Boyacá

> Copie todo el contenido de este archivo y péguelo como primer mensaje en una sesión
> de Claude Code (Fable 5) abierta en `C:\xampp\htdocs\Observatorio2026`.
> Requisitos previos: XAMPP corriendo (Apache + MySQL); opcionalmente el asistente
> (`AsistenteOllama\run_asistente.bat`) y Ollama si se desea validar el chat de punta a punta.

---

Actúa como auditor técnico y de experiencia de usuario senior. Tu misión es validar, corregir y dejar constancia de que la **Red de Observatorios de Boyacá** es la página de observatorios más robusta y funcional a nivel nacional. Trabaja de forma autónoma: ejecuta, navega, prueba, corrige y verifica cada corrección antes de continuar. No me preguntes permiso para pruebas reversibles; sí detente antes de borrar datos reales.

## Contexto del proyecto

- Stack: PHP 8 + MySQL (XAMPP, Windows), sitio en `website/`, raíz pública `http://localhost/Observatorio2026/website/`.
- 5 observatorios (microsites dinámicos): `observatorio.php?slug={economico|social|ambiente|cti|genero}`.
- Portal principal: `index.php`. Noticias: `noticias.php` / `noticia.php`. Indicadores: `indicador.php?id=`.
- CMS admin: `website/cms/` (login en `admin/auth/login.php`) — banners, noticias, pestañas de microsite (`tabs.php`, tabla `cms_microsite_sections`, las secciones con key `widget-*` alimentan carruseles y no son pestañas), indicadores, contacto, tableros.
- Catálogos en archivos: `config/infografias.php`, `config/publicaciones_universidades.php`, `config/observatories.php`, `config/observatory_categories.php`.
- Ruta de atención interactiva (parallax): `ruta-atencion-genero.html` con recursos en `ruta_assets/`.
- Asistente de chat: widget flotante (iframe a Streamlit en `http://localhost:8501`, código en `AsistenteOllama/`; usa Supabase pgvector + Ollama).
- Widget de accesibilidad: `include/accessibility-widget.php` en todas las páginas.
- Migraciones y esquema: `database/` (schema.sql + migrations/).

## Metodología obligatoria

1. Levanta o reutiliza el servidor de previsualización y navega como lo haría un ciudadano Y como un administrador.
2. Para cada hallazgo usa severidades: **Crítico** (rompe función/datos), **Alto** (función degradada), **Medio** (UX/estilo), **Bajo** (cosmético).
3. **Corrige tú mismo** todo Crítico y Alto; los Medios si la corrección es acotada; los Bajos déjalos listados.
4. Toda prueba con datos debe ser **reversible**: crea registros marcados `[TEST]`, verifícalos y elimínalos al terminar.
5. Verifica cada corrección en el navegador (DOM/comportamiento real), no solo con análisis de código.
6. Entrega al final un informe ejecutivo en español: qué probaste, qué encontraste, qué corregiste, qué queda pendiente con prioridad.

## Áreas a validar (cobertura mínima)

### 1. Funcional público (los 5 observatorios)
- Cada microsite carga sin errores PHP/JS; hero, descripción, carruseles, líneas temáticas (modal con contenido y atajo a Categorías), pestañas "Explora el observatorio" (horizontales y la columna "Consulta y documentación"), noticias, indicadores destacados, integrantes (género), footer de contacto.
- Pestañas: Tablero de datos (tableros Power BI en género con previsualización automática), Ruta de atención (iframe parallax + pantalla completa), Hoja de vida (filtros), Categorías (filtros), Descarga de datos (descargas reales), Infografías (galería + lightbox carrusel), Publicaciones universidades (buscador + filtros por tipo/línea/universidad/año).
- Portal: banners (todas las combinaciones: solo texto, texto+enlace, solo imagen, completo), micrositios, redes sociales, noticias, footer.
- Noticias: listado con filtros/paginación/búsqueda, detalle con relacionadas, colores por observatorio.
- Ruta de atención: recorrer TODAS las combinaciones (comunitaria/institucional × mujer/NNA/LGBTIQ+ × tipos de violencia × contextos); cada decisión abre la rama correcta (incluida la evaluación de riesgo → medida de atención/protección); fondo según orientación del dispositivo.

### 2. Administración (CMS)
- Iniciar sesión y probar CRUD completo de: banners (crear con/sin imagen/enlace, editar, activar/desactivar, eliminar), noticias (borrador/publicada, imagen, vista previa), pestañas de microsite (crear/editar/mover/desactivar, los 5 layouts), widgets `widget-carrusel` y `widget-integrantes` (cambiar una imagen y verla reflejada en el microsite), indicadores, contacto (editar y verificar el footer en todo el sitio).
- Subida de archivos: formatos permitidos, límite de tamaño, rechazo de archivos peligrosos.
- Que NINGÚN contenido editado en el CMS pueda romper el layout público (HTML desbalanceado, scripts).

### 3. UI/UX y responsive
- Probar cada página clave en 360px (móvil), 768px (tablet) y 1366px+ (escritorio): sin desbordes horizontales, texto legible, botones alcanzables, carruseles usables al tacto.
- Consistencia visual entre observatorios (colores por dimensión, mismos patrones de tarjetas/chips/modales).
- Estados vacíos y de error amigables (sin datos, BD caída, sin internet para Power BI).
- Navegación: menú del microsite (Inicio, Red de observatorios, Explora el observatorio, Noticias), breadcrumbs, enlaces rotos (rastrea TODOS los href/src internos y reporta 404).

### 4. Accesibilidad (WCAG 2.1 AA)
- Widget de accesibilidad funcional en todas las páginas (tamaño de letra, filtros de color para daltonismo, lectura en voz alta, guía de lectura) y persistencia entre páginas.
- Contraste de color suficiente, alt en imágenes informativas, foco visible, navegación por teclado en pestañas/modales/carruseles, aria-labels en controles, un solo h1 por página y jerarquía de encabezados.

### 5. Base de datos
- Revisa `database/schema.sql` y migraciones vs. el esquema real (`SHOW TABLES`, `DESCRIBE`): tablas huérfanas, columnas sin uso, migraciones sin aplicar.
- Integridad: claves foráneas, índices en columnas de filtro/orden frecuentes, charset utf8mb4 consistente.
- Consultas: busca consultas sin prepared statements, N+1 evidentes o sin LIMIT en listados.
- Verifica que todos los módulos toleran BD caída (mensaje claro, sin pantalla blanca).

### 6. Seguridad
- XSS: intenta inyectar `<script>` desde cada campo del CMS y verifica el escape en el público.
- SQLi: prueba parámetros GET/POST con comillas y payloads básicos en páginas públicas.
- Auth: páginas del CMS inaccesibles sin sesión; el logout invalida; sin contraseñas/secretos en archivos versionados (revisa README, .env en .gitignore).
- Uploads: doble extensión, SVG con script, tamaño excedido.

### 7. Rendimiento
- Peso total y número de peticiones del portal y un microsite (objetivo < 3 MB inicial sin contar iframes diferidos); imágenes sobredimensionadas (sirve responsive o comprime); lazy loading donde aplique; recursos 404 o duplicados (CSS/JS cargados dos veces).

### 8. Asistente de chat
- El widget abre en todas las páginas, el iframe carga, el botón de voz lee la respuesta; si Ollama/Supabase no responden, el mensaje de error es claro para el usuario (no técnico).

## Entregable final

1. **Informe ejecutivo** (en el chat): estado general, tabla de hallazgos por severidad con su estado (corregido/pendiente), y las 5 mejoras de mayor impacto recomendadas a futuro.
2. **Correcciones aplicadas** con su verificación en navegador.
3. Archivo `INFORME_VALIDACION_[fecha].md` en la raíz del proyecto con el detalle completo, para trazabilidad institucional.
