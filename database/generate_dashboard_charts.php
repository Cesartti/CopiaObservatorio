#!/usr/bin/env php
<?php
/**
 * ============================================================================
 *  GENERADOR DE GRÁFICOS CMS A PARTIR DE INDICADORES MIGRADOS
 * ============================================================================
 *  Lee los indicadores ya migrados en la BD (indicators + indicator_observations)
 *  y genera gráficos Google Charts en cms_charts para los tableros "principal"
 *  de cada observatorio.
 *
 *  Selecciona los indicadores con más datos y genera hasta 6 gráficos
 *  por observatorio (los más representativos).
 *
 *  USO:
 *    php generate_dashboard_charts.php [--dry-run] [--max-charts=6]
 * ============================================================================
 */

$dryRun = in_array('--dry-run', $argv ?? []);
$maxCharts = 6;
foreach ($argv ?? [] as $arg) {
    if (strpos($arg, '--max-charts=') === 0) {
        $maxCharts = (int) substr($arg, strlen('--max-charts='));
    }
}

require_once __DIR__ . '/../website/config/database.php';
$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "ERROR: No se pudo conectar a BD.\n");
    exit(1);
}

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  Generador de gráficos para tableros CMS            ║\n";
echo "║  Max gráficos por observatorio: {$maxCharts}                   ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// Obtener tableros "principal" por observatorio
$dashboards = $pdo->query('
    SELECT d.id AS dashboard_id, d.observatory_id, o.slug, o.name
    FROM cms_dashboards d
    JOIN observatories o ON o.id = d.observatory_id
    WHERE d.slug = "principal" AND d.is_active = 1
')->fetchAll(PDO::FETCH_ASSOC);

if (empty($dashboards)) {
    fwrite(STDERR, "No hay tableros 'principal' activos. Ejecute seed_full_content.sql primero.\n");
    exit(1);
}

$totalCharts = 0;

foreach ($dashboards as $dash) {
    $obsId = (int) $dash['observatory_id'];
    $dashId = (int) $dash['dashboard_id'];
    echo "  [{$dash['slug']}] {$dash['name']} (dashboard_id={$dashId})\n";

    // Buscar indicadores con más observaciones de datos
    $indicators = $pdo->prepare('
        SELECT i.id, i.title, i.objective,
               COUNT(DISTINCT io.id) AS obs_count,
               COUNT(DISTINCT io.chart_order) AS chart_count
        FROM indicators i
        LEFT JOIN indicator_observations io ON io.indicator_id = i.id
        WHERE i.observatory_id = ? AND i.content_status = "published"
        GROUP BY i.id
        HAVING obs_count > 0
        ORDER BY obs_count DESC
        LIMIT ?
    ');
    $indicators->execute([$obsId, $maxCharts * 2]); // traemos el doble para elegir
    $inds = $indicators->fetchAll(PDO::FETCH_ASSOC);

    if (empty($inds)) {
        echo "    → Sin indicadores con datos. Saltando.\n\n";
        continue;
    }

    // Limpiar gráficos previos generados automáticamente
    if (!$dryRun) {
        $pdo->prepare('DELETE FROM cms_charts WHERE dashboard_id = ?')->execute([$dashId]);
    }

    $chartOrder = 0;
    foreach (array_slice($inds, 0, $maxCharts) as $ind) {
        $indId = (int) $ind['id'];
        $chartOrder++;

        // Obtener el primer chart_order con datos
        $firstChart = $pdo->prepare('
            SELECT DISTINCT chart_order FROM indicator_observations
            WHERE indicator_id = ? ORDER BY chart_order LIMIT 1
        ');
        $firstChart->execute([$indId]);
        $co = (int) $firstChart->fetchColumn();

        // Obtener datos para construir Google Charts JSON
        $dataRows = $pdo->prepare('
            SELECT period_label, category, value_decimal, value_text
            FROM indicator_observations
            WHERE indicator_id = ? AND chart_order = ?
            ORDER BY period_label, category
        ');
        $dataRows->execute([$indId, $co]);
        $rows = $dataRows->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) continue;

        // Construir estructura Google Charts: [["Periodo","Serie1","Serie2"],[val,...]]
        $periods = [];
        $categories = [];
        $matrix = [];

        foreach ($rows as $r) {
            $p = $r['period_label'];
            $c = $r['category'];
            if (!isset($periods[$p])) $periods[$p] = true;
            if (!isset($categories[$c])) $categories[$c] = true;
            $matrix[$p][$c] = $r['value_decimal'] !== null
                ? (float) $r['value_decimal']
                : $r['value_text'];
        }

        $periodKeys = array_keys($periods);
        $catKeys = array_keys($categories);

        // Limitar a 8 series máximo para legibilidad
        if (count($catKeys) > 8) {
            $catKeys = array_slice($catKeys, 0, 8);
        }

        // Header
        $header = array_merge(['Período'], $catKeys);
        $chartData = [$header];

        foreach ($periodKeys as $p) {
            $row = [$p];
            foreach ($catKeys as $c) {
                $val = $matrix[$p][$c] ?? 0;
                $row[] = is_numeric($val) ? (float) $val : 0;
            }
            $chartData[] = $row;
        }

        // Decidir tipo de gráfico
        $chartType = 'LineChart';
        if (count($periodKeys) <= 6 && count($catKeys) <= 4) {
            $chartType = 'ColumnChart';
        }
        if (count($catKeys) >= 5) {
            $chartType = 'BarChart';
        }

        // Colores por observatorio
        $colorSchemes = [
            'economico' => ['#0f3557', '#d8a21d', '#2980b9', '#27ae60'],
            'social'    => ['#5f2a8a', '#d74fa6', '#3498db', '#e74c3c'],
            'ambiente'  => ['#1f6b45', '#23a9b8', '#27ae60', '#f39c12'],
            'cti'       => ['#1847b7', '#6d4aff', '#00bcd4', '#ff5722'],
            'genero'    => ['#7d2d91', '#ef6f8f', '#9c27b0', '#e91e63'],
        ];
        $colors = $colorSchemes[$dash['slug']] ?? ['#333'];

        $optionsJson = json_encode([
            'colors' => $colors,
            'legend' => ['position' => count($catKeys) > 1 ? 'bottom' : 'none'],
            'chartArea' => ['width' => '80%', 'height' => '70%'],
        ], JSON_UNESCAPED_UNICODE);

        $dataJson = json_encode($chartData, JSON_UNESCAPED_UNICODE);

        // Decidir layout
        $layoutSpan = count($catKeys) > 3 ? 12 : 6;
        $tileHeight = $chartType === 'BarChart' ? 380 : 320;

        $title = mb_substr($ind['title'], 0, 200);

        echo "    #{$chartOrder}: {$title} ({$chartType}, " . count($chartData) . " filas)\n";

        if (!$dryRun) {
            $stmt = $pdo->prepare('
                INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json,
                    sort_order, layout_span, tile_height_px, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
            ');
            $stmt->execute([
                $dashId, $title, $chartType, $optionsJson, $dataJson,
                $chartOrder, $layoutSpan, $tileHeight
            ]);
        }
        $totalCharts++;
    }
    echo "\n";
}

echo "╔══════════════════════════════════════════════════════╗\n";
printf("║  Gráficos generados: %3d                            ║\n", $totalCharts);
echo "╚══════════════════════════════════════════════════════╝\n";

if ($dryRun) {
    echo "\n  ⚠  DRY-RUN: no se escribió nada.\n";
}
echo "  ✓ Proceso completado.\n";
