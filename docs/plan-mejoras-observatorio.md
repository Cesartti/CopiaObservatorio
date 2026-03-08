# Plan inicial de mejoras: CopiaObservatorio

## Diagnóstico rápido del estado actual

1. El portal depende de archivos manuales (`indicador/<id>/*.info` y `*.csv`) para construir visualizaciones, lo que ralentiza actualizaciones y aumenta riesgo de inconsistencias.
2. Existen listados estáticos extensos en páginas de dimensión (por ejemplo, social), difíciles de mantener y de explicar a ciudadanía.
3. Hay conexión a base de datos en el módulo `dashboard`, pero no está integrada al flujo principal de publicación de indicadores.
4. El frontend no ofrece una vista-resumen clara del estado de datos por dimensión, como sí ocurre en observatorios más maduros.

## Mejoras propuestas (frontend + datos)

### 1) Capa de datos
- Migrar de archivos sueltos a un modelo relacional de indicadores + observaciones.
- Mantener ETL incremental (diario/semanal) desde fuentes oficiales hacia tablas normalizadas.
- Exponer APIs JSON para frontend (catálogo, series, metadatos, fuentes).
- Agregar control de calidad: fechas de corte, versionado de cargas y validaciones de duplicados.

### 2) Capa frontend
- Implementar una vista de “estado del observatorio” con métricas de cobertura por dimensión.
- Reducir navegación manual por listados largos e incorporar búsqueda/filtros.
- Estandarizar fichas de indicador con lenguaje ciudadano: qué mide, por qué importa, cómo leer la gráfica.
- Mejorar accesibilidad: contraste, labels de formularios, estructura semántica y textos legibles.

### 3) Prioridad de implementación sugerida
1. API de catálogo de indicadores.
2. API de datos por gráfico/serie.
3. Nueva vista de resumen y exploración de indicadores.
4. Migración progresiva de páginas de dimensión para consumir API.
5. ETL automático hacia base de datos y retiro gradual de CSV manuales.

## Tareas iniciadas en este cambio

- API `api/indicators.php` para listar indicadores de forma automática.
- API `api/indicator_data.php` para exponer datos tabulares por indicador y gráfico.
- Pantalla `estado-observatorio.php` con resumen y buscador de indicadores.
- Esquema base SQL (`database/schema.sql`) para comenzar la automatización de datos.
