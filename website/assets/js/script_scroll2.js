document.addEventListener("DOMContentLoaded", () => {
    // ==== 4 slides; alternan morado/blanco/morado/blanco ====
    const DATA = [
      {
        theme: "purple",
        title: "Asistencias técnicas con enfoque diverso",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS DE LIDERAZGO Y PARTICIPACION POLITICA/ASISTENCIA TECNICA EN ENFOQUE DIVERSO/ASISTENCIA TECNICA EN ENFOQUE DIVERSO.jpg",
        html: `
          <p>Asistencias técnicas con enfoque de diversidad a municipios del Departamento, con el objetivo de incorporar el enfoque de diversidad en la gestión territorial y de garantizar entornos inclusivos y libres de discriminación.</p>
          <p>Se propone la inclusión de la diversidad desde el fortalecimiento de capacidades institucionales, la sensibilización, y la adopción de medidas afirmativas que permitan desarrollar políticas, programas y proyectos que reconozcan las identidades, orientaciones y expresiones diversas.</p>
          <p>Se plantea el enfoque de diversidad como perspectiva que reconoce la pluralidad humana, las diferencias como valor social y la necesidad de eliminar barreras estructurales que reproducen desigualdades.</p>
          <p>Se realizó acompañamiento técnico en municipios priorizados del departamento, abordando normatividad vigente, herramientas conceptuales, diagnósticos participativos y planes de acción.</p>
          <p>Se recomienda continuar con procesos de formación continuada, transversalización institucional y articulación con organizaciones sociales para fortalecer el enfoque en los ámbitos educativo, laboral, salud, justicia y participación política.</p>
          <p><strong>Municipios atendidos:</strong> Soatá, Susacón, Ventaquemada, Toca, Cómbita, Arcabuco, Paz de Río, Tasco, Tunja, Sutamarchán, Samacá, Pajarito, Labranzagrande, Paya, Pisba, Tinjacá, El Espino, Otanche y Viracachá.</p>
        `,
        credit: "Dirección de Mujer e Inclusión Social"
      },
      {
        theme: "white",
        title: "8 de marzo – Conmemoración",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS DE LIDERAZGO Y PARTICIPACION POLITICA/CONMEMORACION DIA DE LA MUJER/8-marzo.jpg",
        html: `
          <p>La conmemoración del 8 de marzo en Boyacá busca visibilizar los derechos de las mujeres, promover la igualdad y prevenir todas las formas de violencias basadas en género.</p>
          <p>Se desarrollaron actividades pedagógicas, actos simbólicos y jornadas de sensibilización, con participación de instituciones, organizaciones sociales y comunidad en general.</p>
          <p>La agenda incluyó foros, talleres, ferias de servicios, piezas comunicativas y acciones públicas de reconocimiento de la labor de las mujeres en el territorio.</p>
          <p>Se hizo énfasis en la garantía de derechos, la autonomía económica, el liderazgo y la participación política de las mujeres.</p>
        `,
        credit: "Dirección de Mujer e Inclusión Social"
      },
      {
        theme: "purple",
        title: "Estrategia para la prevención del bullying diverso – Transformar",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS DE LIDERAZGO Y PARTICIPACION POLITICA/ESTRATEGIA PEREVENCION DE BULLYING/ESTRATEGIA PARA LA PREVENCION DEL BULLYING DIVERSO – TRANSFORMAR.jpg",
        html: `
          <p>La estrategia <strong>Transformar</strong> promueve entornos escolares y comunitarios seguros e incluyentes, libres de bullying y discriminación por orientación sexual, identidad y expresión de género.</p>
          <p>Aborda componentes de sensibilización, formación y activación de rutas, trabajando con directivos, docentes, estudiantes, familias e instancias locales.</p>
          <p>Incluye herramientas pedagógicas, protocolos de atención, ejercicios de reconocimiento y acciones de convivencia escolar.</p>
          <p>Se enfatiza el cumplimiento de la normativa nacional, la protección de niños, niñas y adolescentes y la transversalización del enfoque de diversidad en la vida escolar.</p>
        `,
        credit: "Dirección de Mujer e Inclusión Social"
      },
      {
        theme: "white",
        title: "IMPARABLES – Mujeres lideresas",
        image: "assets/pdf/asuntosGenero/publicaciones/ESTRATEGIAS MUJER Y GENERO/ESTRATEGIAS DE LIDERAZGO Y PARTICIPACION POLITICA/IMPARABLES /ESCUELA PARTICCIPACION POLITICA IMPARABLES.jpg",
        html: `
          <p><strong>Imparables</strong> es una escuela diseñada para fortalecer las capacidades de liderazgo y participación política de mujeres en el departamento.</p>
          <p>Trabaja en habilidades para la incidencia, la comunicación estratégica, el conocimiento de rutas y normativas, la formulación de agendas y la articulación con instituciones.</p>
          <p>Promueve la participación paritaria, la eliminación de violencias políticas y la construcción de redes de apoyo y sororidad.</p>
          <p>Incluye sesiones formativas, mentorías, acompañamiento técnico y espacios de intercambio de experiencias entre lideresas.</p>
        `,
        credit: "Dirección de Mujer e Inclusión Social"
      }
    ];
  
    // ==== Elementos ====
    const track   = document.getElementById("sliderTrack");
    const dotsBox = document.getElementById("sliderDots");
    const btnPrev = document.getElementById("btnPrev");
    const btnNext = document.getElementById("btnNext");
    const slider  = document.getElementById("slider");
  
    // ==== Render ====
    const slideTpl = ({ theme, title, image, html, credit }) => {
      const themeClass = theme === "purple" ? "slide--purple" : "slide--white";
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
  
    // Dots
    dotsBox.innerHTML = DATA
      .map((_,i)=>`<button class="slider-dot${i===0?' is-active':''}" data-i="${i}" aria-label="Ir al slide ${i+1}"></button>`)
      .join("");
  
    // ==== Carrusel básico ====
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
  
    // Teclas
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
  