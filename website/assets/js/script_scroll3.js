document.addEventListener("DOMContentLoaded", () => {
    // ===== 12 slides (alternan morado/blanco automáticamente) =====
    const DATA = [
      // 1) Asistencias técnicas municipales (sin imagen: placeholder)
      {
        title: "Asistencias técnicas sobre prevención de VBG",
        image: "",
        html: `
          <p><strong>Propósito.</strong> Fortalecer capacidades de enlaces de mujer y Comisarías de Familia en prevención y atención de violencias basadas en género.</p>
          <p><strong>Descripción.</strong> Asistencia técnica en políticas públicas y estrategias de prevención; actualización de equipos psicosociales para acciones efectivas en el territorio.</p>
          <p><strong>Población beneficiada.</strong> 16 enlaces de mujer y 22 Comisarías de Familia (entre otras instancias municipales).</p>
        `,
        credit: "Componente Mujer – Integración Social"
      },
      // 2) Círculo de la palabra
      {
        title: "Círculo de la palabra",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/CIRCULO DE LA PALABRA/CIRCULO DE LA PALABRA.jpg",
        html: `
          <p>Espacio de <strong>reflexión-acción</strong> para que las mujeres expresen y gestionen emociones, fortalezcan su autoestima y construyan herramientas para afrontar sus contextos.</p>
          <p><strong>Descripción.</strong> Encuentros participativos que promueven escucha, cuidado y reconstrucción de sí mismas a partir de la palabra compartida.</p>
          <p><strong>Población beneficiada.</strong> Aproximadamente 300 mujeres en municipios como Tunja, Guateque, Sotaquirá, Sogamoso, San Miguel de Sema, Cubará, Páez y Saboyá.</p>
        `,
        credit: "Dirección de Mujer e Inclusión Social"
      },
      // 3) Comité promoción y prevención (Mecanismo Articulador)
      {
        title: "Comité de promoción y prevención de violencias",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/Comité de promoción y prevención de violencia contra niñas, niños, adolescentes y mujeres del Mecanismo Articulador/COMITD~1.JPG",
        html: `
          <p>Espacio interinstitucional que <strong>coordina acciones de promoción y prevención</strong> de violencias contra niñas, niños, adolescentes y mujeres en el departamento.</p>
          <p><strong>Descripción.</strong> La Secretaría de Integración Social ejerce la secretaría técnica y articula a entidades departamentales y nacionales (entre ellas, Defensoría, Fiscalía, Policía, Migración y sectoriales de la Gobernación) para impulsar estrategias preventivas, campañas y seguimiento.</p>
          <p><strong>Población beneficiada.</strong> Cobertura departamental (123 municipios).</p>
        `,
        credit: "Mecanismo Articulador – Secretaría Técnica"
      },
      // 4) Descuida yo te cuido
      {
        title: "Descuida yo te cuido",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/ENCUENTRO DEPARTAMENTAL DE VIOLENCIAS/DESCUIDA YO TE CUIDO.jpg",
        html: `
          <p><strong>Objetivo.</strong> Prevenir violencias basadas en género en el <em>sector transporte</em> y visibilizar el rol de las mujeres en un ámbito tradicionalmente masculino.</p>
          <p><strong>Acciones.</strong> Sensibilización con conductores/as de transporte público (individual, urbano e intermunicipal), entrega de material y rutas de atención.</p>
          <p><strong>Alcance.</strong> Trabajo con empresas y gremios del transporte en municipios priorizados.</p>
        `,
        credit: "Componente Mujer – Integración Social"
      },
      // 5) Encuentro Departamental de Violencias
      {
        title: "Encuentro Departamental de Violencias",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/ENCUENTRO DEPARTAMENTAL DE VIOLENCIAS/ENCUENTRO DEPARTAMENTAL DE VIOLENCIAS.jpg",
        html: `
          <p>Jornada departamental <strong>“Proteger no da espera”</strong> para sensibilizar sobre VBG y acordar acciones de prevención con alcaldías, Comisarías de Familia y enlaces municipales.</p>
          <p><strong>Participación.</strong> 400+ personas (alcaldías, comisarías, enlaces de infancia y género) con cobertura a los 123 municipios.</p>
          <p><strong>Resultados.</strong> Transferencia de lineamientos, articulación intersectorial y difusión de rutas de atención.</p>
        `,
        credit: "Gobernación de Boyacá – Integración Social"
      },
      // 6) Escuela de padres
      {
        title: "Escuela de padres para la prevención de violencias",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/Escuela de padres para la prevención de violencias en el contexto familiar/Escuela de padres para la prevención de violencias en el contexto familiar.jpg",
        html: `
          <p><strong>Descripción.</strong> Estrategia en instituciones educativas para <strong>fortalecer dinámicas familiares</strong> y prevenir violencias basadas en género en el hogar.</p>
          <p><strong>Población beneficiada:</strong> 526 personas.</p>
          <p><strong>Municipios:</strong> Ventaquemada, Oicatá, Otanche, Puerto Boyacá, Arcabuco, Sutamarchán.</p>
        `,
        credit: "Componente Mujer – Integración Social"
      },
      // 7) Fondo de Incentivo – Autonomía económica (sin imagen: placeholder)
      {
        title: "Fondo de Incentivo para la Autonomía Económica",
        image: "",
        html: `
          <p>Instrumento que brinda <strong>apoyo material a emprendimientos</strong> liderados por mujeres para fortalecer su autonomía económica.</p>
          <p>Incluye iniciativas aprobadas en múltiples municipios (Güicán, El Cocuy, Sogamoso, Tunja, Duitama, Paipa, Puerto Boyacá, etc.), con cobertura departamental.</p>
        `,
        credit: "Dirección de Mujer e Inclusión Social"
      },
      // 8) Fortalecimiento a Comisarías de Familia
      {
        title: "Fortalecimiento a comisarías para prevención de violencias",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/Fortalecimiento a comisarías de familia para la prevención de violencias/2. NO NOS CALLAMOS MAS.jpg",
        html: `
          <p><strong>Objetivo.</strong> Mejorar la <strong>capacidad instalada y técnica</strong> de comisarías de familia (dotación, asistencia técnica y priorización por indicadores de riesgo).</p>
          <p><strong>Población beneficiada:</strong> 10 comisarías de familia.</p>
          <p><strong>Municipios priorizados (ejemplos):</strong> Tunja, Sogamoso, Chiquinquirá, Puerto Boyacá, Toca, Siachoque, Soracá, Tuta, Sotaquirá, Cómbita, Ventaquemada, entre otros.</p>
        `,
        credit: "Secretaría de Integración Social – OIM (memorando de entendimiento)"
      },
      // 9) No nos callamos más
      {
        title: "Estrategia No nos callamos más",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/NO NOS CALLAMOS MAS/1. NO NOS CALLAMOS MAS.jpg",
        html: `
          <p>Estrategia para <strong>prevenir</strong> y <strong>responder</strong> a la violencia contra las mujeres con formación a instancias municipales/departamentales y fortalecimiento de redes comunitarias.</p>
          <p><strong>Población beneficiada:</strong> 1.610 personas.</p>
          <p><strong>Municipios:</strong> Tunja, Guateque, Sotaquirá, Sogamoso, San Miguel de Sema, Jenesano, Paipa, Duitama, Chiquinquirá, Santa Rosa de Viterbo, Nobsa, Aquitania, Cocuy, Puerto Boyacá, entre otros.</p>
        `,
        credit: "Componente Mujer – Gobernación de Boyacá"
      },
      // 10) Prevención de violencias en el ámbito laboral
      {
        title: "Prevención de violencias en el ámbito laboral",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/PREVENCION DE VIOLENCIAS EN EL AMBITO LABORAL/PREVENCION DE VIOLENCIAS EN EL AMBITO LABORAL.jpg",
        html: `
          <p>Proceso de <strong>sensibilización y formación</strong> para prevenir violencias basadas en género en entidades públicas y privadas.</p>
          <p><strong>Población beneficiada:</strong> 748 personas.</p>
          <p><strong>Municipios:</strong> Tunja y Santa Rosa de Viterbo.</p>
        `,
        credit: "Dirección de Mujer e Inclusión Social"
      },
      // 11) Es REAL Y MATA
      {
        title: "Prevención de violencias – Es REAL Y MATA",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/Prevención de violencias Estrategia Es REAL Y MATA/LOGO ES REAL Y MATA.jpg",
        html: `
          <p><strong>Descripción.</strong> Estrategia 2025 para <strong>prevenir y responder</strong> a violencias contra las mujeres mediante <em>capacitaciones, sensibilización y fortalecimiento</em> de instancias municipales y departamentales, con enfoque de <strong>derechos humanos</strong> y <strong>diferencial</strong>. Impulsa redes y organizaciones comunitarias y difunde contenidos libres de sexismo y VBG.</p>
          <p><strong>Población beneficiada:</strong> 495 personas.</p>
          <p><strong>Municipios:</strong> Puerto Boyacá, San Miguel de Sema, Motavita y Samacá.</p>
        `,
        credit: "Componente Mujer – Gobernación de Boyacá"
      },
      // 12) Tour Violeta
      {
        title: "Tour Violeta – sensibilización y acción",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS/TOUR VIOLETA/LOGO TOUR VIOLETA.jpg",
        html: `
          <p>Herramienta itinerante para <strong>visibilizar la VBG</strong>, generar <strong>diálogo comunitario</strong> y articular acciones con instituciones y organizaciones sociales. Incluye talleres, capacitaciones y actividades de sensibilización para promover la denuncia y la prevención.</p>
          <p><strong>Población beneficiada:</strong> 1.234 personas.</p>
          <p><strong>Municipios:</strong> Sogamoso, Tunja, Duitama, Aquitania, Paipa, Moniquirá, Chiquinquirá, Toca, Puerto Boyacá, Oicatá, Ventaquemada, Otanche, Arcabuco, Sutamarchán, Garagoa y El Espino.</p>
        `,
        credit: "Dirección de Mujer e Inclusión Social"
      }
    ];
  
    // ===== Elementos =====
    const track   = document.getElementById("sliderTrack");
    const dotsBox = document.getElementById("sliderDots");
    const btnPrev = document.getElementById("btnPrev");
    const btnNext = document.getElementById("btnNext");
    const slider  = document.getElementById("slider");
  
    // ===== Render =====
    const slideTpl = ({ title, image, html, credit }, idx) => {
      const themeClass = (idx % 2 === 0) ? "slide--purple" : "slide--white"; // alterna morado/blanco
      return `
        <section class="slide ${themeClass}">
          <figure class="slide-media">
            <img src="${image || ""}" alt="${title || ""}" loading="lazy">
          </figure>
          <div class="slide-body">
            <h2 class="slide-title">${title || ""}</h2>
            <div class="slide-html">${html || ""}</div>
            <div class="slide-credit"><i class="fa-solid fa-circle-info"></i> ${credit || ""}</div>
          </div>
        </section>
      `;
    };
  
    track.innerHTML = DATA.map(slideTpl).join("");
    dotsBox.innerHTML = DATA
      .map((_,i)=>`<button class="slider-dot${i===0?' is-active':''}" data-i="${i}" aria-label="Ir al slide ${i+1}"></button>`)
      .join("");
  
    // ===== Carrusel básico =====
    let i = 0;
    const n = DATA.length;
    const clamp = (v, min, max) => Math.min(Math.max(v, min), max);
  
    function goTo(idx){
      i = clamp(idx, 0, n-1);
      track.style.transform = `translateX(-${i*100}%)`;
      dotsBox.querySelectorAll(".slider-dot").forEach((d,k)=>d.classList.toggle("is-active", k===i));
      btnPrev.disabled = (i===0);
      btnNext.disabled = (i===n-1);
    }
  
    btnPrev.addEventListener("click", ()=> goTo(i-1));
    btnNext.addEventListener("click", ()=> goTo(i+1));
    dotsBox.addEventListener("click", (e)=>{
      const dot = e.target.closest(".slider-dot");
      if(!dot) return;
      goTo(parseInt(dot.dataset.i,10));
    });
  
    // Teclado
    document.addEventListener("keydown", (e)=>{
      if (e.key === "ArrowLeft")  goTo(i-1);
      if (e.key === "ArrowRight") goTo(i+1);
    });
  
    // Swipe móvil
    let startX = null, deltaX = 0, swiping = false;
    const THRESHOLD = 45;
    function onStart(e){ const t = e.touches ? e.touches[0] : e; startX = t.clientX; deltaX = 0; swiping = true; track.style.transition = "none"; }
    function onMove(e){
      if(!swiping) return;
      const t = e.touches ? e.touches[0] : e;
      deltaX = t.clientX - startX;
      const pct = (deltaX / slider.clientWidth) * 100;
      track.style.transform = `translateX(calc(-${i*100}% + ${pct}%))`;
    }
    function onEnd(){
      if(!swiping) return;
      track.style.transition = "";
      if (Math.abs(deltaX) > THRESHOLD){ if (deltaX < 0) goTo(i+1); else goTo(i-1); } else { goTo(i); }
      startX = null; deltaX = 0; swiping = false;
    }
    slider.addEventListener("pointerdown", onStart);
    slider.addEventListener("pointermove", onMove);
    slider.addEventListener("pointerup", onEnd);
    slider.addEventListener("pointerleave", onEnd);
    slider.addEventListener("touchstart", onStart, {passive:true});
    slider.addEventListener("touchmove", onMove, {passive:true});
    slider.addEventListener("touchend", onEnd);
  
    // Fallback de imagen (si alguna ruta falla)
    track.querySelectorAll(".slide-media img").forEach(img=>{
      img.addEventListener("error", ()=> { img.src = "https://placehold.co/1200x800?text=Imagen"; });
    });
  
    // Init
    goTo(0);
  });
  