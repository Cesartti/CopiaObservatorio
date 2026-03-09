# Fase 2 · Arquitectura objetivo

## Arquitectura funcional
- **Portal Madre**: Red de Observatorios (home unificada + búsqueda global + acceso transversal).
- **5 micrositios temáticos**: económico, social, ambiente, cti, género.
- **Backoffice único**: usuarios, roles, contenidos, datasets, workflow y auditoría.

## URL map propuesto
- `/red-home.php`
- `/observatorio.php?slug=economico|social|ambiente|cti|genero`
- `/indicadores.php?obs=<slug>` (próxima fase)
- `/noticias.php?obs=<slug>` (próxima fase)
- `/documentos.php?obs=<slug>` (próxima fase)
- `/api/search.php?q=`
- `/admin/auth/login.php`
- `/dashboard/` (migración progresiva al nuevo admin)

## Modelo de datos objetivo (alto nivel)
- `roles`, `users`, `user_observatories`
- `observatories`
- `indicators`, `indicator_series`, `indicator_metadata`
- `news`, `documents`, `datasets`
- `content_workflows`, `content_versions`, `audit_logs`

## Flujo editorial
1. Cargador crea borrador.
2. Editor ajusta y envía a revisión.
3. Revisor aprueba/rechaza con comentarios.
4. Administrador publica y programa.
5. Sistema registra auditoría/versionado.

## Seguridad
- Password hash seguro (`password_hash`).
- Sesiones con timeout, CSRF token y regeneración de sesión.
- Permisos por rol y por observatorio (scoped RBAC).
