<?php
/**
 * Información estructurada por línea/categoría de cada observatorio.
 *
 * Estructura: [slug_observatorio => [categoria_2_orden => { intro, consulta[], fuentes[], periodicidad, image }]]
 *
 * Estas tarjetas se muestran al inicio del micrositio (debajo del hero) y al
 * hacer click se abre un modal con la información detallada.
 *
 * Si una categoría está en la BD pero no aquí, se usan defaults derivados
 * del catálogo de indicadores.
 */
return [
    'economico' => [
        'Pobreza' => [
            'intro' => 'Mide y monitorea la incidencia de la pobreza multidimensional y monetaria en el departamento, así como las brechas entre cabeceras urbanas y zonas rurales.',
            'consulta' => [
                'Tasa de analfabetismo, bajo logro educativo, inasistencia escolar, rezago escolar',
                'Acceso a servicios de salud, aseguramiento, primera infancia',
                'Condiciones de vivienda, materiales, hacinamiento, servicios públicos',
                'Trabajo informal, trabajo infantil, desempleo de larga duración',
                'Línea de pobreza monetaria, brecha y coeficiente de Gini',
            ],
            'fuentes' => ['DANE — IPM', 'DANE — Pobreza monetaria', 'GEIH'],
            'periodicidad' => 'Anual',
        ],
        'Calidad de vida' => [
            'intro' => 'Caracterización de hogares, viviendas y personas a partir de la Encuesta de Calidad de Vida (ECV) del DANE, con foco en bienestar y condiciones del entorno.',
            'consulta' => [
                'Tipología de hogares y tenencia de vivienda',
                'Acceso a servicios públicos, internet y bienes del hogar',
                'Percepción de pobreza, situación económica y satisfacción con la vida',
                'Salud, educación, nutrición y cuidado de la primera infancia',
            ],
            'fuentes' => ['DANE — Encuesta de Calidad de Vida (ECV)'],
            'periodicidad' => 'Anual',
        ],
        'Calidad de vida campesina' => [
            'intro' => 'Indicadores específicos de hogares y personas que se autorreconocen como campesinos, derivados del módulo especial de la ECV.',
            'consulta' => [
                'Caracterización de hogares campesinos y personas que se identifican como tales',
                'Acceso a servicios públicos, agua y saneamiento básico',
                'Educación, salud y seguridad social en el ámbito rural',
                'Conectividad, satisfacción y autopercepción de pobreza',
            ],
            'fuentes' => ['DANE — ECV módulo hogares campesinos'],
            'periodicidad' => 'Anual',
        ],
        'Agropecuario' => [
            'intro' => 'Monitorea el desempeño del sector agropecuario del departamento: producción, áreas sembradas y cosechadas, inventario pecuario y sacrificio.',
            'consulta' => [
                'Volumen y áreas de producción agrícola por cultivo y municipio',
                'Inventario bovino, bufalino, avícola, porcino, caprino y ovino',
                'Predios pecuarios, granjas tecnificadas y traspatio',
                'Sacrificio bovino y porcino',
            ],
            'fuentes' => ['UPRA — EVA', 'ICA — Censo pecuario', 'FEDEGAN', 'Federación Nacional de Cafeteros'],
            'periodicidad' => 'Anual',
        ],
        'Minería' => [
            'intro' => 'Indicadores de producción, comercio exterior y seguridad del sector minero-energético del departamento.',
            'consulta' => [
                'Producción de hidrocarburos (petróleo y gas) por campo y operador',
                'Producción de minerales (carbón, esmeralda, materiales de construcción)',
                'Exportaciones e importaciones del sector',
                'Accidentalidad minera (eventos, afectados, modalidad)',
            ],
            'fuentes' => ['UPME — SIMCO', 'Agencia Nacional de Hidrocarburos (ANH)', 'Secretaría de Minas de Boyacá'],
            'periodicidad' => 'Mensual / Anual',
        ],
        'Política Fiscal' => [
            'intro' => 'Seguimiento a las finanzas territoriales municipales y departamentales: ingresos, gastos, desempeño fiscal y racionalidad del gasto.',
            'consulta' => [
                'Ingresos totales municipales (corrientes, tributarios, de capital)',
                'Gastos de funcionamiento e inversión',
                'Recursos de capital y regalías',
                'Índice de Desempeño Fiscal (IDF) y Medición de Desempeño Municipal (MDM)',
                'Cuentas por pagar y reservas presupuestales',
            ],
            'fuentes' => ['DNP — CIFFIT', 'DNP — Dirección de Descentralización y Fortalecimiento Fiscal'],
            'periodicidad' => 'Anual',
        ],
        'Turismo' => [
            'intro' => 'Caracterización de los viajeros que visitan el departamento a partir de la Encuesta de Turismo (SITUR) de la Secretaría sectorial.',
            'consulta' => [
                'Origen y destino de los viajeros',
                'Motivo del viaje, tiempo de permanencia y tipo de alojamiento',
                'Presupuesto y nivel de satisfacción',
                'Comportamiento estacional y por provincia',
            ],
            'fuentes' => ['Secretaría de Turismo de Boyacá — SITUR'],
            'periodicidad' => 'Mensual',
        ],
        'Variables Macroecónomicas' => [
            'intro' => 'Indicadores macroeconómicos territoriales como Producto Interno Bruto y el Índice Departamental de Competitividad (IDC).',
            'consulta' => [
                'PIB departamental a precios corrientes y constantes',
                'PIB por habitante (per cápita)',
                'PIB por actividad económica',
                'Índice Departamental de Competitividad (IDC) por factor y pilar',
            ],
            'fuentes' => ['DANE — Cuentas Nacionales Departamentales', 'Consejo Privado de Competitividad — Compite'],
            'periodicidad' => 'Anual',
        ],
    ],

    'social' => [
        'Demografía' => [
            'intro' => 'Proyecciones y estructura de la población del departamento por sexo, edad, área geográfica y ciclo de vida.',
            'consulta' => ['Población total y proyecciones', 'Distribución por sexo y edad', 'Área urbana y rural', 'Ciclo de vida'],
            'fuentes' => ['DANE — Proyecciones de Población'],
            'periodicidad' => 'Anual',
        ],
        'Discapacidad' => [
            'intro' => 'Caracterización de las personas con discapacidad del departamento: registros, deportistas y atención educativa.',
            'consulta' => ['Certificados de discapacidad emitidos', 'Personas con discapacidad por tipo', 'Deportistas con discapacidad', 'Cobertura educativa por etnia y discapacidad'],
            'fuentes' => ['RLCPD — MinSalud', 'Indeportes Boyacá', 'Ministerio de Educación — SIMAT'],
            'periodicidad' => 'Anual',
        ],
        'Educación' => [
            'intro' => 'Indicadores del sistema educativo: cobertura, matrícula, deserción, repitencia, aprobación y resultados de pruebas estandarizadas.',
            'consulta' => ['Cobertura por nivel', 'Matriculados por nivel y por etnia/discapacidad', 'Deserción, repitencia, aprobación y reprobación', 'Resultados Pruebas Saber', 'Infraestructura educativa', 'Programa de Alimentación Escolar (PAE)'],
            'fuentes' => ['Ministerio de Educación Nacional — SIMAT', 'Secretaría de Educación de Boyacá', 'ICFES'],
            'periodicidad' => 'Anual',
        ],
        'Familia' => [
            'intro' => 'Condiciones de vida y bienestar de los hogares boyacenses con énfasis en la familia y pobreza multidimensional por sexo.',
            'consulta' => ['ECV — Encuesta de Calidad de Vida', 'ECV HCAMP — Hogares campesinos', 'Pobreza multidimensional según sexo'],
            'fuentes' => ['DANE — ECV', 'DANE — Censo'],
            'periodicidad' => 'Anual',
        ],
        'Juventud' => [
            'intro' => 'Indicadores de la población joven del departamento que abarcan salud, educación, violencia, derechos humanos y participación.',
            'consulta' => ['Fecundidad en adolescentes', 'Violencias y muertes (Medicina Legal)', 'Mortalidad VIH, materno-infantil', 'Cobertura de afiliación al SGSSS', 'Adolescentes aprehendidos y Consejo de adolescentes'],
            'fuentes' => ['DANE', 'Medicina Legal', 'SIVIGILA', 'ICBF', 'UARIV'],
            'periodicidad' => 'Trimestral / Anual',
        ],
        'Responsabilidad Penal' => [
            'intro' => 'Sistema de Responsabilidad Penal para Adolescentes (SRPA): aprehensiones, sanciones y participación en consejos.',
            'consulta' => ['Adolescentes aprehendidos', 'Ejecución de sanciones ICBF', 'Consejo de adolescentes'],
            'fuentes' => ['ICBF — SRPA', 'Policía Nacional'],
            'periodicidad' => 'Anual',
        ],
        'Salud' => [
            'intro' => 'Indicadores del sistema de salud: mortalidad, morbilidad, cobertura, vacunación, atención materno-infantil y enfermedades transmisibles.',
            'consulta' => ['Mortalidad por causa (VIH, ERA, desnutrición, perinatal, prematura, <5 años)', 'Morbilidad materna extrema (MME)', 'Vacunación y partos atendidos', 'Cobertura SGSSS por edad', 'Enfermedades infecciosas y transmitidas por vector', 'Cáncer infantil y leucemia'],
            'fuentes' => ['MinSalud — SISPRO', 'SIVIGILA', 'Secretaría de Salud de Boyacá'],
            'periodicidad' => 'Mensual / Anual',
        ],
        'Violencia' => [
            'intro' => 'Hechos victimizantes, violencia intrafamiliar, sexual, conflicto armado, feminicidio, suicidio y atenciones médicas asociadas.',
            'consulta' => ['Atenciones médicas por tipo de violencia', 'Intentos de suicidio', 'Víctimas del conflicto armado', 'Feminicidios', 'Violencias y muertes — Medicina Legal', 'Convivencia escolar'],
            'fuentes' => ['Medicina Legal — Forensis', 'UARIV', 'Policía Nacional', 'Sistema de Convivencia Escolar (SIUCE)'],
            'periodicidad' => 'Mensual / Anual',
        ],
    ],

    'genero' => [
        'Demografía y población' => [
            'intro' => 'Caracterización demográfica de la población femenina del departamento.',
            'consulta' => ['Población femenina total y proyecciones', 'Pobreza monetaria y multidimensional por sexo'],
            'fuentes' => ['DANE — Proyecciones de Población', 'DANE — GEIH', 'DANE — ECV'],
            'periodicidad' => 'Anual',
        ],
        'Salud sexual y reproductiva' => [
            'intro' => 'Indicadores de salud sexual y reproductiva de la población femenina del departamento.',
            'consulta' => ['Tasa de fecundidad', 'Indicadores materno-infantiles', 'Mortalidad perinatal y neonatal', 'Cobertura de afiliación al SGSSS por sexo'],
            'fuentes' => ['MinSalud — SISPRO', 'SIVIGILA', 'BDUA'],
            'periodicidad' => 'Mensual / Anual',
        ],
        'Salud y mortalidad' => [
            'intro' => 'Indicadores de mortalidad y salud general con desagregación por sexo.',
            'consulta' => ['Mortalidad prematura por sexo', 'Mortalidad por VIH/SIDA por sexo'],
            'fuentes' => ['Secretaría de Salud de Boyacá', 'SIVIGILA'],
            'periodicidad' => 'Anual',
        ],
        'Educación' => [
            'intro' => 'Indicadores educativos con desagregación por sexo y nivel.',
            'consulta' => ['Repitencia escolar por sexo y nivel', 'Matriculados por nivel y sexo', 'Convivencia escolar por sexo'],
            'fuentes' => ['Ministerio de Educación Nacional (SIMAT)', 'Sistema de Convivencia Escolar (SIUCE)'],
            'periodicidad' => 'Anual',
        ],
        'Discapacidad' => [
            'intro' => 'Caracterización de la población femenina con discapacidad.',
            'consulta' => ['Certificados de discapacidad emitidos a mujeres'],
            'fuentes' => ['RLCPD — MinSalud'],
            'periodicidad' => 'Anual',
        ],
        'Participación política' => [
            'intro' => 'Participación política y comunitaria de las mujeres del departamento.',
            'consulta' => ['Mujeres en juntas de acción comunal', 'Mujeres concejalas electas', 'Mujeres alcaldesas electas', 'Referente mujer en Consejos Territoriales de Planeación'],
            'fuentes' => ['Registraduría Nacional', 'Secretaría de la Mujer y Equidad de Género', 'DNP — CTP'],
            'periodicidad' => 'Cuatrienal',
        ],
        'Empoderamiento económico' => [
            'intro' => 'Programas e iniciativas de autonomía y empoderamiento económico de las mujeres del departamento.',
            'consulta' => ['Programa Mujer Visionaria — proyectos productivos financiados'],
            'fuentes' => ['Secretaría de la Mujer y Equidad de Género de Boyacá'],
            'periodicidad' => 'Anual',
        ],
        'Reintegración y conflicto' => [
            'intro' => 'Mujeres en procesos de desmovilización, reincorporación y víctimas del conflicto armado.',
            'consulta' => ['Mujeres desmovilizadas', 'Mujeres en proceso de reincorporación', 'Mujeres víctimas del conflicto armado'],
            'fuentes' => ['Agencia para la Reincorporación y la Normalización (ARN)', 'Unidad para las Víctimas (UARIV)'],
            'periodicidad' => 'Anual',
        ],
        'Violencias contra la mujer' => [
            'intro' => 'Hechos victimizantes contra las mujeres del departamento: atenciones médicas, casos de feminicidio, intentos de suicidio, lesiones no fatales y muertes violentas.',
            'consulta' => ['Atenciones médicas por violencias contra mujeres', 'Intentos de suicidio en mujeres', 'Casos de feminicidio', 'Lesiones no fatales y muertes violentas (Medicina Legal)'],
            'fuentes' => ['Medicina Legal — Forensis', 'SIVIGILA — violencias', 'Línea 155'],
            'periodicidad' => 'Mensual / Anual',
        ],
    ],
];
