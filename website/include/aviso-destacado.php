<?php
/**
 * Aviso destacado: una convocatoria con fecha, arriba del todo.
 *
 * Una noticia queda sepultada cuando la página es larga: en el micrositio de
 * Género la del ciclo de formación aparecía al 94 % de la página, después de
 * todo el catálogo de indicadores. Una convocatoria con fecha de cierre no
 * sirve de nada ahí.
 *
 * Este aviso se pinta debajo del encabezado, lleva el enlace directo a la
 * inscripción y desaparece solo cuando pasa la última fecha. No hay que
 * acordarse de bajarlo.
 *
 * Uso:
 *   $avisoSlug = 'genero';  // o 'general' para la portada
 *   include __DIR__ . '/include/aviso-destacado.php';
 */

$AVISOS = [
    'genero' => [
        'hasta'     => '2026-11-17',
        'etiqueta'  => 'Convocatoria abierta',
        'titulo'    => 'Ciclo de jornadas de formación virtual de la Red Nacional de Observatorios de Mujeres y Género',
        'texto'     => 'Cinco sesiones sobre indicadores de violencias, autonomía económica y trabajo de cuidado, y sobre los instrumentos de gobernanza de la Red. De 3:00 a 5:00 p. m. por Google Meet.',
        'boton'     => 'Inscribirse',
        'enlace'    => 'https://docs.google.com/forms/d/e/1FAIpQLSdIznY_sJqr8dWZ3FUxAbNEZhx7exYaw_QEXWKp3l21BOj4JA/viewform',
        'detalle'   => 'noticia.php?slug=formacion-red-nacional-observatorios-mujeres-genero-2026',
    ],
];
// La portada muestra el mismo aviso que el observatorio que lo tiene abierto.
$AVISOS['general'] = $AVISOS['genero'];

$avisoSlug = $avisoSlug ?? '';
$aviso = $AVISOS[$avisoSlug] ?? null;
if ($aviso && $aviso['hasta'] >= date('Y-m-d')):
    ?>
    <style>
      .aviso-destacado{
        display:flex;flex-wrap:wrap;align-items:center;gap:1rem 1.25rem;
        background:linear-gradient(135deg,#7D2D91 0%,#A4409E 100%);
        color:#fff;border-radius:16px;padding:1.1rem 1.4rem;
        box-shadow:0 8px 24px rgba(125,45,145,.28);
      }
      .aviso-destacado__cuerpo{flex:1 1 320px;min-width:0}
      .aviso-destacado__etiqueta{
        display:inline-block;background:rgba(255,255,255,.22);
        font-size:.7rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;
        padding:.22rem .6rem;border-radius:999px;margin-bottom:.45rem;
      }
      .aviso-destacado__titulo{font-size:1.05rem;font-weight:700;line-height:1.3;margin:0 0 .35rem}
      .aviso-destacado__texto{font-size:.86rem;line-height:1.5;margin:0;color:rgba(255,255,255,.92)}
      .aviso-destacado__acciones{display:flex;flex-wrap:wrap;gap:.6rem;align-items:center}
      .aviso-destacado__btn{
        background:#fff;color:#7D2D91;font-weight:700;font-size:.9rem;
        padding:.6rem 1.25rem;border-radius:999px;text-decoration:none;white-space:nowrap;
      }
      .aviso-destacado__btn:hover{background:#F3E6F7;color:#5E1F70}
      .aviso-destacado__link{color:#fff;font-size:.85rem;text-decoration:underline;white-space:nowrap}
      .aviso-destacado__link:hover{color:#F3E6F7}
      @media (max-width:575.98px){ .aviso-destacado{padding:1rem} .aviso-destacado__titulo{font-size:.98rem} }
    </style>
    <div class="container my-3">
      <div class="aviso-destacado">
        <div class="aviso-destacado__cuerpo">
          <span class="aviso-destacado__etiqueta"><?= htmlspecialchars($aviso['etiqueta']) ?></span>
          <p class="aviso-destacado__titulo"><?= htmlspecialchars($aviso['titulo']) ?></p>
          <p class="aviso-destacado__texto"><?= htmlspecialchars($aviso['texto']) ?></p>
        </div>
        <div class="aviso-destacado__acciones">
          <a class="aviso-destacado__btn" href="<?= htmlspecialchars($aviso['enlace']) ?>"
             target="_blank" rel="noopener"><?= htmlspecialchars($aviso['boton']) ?></a>
          <a class="aviso-destacado__link" href="<?= htmlspecialchars($aviso['detalle']) ?>">Ver la programación</a>
        </div>
      </div>
    </div>
<?php endif; ?>
