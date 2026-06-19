# CMS dinámico para pestañas de micrositios

Este documento describe el sistema de **pestañas y sub-secciones gestionables desde el CMS**, que reemplaza el contenido estático de las páginas `indic-*.php` (legacy) en la nueva arquitectura `observatorio.php?slug=...`.

## Resumen

- **Tabla**: `cms_microsite_sections` (jerárquica vía `parent_id`).
- **Editor**: TinyMCE 7 con visor de código, subida de imágenes, listas, tablas, links — “estilo WordPress”.
- **Subida de archivos**: `website/cms/upload-media.php` → guarda en `website/uploads/cms/AÑO/MES/`.
- **Render público**: `observatorio.php` lee las pestañas activas y las renderiza junto a las pestañas fijas (Tablero, Hoja de vida, etc.).
- **Migración inicial**: script `database/scripts/migrate_genero_legacy_content.php` carga las 9 pestañas históricas de `indic-genero.php` con sus chips y metadata.

## Estructura de datos

| Columna | Tipo | Descripción |
| --- | --- | --- |
| `id` | INT PK | identificador |
| `observatory_id` | SMALLINT FK | referencia a `observatories(id)` |
| `parent_id` | INT NULL | `NULL` = pestaña raíz · valor = sub-sección (chip/accordion/card) |
| `section_key` | VARCHAR(64) | slug interno (`barreras`, `informacion`, etc.) |
| `title` | VARCHAR(255) | título visible en la pestaña/sub-sección |
| `subtitle` | VARCHAR(512) | bajada corta (debajo del título) |
| `body_html` | LONGTEXT | HTML generado desde TinyMCE |
| `layout` | VARCHAR(32) | `standard`, `chips`, `accordion`, `cards`, `split` |
| `icon` | VARCHAR(64) | icono FontAwesome (`fa-landmark`, etc.) |
| `image_url` | VARCHAR(512) | imagen destacada (relativa a `website/`) |
| `cta_label`/`cta_url` | VARCHAR | botón opcional al final de la sección |
| `sort_order` | SMALLINT | orden de aparición |
| `is_active` | TINYINT(1) | publica/oculta sin borrar |

### Layouts soportados

- **standard** — texto libre con imagen banner opcional.
- **chips** — la pestaña muestra una barra de chips internos; cada hijo es un chip.
- **accordion** — los hijos se muestran como acordeón.
- **cards** — los hijos se muestran como rejilla de mini-tarjetas.
- **split** — bloque con imagen a un lado y texto al otro.

## Despliegue (orden)

### 1. Ejecutar la migración SQL

Desde phpMyAdmin o consola:

```bash
mysql -u root observatorio_boyaca < database/migrations/013_microsite_sections.sql
```

### 2. Cargar el contenido legado de género

```bash
cd C:\xampp\htdocs\Observatorio2026
C:\xampp\php\php.exe database/scripts/migrate_genero_legacy_content.php
```

Opciones:

- `--dry-run` — simula sin escribir
- `--reset`   — borra primero todas las secciones de género

El script es **idempotente**: si ya existen las secciones con esa `section_key`, las actualiza en vez de duplicar.

### 3. Verificar

- CMS: <http://localhost/Observatorio2026/website/cms/tabs.php?obs=5>
- Público: <http://localhost/Observatorio2026/website/observatorio.php?slug=genero>

## Flujo del editor

1. Inicie sesión en `website/admin/auth/login.php` con un usuario que tenga permiso de escritura en el módulo `tabs`.
2. Vaya a **Pestañas micrositios** en el menú lateral.
3. Seleccione un observatorio (chips superiores).
4. La columna izquierda muestra el árbol de pestañas; la derecha el editor.
5. Use el botón **Imagen** dentro de TinyMCE para subir imágenes inline; van a `website/uploads/cms/AAAA/MM/` con nombre normalizado.
6. Marque **Sección activa** y pulse **Guardar**.

## Notas de seguridad

- El endpoint `upload-media.php` requiere `auth_require_permission('tabs', true)`.
- Tipos permitidos: JPG, PNG, WebP, GIF y SVG (este último se sanitiza eliminando `<script>` y manejadores `on*=` antes de guardarse).
- Tamaño máximo por archivo: 5 MB.
- Las URLs se almacenan como rutas relativas al directorio `website/` para evitar fugas absolutas y facilitar despliegues.

## Compatibilidad

`observatorio.php` mantiene retro-compatibilidad: si la tabla `cms_microsite_sections` no existe o no tiene datos activos, la página se comporta como antes (carga `data/content.json` y muestra la sección hardcoded `#barreras-quees`).
