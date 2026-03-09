# Fase 1 · Diagnóstico de la plataforma actual

## 1) Estado actual identificado
- **Stack principal**: PHP procedural + archivos estáticos `.info/.csv` para indicadores + JS/CSS sueltos por página.
- **Modelo de datos**: mezcla de contenido incrustado en PHP y archivos en `website/indicador/<id>`.
- **Visualización**: páginas por dimensión con listados manuales y embebidos externos (PowerBI en algunos casos).
- **Administración**: módulo `website/admin` y `website/dashboard` desacoplado del flujo principal de publicación.

## 2) Hallazgos críticos
1. **Arquitectura acoplada**: contenido, presentación y lógica mezclados en páginas individuales.
2. **Escalabilidad baja**: para agregar indicadores o categorías se modifica código manual.
3. **Riesgo de inconsistencia**: catálogo de indicadores repetido en varias páginas.
4. **UX heterogénea**: múltiples estilos y patrones de navegación no unificados.
5. **Accesibilidad limitada**: sin diseño sistemático AA, sin componentes consistentes para lectura/contraste.
6. **Gobierno de datos incompleto**: falta modelo robusto para trazabilidad, estados editoriales y auditoría.
7. **Seguridad y roles**: no hay RBAC consolidado para flujos editoriales por observatorio.

## 3) Deuda técnica priorizada
- Unificar sistema de diseño y layout maestro.
- Centralizar catálogo de observatorios, navegación y contenidos.
- Implementar modelo de datos para roles, estados editoriales e historial.
- Construir APIs internas para indicadores, noticias, documentos y búsqueda global.
- Separar progresivamente frontend público de panel administrativo.

## 4) Estrategia de modernización (sin romper operación)
1. **Fase de convivencia**: mantener URLs actuales y crear capa nueva en paralelo (`red-home`, `observatorio.php?slug=`).
2. **Backoffice progresivo**: agregar auth + roles + workflow antes de migrar toda la edición.
3. **Datos**: convivir temporalmente con archivos `.csv/.info` mientras se migra a BD.
4. **Migración por dominio**: empezar por Social y Económico para validar patrón, luego escalar al resto.

## 5) KPI de éxito de modernización
- Reducción del tiempo de publicación de indicadores.
- Porcentaje de contenidos administrados desde panel (vs edición de código).
- Cumplimiento de accesibilidad AA en páginas clave.
- Tiempo medio de carga y navegación entre observatorios.
