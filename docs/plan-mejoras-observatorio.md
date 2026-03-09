# Plan maestro de modernización · Red de Observatorios de Boyacá

## Resumen ejecutivo
La modernización se plantea en **dos velocidades**:
1. **Continuidad operativa**: mantener páginas actuales en PHP sin interrumpir publicación.
2. **Nueva experiencia unificada**: construir portal madre + micrositios por observatorio + backoffice con roles y trazabilidad.

## Entregables construidos en esta iteración
- Diagnóstico técnico y deuda priorizada (`docs/modernizacion/fase1-diagnostico.md`).
- Arquitectura objetivo de plataforma, URLs, entidades y flujo editorial (`docs/modernizacion/fase2-arquitectura-objetivo.md`).
- Sistema UI/UX base (principios, paletas por observatorio, wireframe textual) (`docs/modernizacion/fase3-ui-ux.md`).
- Nuevo **home institucional**: `website/red-home.php`.
- Nuevo **micrositio dinámico por observatorio**: `website/observatorio.php?slug=`.
- Buscador global inicial: `website/api/search.php`.
- Autenticación base para admin y protección de dashboard: `website/admin/auth/*` + ajuste en `website/dashboard/index.php`.
- Esquema de base de datos ampliado para usuarios, roles, contenidos, workflow y auditoría (`database/schema.sql`).

## Fases siguientes (ejecución recomendada)
1. Migrar noticias/documentos/datasets reales a tablas SQL.
2. Conectar formularios de administración (CRUD + workflow).
3. Unificar diseño de todas las páginas legadas a componentes del nuevo sistema.
4. Integrar ETL para actualización automatizada de series.
5. Habilitar mapas territoriales y filtros avanzados por municipio/provincia.
6. Fortalecer seguridad (CSRF, rate limit, política de contraseñas, recuperación segura).
