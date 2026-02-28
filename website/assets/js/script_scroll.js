document.addEventListener("DOMContentLoaded", () => {
  const IMG_BASE = "assets/svg/img-genero/RutaAtencion/15pasos";

  // Altura de header fijo
  const headerEl = document.querySelector(
    ".navbar, header .navbar, .site-header, header.sticky, header#header, .topnav"
  );
  const headerOffset = headerEl ? headerEl.offsetHeight : 0;
  document.documentElement.style.setProperty("--header-offset", `${headerOffset}px`);

  const hero = document.querySelector(".hero");
  if (hero) hero.style.minHeight = `calc(100vh - ${headerOffset}px)`;

  // Texto corto (columna derecha)
  const pasos = [
    { icon: "fa-hand-holding-medical", text: "Acogida inicial: Te reciben en urgencias o consulta prioritaria con respeto y privacidad." },
    { icon: "fa-user-nurse", text: "Valoración médica: Evalúan tu estado físico, lesiones visibles o signos de abuso." },
    { icon: "fa-shield-heart", text: "Evaluación de riesgo: Determinan si estás en riesgo extremo y si necesitas protección inmediata." },
    { icon: "fa-clipboard-user", text: "Consentimiento informado: Te explican cada procedimiento y solo avanzan si tú lo autorizas." },
    { icon: "fa-user-doctor", text: "Examen físico completo: Revisión integral para documentar lesiones y recolectar pruebas." },
    { icon: "fa-dna", text: "Pruebas diagnósticas: Garantizaremos el tratamiento pertinente indicado para tu caso." },
    { icon: "fa-face-smile", text: "Atención emocional inmediata: Una psicóloga te acompaña desde el primer momento." },
    { icon: "fa-diagram-project", text: "Plan de atención integral: Te explicamos qué ayuda hay y cómo acceder a ella." },
    { icon: "fa-syringe", text: "Medicamentos y vacunas para protegerte, según tu caso y tiempos." },
    { icon: "fa-user-friends", text: "Citas de control y acompañamiento continuo." },
    { icon: "fa-people-arrows", text: "Activación intersectorial y remisiones necesarias." },
    { icon: "fa-face-smile", text: "Acompañamiento continuo en salud mental." },
    { icon: "fa-file-medical", text: "Registro y notificación epidemiológica: datos protegidos." },
    { icon: "fa-heart-circle-check", text: "Seguimiento institucional a cargo de medicina general." },
    { icon: "fa-hand-holding-heart", text: "Cierre de caso y orientación para continuar con apoyos." }
  ];

  // Contenido ampliado (ahora se inserta inline en cada paso)
  const detalles = [
    // 1
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-hand-holding-medical" style="color:#19c20e"></i> Acogida inicial: aquí estás segura</h3>

        <p>
          <i class="fa-solid fa-hospital" style="color:#0ea5e9"></i>
          Cuando llegues al <strong>hospital o centro de salud</strong> y manifiestes que has sufrido violencia sexual,
          se te atenderá <strong>prioritariamente</strong> en un <strong>espacio privado</strong>, sin otras personas alrededor.
        </p>

        <p>
          <i class="fa-solid fa-user-group" style="color:#f59e0b"></i>
          Puedes pedir que te <strong>acompañe alguien de tu confianza</strong> si lo necesitas. <strong>Nadie te juzgará</strong>;
          tú <strong>controlas tus tiempos</strong> y puedes detenerte o pedir pausas en cualquier momento.
        </p>

        <p>
          <i class="fa-solid fa-notes-medical" style="color:#22c55e"></i>
          Recuerda que <strong>toda violencia sexual es una urgencia médica</strong>. Por eso se recomienda consultar
          <strong>de inmediato</strong> por el servicio de <strong>urgencias</strong>. Si llegaste por otro motivo,
          deben realizar tu <strong>remisión inmediata a urgencias</strong>.
        </p>

        <div class="callout">
          <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
          <div>
            <strong>Si has sufrido violencia sexual, acude a urgencias AHORA.</strong><br>
            La atención es <strong>urgente</strong>, <strong>confidencial</strong> y es tu <strong>derecho garantizado</strong>.
          </div>
        </div>
      </div>
    `,
    // 2
    `
      <div class="rich-block">
        <h3>
          <i class="fa-solid fa-user-nurse" style="color:#2563eb"></i>
          Valoración médica: comenzamos con tu historia clínica
        </h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-venus-mars" style="color:#f59e0b"></i>
            <span>Puedes pedir que te atienda un <strong>profesional del género con el que te sientas más cómoda</strong>.</span></li>
          <li><i class="fa-solid fa-clipboard-list" style="color:#22c55e"></i>
            <span>Se solicita tu <strong>relato</strong> para <strong>individualizar</strong> el caso y definir <strong>conductas</strong>.</span></li>
          <li><i class="fa-solid fa-shield-halved" style="color:#0ea5e9"></i>
            <span>Sin <strong>juicios</strong>; tu seguridad es prioridad.</span></li>
          <li><i class="fa-solid fa-circle-pause" style="color:#a855f7"></i>
            <span>Puedes <strong>pausar</strong> y retomar cuando lo necesites.</span></li>
        </ul>
        <div class="callout" style="margin-top:10px">
          <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
          <div><strong>Tus derechos:</strong> elegir quién te atiende, ser escuchada sin juicios y pedir pausas.</div>
        </div>
      </div>
    `,
    // 3
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-shield-halved" style="color:#ef4444"></i> Evaluación de riesgo: ¿estás en peligro ahora mismo?</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-location-dot" style="color:#f59e0b"></i>
            <span>Se valora el <strong>riesgo inmediato</strong> (proximidad del agresor, amenazas, etc.).</span></li>
          <li><i class="fa-solid fa-house" style="color:#10b981"></i>
            <span>Se indaga sobre <strong>armas</strong> y riesgo a otras personas.</span></li>
          <li><i class="fa-solid fa-user-shield" style="color:#3b82f6"></i>
            <span>Si hay <strong>peligro</strong>, se activa un <strong>plan rápido de protección</strong>.</span></li>
          <li><i class="fa-solid fa-hand-holding-heart" style="color:#a855f7"></i>
            <span>Opciones: espacio protegido, <strong>traslado seguro</strong>, medidas de protección.</span></li>
        </ul>
        <div class="callout" style="margin-top:10px;display:grid;grid-template-columns:28px 1fr;gap:10px;
          padding:10px 12px;border-radius:10px;background:#fef3c7;border:1px solid #fde68a;color:#7c2d12;">
          <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;font-size:1.1rem;margin-top:2px"></i>
          <div><strong>Importante:</strong> puedes recibir protección y atención sin denunciar en ese momento.</div>
        </div>
      </div>
    `,
    // 4
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-clipboard-check" style="color:#22c55e"></i> Consentimiento informado: tú decides</h3>
        <p><i class="fa-solid fa-circle-question" style="color:#0ea5e9"></i>
          Antes de cualquier procedimiento te explican <strong>qué es</strong>, <strong>para qué</strong> y <strong>posibles efectos</strong>.</p>
        <p><i class="fa-solid fa-user-shield" style="color:#a855f7"></i>
          Puedes <strong>preguntar</strong> todo y elegir quién te acompaña.</p>
        <div class="chip-group" role="list" aria-label="Opciones de consentimiento" style="margin:8px 0 6px">
          <span class="chip is-active" role="listitem">Decir “sí”</span>
          <span class="chip" role="listitem">Decir “no”</span>
          <span class="chip" role="listitem">“Necesito pensarlo”</span>
        </div>
        <div class="callout" style="margin-top:10px;display:grid;grid-template-columns:28px 1fr;gap:10px;
          padding:10px 12px;border-radius:10px;background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;">
          <i class="fa-solid fa-circle-check" style="color:#16a34a;font-size:1.1rem;margin-top:2px"></i>
          <div><strong>Nada se hará sin tu permiso.</strong> Puedes decir “no” o tomarte tiempo.</div>
        </div>
      </div>
    `,
    // 5
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-user-doctor" style="color:#2563eb"></i> Examen físico completo</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-stethoscope" style="color:#0ea5e9"></i> Examen cuidadoso de lesiones.</li>
          <li><i class="fa-solid fa-circle-question" style="color:#22c55e"></i> Puedes <strong>preguntar</strong> en todo momento.</li>
          <li><i class="fa-solid fa-circle-pause" style="color:#a855f7"></i> Puedes <strong>pedir pausas</strong>.</li>
          <li><i class="fa-solid fa-shield-heart" style="color:#ef4444"></i> Examen genital/anal solo si es necesario y con <strong>consentimiento</strong>.</li>
          <li><i class="fa-solid fa-hourglass-half" style="color:#f59e0b"></i> Conductas varían según el <strong>tiempo</strong> transcurrido.</li>
          <li><i class="fa-solid fa-vial" style="color:#10b981"></i> Posible toma de <strong>muestras legales</strong> (cadena de custodia).</li>
        </ul>
        <div class="callout" style="margin-top:10px;display:grid;grid-template-columns:28px 1fr;gap:10px;
          padding:10px 12px;border-radius:10px;background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;">
          <i class="fa-solid fa-user-shield" style="color:#16a34a;font-size:1.1rem;margin-top:2px"></i>
          <div><strong>Este proceso es tuyo:</strong> autonomía y consentimiento en cada paso.</div>
        </div>
      </div>
    `,
    // 6
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-dna" style="color:#7c3aed"></i> Pruebas diagnósticas</h3>
        <p>Se realizan <strong>con tu consentimiento</strong> y te explican para qué sirve cada una.</p>
        <div class="chip-group" role="tablist" aria-label="Secciones de pruebas">
          <button class="chip is-active" data-target="gen" role="tab" aria-selected="true"><i class="fa-solid fa-vial"></i> Pruebas generales</button>
          <button class="chip" data-target="72h" role="tab" aria-selected="false"><i class="fa-solid fa-clock"></i> Primeras 72&nbsp;horas</button>
          <button class="chip" data-target="4-5d" role="tab" aria-selected="false"><i class="fa-solid fa-calendar-day"></i> Entre 4 y 5 días</button>
        </div>
        <section class="mod-section" data-sec="gen">
          <h4><i class="fa-solid fa-vials" style="color:#0ea5e9"></i> ¿Cuáles son?</h4>
          <ul>
            <li><strong>Sangre:</strong> VDRL, tamizaje VIH, antígenos hepatitis B.</li>
            <li><strong>Sitios específicos (si aceptas):</strong> Gram/cultivo, frotis en fresco, búsqueda de espermatozoides.</li>
          </ul>
        </section>
        <section class="mod-section is-hidden" data-sec="72h">
          <h4><i class="fa-solid fa-hourglass-start" style="color:#f59e0b"></i> Si consultas &lt;= 72 h</h4>
          <ul>
            <li>Se solicitan <strong>pruebas generales</strong>.</li>
            <li>En ciertos casos: <strong>asesoría y prueba rápida VIH</strong>.</li>
          </ul>
        </section>
        <section class="mod-section is-hidden" data-sec="4-5d">
          <h4><i class="fa-solid fa-calendar-check" style="color:#10b981"></i> Si consultas 4–5 días</h4>
          <ul>
            <li><strong>Prueba de embarazo</strong>.</li>
            <li><strong>Asesoría y prueba rápida VIH</strong> (confirmatorio si positiva).</li>
            <li>Diagnósticos/confirmatorios de <strong>ITS</strong>.</li>
          </ul>
        </section>
        <div class="callout" style="margin-top:10px;display:grid;grid-template-columns:28px 1fr;gap:10px;
          padding:10px 12px;border-radius:10px;background:#f0f9ff;border:1px solid #bae6fd;color:#0c4a6e;">
          <i class="fa-solid fa-circle-info" style="color:#0284c7;font-size:1.1rem;margin-top:2px"></i>
          <div><strong>Recuerda:</strong> puedes preguntar, aceptar o rechazar pruebas y pedir explicación de resultados.</div>
        </div>
      </div>
    `,
    // 7
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-face-smile" style="color:#f59e0b"></i> Atención emocional inmediata</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-user-md" style="color:#a855f7"></i> Te acompaña un/a profesional en salud mental.</li>
          <li><i class="fa-solid fa-user-lock" style="color:#0ea5e9"></i> No tienes que hablar si no quieres.</li>
          <li><i class="fa-solid fa-heart" style="color:#ef4444"></i> No hay respuestas “correctas” o “incorrectas”.</li>
        </ul>
        <div class="callout" style="margin-top:10px;display:grid;grid-template-columns:28px 1fr;gap:10px;
          padding:10px 12px;border-radius:10px;background:#fdf2f8;border:1px solid #fbcfe8;color:#831843;">
          <i class="fa-solid fa-hands-holding-child" style="color:#db2777;font-size:1.1rem;margin-top:2px"></i>
          <div><strong>Tu bienestar emocional</strong> es prioridad.</div>
        </div>
      </div>
    `,
    // 8
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-diagram-project" style="color:#7c3aed"></i> Plan de atención integral</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-list-check" style="color:#22c55e"></i> Plan de salud <strong>personalizado</strong>.</li>
          <li><i class="fa-solid fa-scale-balanced" style="color:#0ea5e9"></i> Derechos y orientación legal.</li>
          <li><i class="fa-solid fa-user-shield" style="color:#f59e0b"></i> Siempre con <strong>consentimiento</strong>.</li>
          <li><i class="fa-solid fa-clock" style="color:#a855f7"></i> Se respetan <strong>tus tiempos</strong>.</li>
        </ul>
        <div class="callout" style="margin-top:10px;display:grid;grid-template-columns:28px 1fr;gap:10px;
          padding:10px 12px;border-radius:10px;background:#f0f9ff;border:1px solid #bae6fd;color:#0c4a6e;">
          <i class="fa-solid fa-circle-info" style="color:#0284c7;font-size:1.1rem;margin-top:2px"></i>
          <div><strong>Tip:</strong> pide el plan por escrito y una <strong>copia</strong>.</div>
        </div>
      </div>
    `,
    // 9
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-syringe" style="color:#7c3aed"></i> Medicamentos y vacunas</h3>
        <p>Aplicación de <strong>vacunas</strong> y <strong>tratamientos</strong> según tu caso, con tu consentimiento.</p>
        <div class="chip-group" role="tablist" aria-label="Opciones de prevención y tratamiento">
          <button class="chip is-active" data-target="vac" role="tab" aria-selected="true"><i class="fa-solid fa-syringe"></i> Vacunas</button>
          <button class="chip" data-target="pep" role="tab" aria-selected="false"><i class="fa-solid fa-shield-virus"></i> Profilaxis VIH</button>
          <button class="chip" data-target="ae" role="tab" aria-selected="false"><i class="fa-solid fa-pills"></i> Anticoncepción de emergencia</button>
        </div>
        <section class="mod-section" data-sec="vac">
          <h4><i class="fa-solid fa-syringe" style="color:#0ea5e9"></i> Vacunas</h4>
          <ul><li><strong>Hepatitis B</strong> y <strong>HBIG</strong> según valoración.</li></ul>
        </section>
        <section class="mod-section is-hidden" data-sec="pep">
          <h4><i class="fa-solid fa-shield-virus" style="color:#22c55e"></i> Profilaxis para VIH</h4>
          <ul>
            <li><strong>Antes de 72 h:</strong> indicada si hay indicación/alto riesgo.</li>
            <li><strong>Después de 72 h:</strong> se clasifica riesgo para decidir.</li>
            <li>No se exige examen previo para iniciar si hay indicación.</li>
          </ul>
        </section>
        <section class="mod-section is-hidden" data-sec="ae">
          <h4><i class="fa-solid fa-pills" style="color:#a855f7"></i> Anticoncepción de emergencia</h4>
          <ul>
            <li><strong>&le; 72 h:</strong> indicada en alto riesgo de embarazo.</li>
            <li><strong>72 h–5 días:</strong> considerar <strong>DIU</strong> si cumple criterios.</li>
          </ul>
        </section>
        <div class="callout" style="margin-top:10px;display:grid;grid-template-columns:28px 1fr;gap:10px;
          padding:10px 12px;border-radius:10px;background:#fff7ed;border:1px solid #fed7aa;color:#7c2d12;">
          <i class="fa-solid fa-circle-exclamation" style="color:#d97706;font-size:1.1rem;margin-top:2px"></i>
          <div><strong>Recuerda:</strong> la IVE es un derecho (C-355/2006, C-055/2022).</div>
        </div>
      </div>
    `,
    // 10
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-calendar-days" style="color:#7c3aed"></i> Acompañamiento continuo y citas de control</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-clipboard-list" style="color:#22c55e"></i> Se programan <strong>seguimientos</strong> desde la primera consulta.</li>
          <li><i class="fa-solid fa-user-shield" style="color:#0ea5e9"></i> Con tu <strong>autorización</strong> pueden contactarte si faltas.</li>
        </ul>
        <h4 style="margin-top:12px"><i class="fa-solid fa-timeline" style="color:#a855f7"></i> Calendario</h4>
        <ol class="timeline">
          <li><span class="badge">1</span> <strong>2 semanas</strong></li>
          <li><span class="badge">2</span> <strong>4 semanas</strong></li>
          <li><span class="badge">3</span> <strong>3 meses</strong></li>
          <li><span class="badge">4</span> <strong>6 meses</strong></li>
          <li><span class="badge">5</span> <strong>12 meses</strong></li>
        </ol>
      </div>
    `,
    // 11
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-people-arrows" style="color:#7c3aed"></i> Activación intersectorial</h3>
        <p>Se te puede derivar (con tu <strong>consentimiento</strong>) a otras entidades.</p>
        <div class="chip-group" role="tablist" aria-label="Rutas de activación">
          <button class="chip is-active" data-target="salud" role="tab" aria-selected="true"><i class="fa-solid fa-user-nurse"></i> Salud</button>
          <button class="chip" data-target="proteccion" role="tab" aria-selected="false"><i class="fa-solid fa-shield-heart"></i> Protección / Niñez</button>
          <button class="chip" data-target="justicia" role="tab" aria-selected="false"><i class="fa-solid fa-scale-balanced"></i> Justicia</button>
        </div>
        <section class="mod-section" data-sec="salud">
          <h4><i class="fa-solid fa-kit-medical" style="color:#22c55e"></i> Profesionales de la salud</h4>
          <ul><li>IVE, salud mental y otras especialidades según el caso.</li></ul>
        </section>
        <section class="mod-section is-hidden" data-sec="proteccion">
          <h4><i class="fa-solid fa-shield" style="color:#0ea5e9"></i> Protección y niñez</h4>
          <ul>
            <li><strong>&lt; 18 años:</strong> Defensor de Familia – ICBF.</li>
            <li><strong>Adultas:</strong> Comisaría de Familia (medidas de protección).</li>
          </ul>
        </section>
        <section class="mod-section is-hidden" data-sec="justicia">
          <h4><i class="fa-solid fa-gavel" style="color:#a855f7"></i> Justicia</h4>
          <ul><li>Articulación con Fiscalía/Policía; asesoría sobre denuncia y cadena de custodia.</li></ul>
        </section>
      </div>
    `,
    // 12
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-heart-circle-check" style="color:#a855f7"></i> Acompañamiento continuo en salud mental</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-user-doctor" style="color:#7c3aed"></i> Seguimiento con <strong>psicología</strong> y salud mental.</li>
          <li><i class="fa-solid fa-people-arrows" style="color:#0ea5e9"></i> Puedes <strong>cambiar de profesional</strong>.</li>
          <li><i class="fa-solid fa-clock" style="color:#22c55e"></i> A tu ritmo y según tus necesidades.</li>
        </ul>
      </div>
    `,
    // 13
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-file-shield" style="color:#0ea5e9"></i> Registro y notificación</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-lock" style="color:#22c55e"></i> Tu información es <strong>confidencial</strong>.</li>
          <li><i class="fa-solid fa-database" style="color:#7c3aed"></i> Reporte estadístico <strong>sin datos personales</strong>.</li>
          <li><i class="fa-solid fa-user-shield" style="color:#f59e0b"></i> Puedes revisar/corregir tu historia clínica.</li>
        </ul>
      </div>
    `,
    // 14
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-hospital-user" style="color:#0ea5e9"></i> Seguimiento institucional</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-user-doctor" style="color:#22c55e"></i> Un profesional de <strong>medicina general</strong> lidera tu caso.</li>
          <li><i class="fa-solid fa-repeat" style="color:#7c3aed"></i> Coordina citas, revisa resultados y actualiza plan.</li>
          <li><i class="fa-solid fa-people-arrows" style="color:#f59e0b"></i> Articula con otras áreas.</li>
        </ul>
      </div>
    `,
    // 15
    `
      <div class="rich-block">
        <h3><i class="fa-solid fa-house-heart" style="color:#22c55e"></i> Cierre del caso</h3>
        <ul class="list-with-icons">
          <li><i class="fa-solid fa-user-doctor" style="color:#0ea5e9"></i> Revisión de resultados y metas.</li>
          <li><i class="fa-solid fa-book-heart" style="color:#a855f7"></i> Educación en derechos sexuales y reproductivos.</li>
          <li><i class="fa-solid fa-handshake-angle" style="color:#7c3aed"></i> Asesoramiento para continuar apoyos si lo requieres.</li>
        </ul>
        <div class="callout" style="margin-top:10px;display:grid;grid-template-columns:28px 1fr;gap:10px;
          padding:10px 12px;border-radius:10px;background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;">
          <i class="fa-solid fa-hands-holding-child" style="color:#16a34a;font-size:1.1rem;margin-top:2px"></i>
          <div><em>No estás sola.</em> Puedes retomar el acompañamiento cuando lo necesites.</div>
        </div>
      </div>
    `
  ];

  const main = document.querySelector("main");
  const placeholder =
    'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800"><rect width="100%" height="100%" fill="%23f3f4f6"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="42" fill="%23999">Imagen no disponible</text></svg>';

  // Helper: inicializa chips/tabs dentro de un contenedor (paso)
  function initChips(container) {
    const chips = container.querySelectorAll(".chip[data-target]");
    const modSections = container.querySelectorAll(".mod-section");
    if (!chips.length || !modSections.length) return;

    const activate = (key) => {
      chips.forEach(c => {
        const active = c.dataset.target === key;
        c.classList.toggle("is-active", active);
        c.setAttribute("aria-selected", active ? "true" : "false");
      });
      modSections.forEach(s => s.classList.toggle("is-hidden", s.dataset.sec !== key));
    };

    const defaultKey =
      (chips[0] && chips[0].dataset.target) ||
      (modSections[0] && modSections[0].dataset.sec);

    chips.forEach(c => c.addEventListener("click", () => activate(c.dataset.target)));
    if (defaultKey) activate(defaultKey);
  }

  // Construye secciones .paso (sin botones; detalles inline)
  pasos.forEach((paso, i) => {
    const sec = document.createElement("section");
    sec.className = "paso";
    sec.innerHTML = `
      <div class="paso-header">
        <i class="fas ${paso.icon}" aria-hidden="true"></i>
        <h2>Paso ${i + 1}</h2>
      </div>
      <div class="paso-grid">
        <div class="paso-media">
          <img data-speed="0.18" alt="Imagen paso ${i + 1}">
        </div>
        <div class="paso-content">
          <p>${paso.text}</p>
          <div class="paso-detalle">
            ${detalles[i] || ""}
          </div>
        </div>
      </div>
    `;

    const img = sec.querySelector("img");
    const tries = [
      `${IMG_BASE}/${i + 1}.png`,
      `${IMG_BASE}/${String(i + 1).padStart(2, "0")}.png`
    ];
    let t = 0;
    const nextSrc = () => { img.src = (t < tries.length) ? tries[t++] : placeholder; };
    img.addEventListener("error", nextSrc);
    nextSrc();

    // Inicializa chips/tabs para este paso (si los tiene)
    initChips(sec);

    main.appendChild(sec);
  });

  // NAV: un punto para HERO (si existe) + pasos
  const sections = Array.from(document.querySelectorAll("section.paso"));
  const targets = hero ? [hero, ...sections] : sections;

  const nav = document.createElement("nav");
  nav.className = "scroll-indicator";
  const ul = document.createElement("ul");
  targets.forEach((el) => {
    const li = document.createElement("li");
    li.addEventListener("click", () => el.scrollIntoView({ behavior: "smooth", block: "start" }));
    ul.appendChild(li);
  });
  nav.appendChild(ul);
  document.body.appendChild(nav);

  // Activa punto actual
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const index = targets.indexOf(entry.target);
        if (entry.isIntersecting && index > -1) {
          const dots = document.querySelectorAll("nav.scroll-indicator li");
          dots.forEach(d => d.classList.remove("active"));
          if (dots[index]) dots[index].classList.add("active");
        }
      });
    },
    { threshold: 0.25, rootMargin: `-${headerOffset}px 0px -35% 0px` }
  );
  targets.forEach(el => observer.observe(el));

  // Parallax suave
  const parallax = () => {
    const vh = window.innerHeight;
    sections.forEach((sec) => {
      const img = sec.querySelector(".paso-media img");
      if (!img) return;
      const r = sec.getBoundingClientRect();
      const progress = (r.top + r.height / 2 - vh / 2) / vh;
      const speed = parseFloat(img.dataset.speed || "0.18");
      const amplitude = 36;
      img.style.transform = `translateY(${(-progress * amplitude * speed).toFixed(2)}px) scale(1.04)`;
    });
  };
  window.addEventListener("scroll", parallax, { passive: true });
  window.addEventListener("resize", parallax);

  // Centrado vertical (se ajusta con contenido más alto)
  function fitSections() {
    const vh = window.innerHeight;
    sections.forEach((sec) => {
      const header = sec.querySelector(".paso-header");
      const grid = sec.querySelector(".paso-grid");
      const inner = header.offsetHeight + grid.offsetHeight;
      const extra = Math.max((vh - inner) / 2, 16);
      sec.style.paddingTop = `${extra}px`;
      sec.style.paddingBottom = `${extra}px`;
    });
    if (hero) hero.style.minHeight = `calc(100vh - ${headerOffset}px)`;
    parallax();
  }
  const revealOnInit = () => { sections.forEach((sec)=>sec.classList.add("visible")); };

  window.addEventListener("load", () => { fitSections(); revealOnInit(); });
  window.addEventListener("resize", () => { fitSections(); revealOnInit(); });
  setTimeout(() => { fitSections(); revealOnInit(); }, 200);
});
