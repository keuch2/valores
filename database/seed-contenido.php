<?php
/**
 * seed-contenido.php — Puebla la BD con el contenido real extraído del sitio
 * estático original. Idempotente: actualiza por slug/clave, no duplica.
 *
 * Uso:  php database/seed-contenido.php
 */

declare(strict_types=1);

require __DIR__ . '/../includes/core/bootstrap.php';
$pdo = db();

/** Helper: HTML de una lista de features [icono,titulo,desc]. */
function feats(array $items): string
{
    $h = '<div class="srv-features">';
    foreach ($items as $f) {
        $h .= '<div class="srv-feature"><h4>' . htmlspecialchars($f[1]) . '</h4><p>' . htmlspecialchars($f[2]) . '</p></div>';
    }
    return $h . '</div>';
}

// ---------------------------------------------------------------------------
//  SERVICIOS — contenido de detalle (corrigiendo los typos del original)
// ---------------------------------------------------------------------------
$servicios = [
    'intermediacion-bursatil' => [
        'desc' => 'Compra y vende valores en la Bolsa de Valores y Productos de Asunción con el respaldo de nuestros corredores especializados y más de 33 años de experiencia operativa.',
        'sub'  => 'Operá en la Bolsa de Valores y Productos de Asunción con el respaldo de los corredores más experimentados del mercado paraguayo.',
        'contenido' =>
            '<h2>Accede al Mercado de Valores con los mejores</h2>' .
            '<p>La intermediación bursátil es el servicio a través del cual compras y vendes valores mobiliarios —acciones, bonos, CDAs, pagarés— en el mercado organizado de la Bolsa de Valores y Productos de Asunción (BVA).</p>' .
            '<p>Como agente habilitado por la Superintendencia de Valores (SIV) del Banco Central del Paraguay, Valores Casa de Bolsa actúa en nombre de sus clientes ejecutando órdenes con agilidad, transparencia y al mejor precio disponible en el mercado.</p>' .
            '<p>Tanto si sos un inversor individual como una empresa o institución, nuestros corredores te acompañan con criterio, experiencia y una mirada estratégica sobre el mercado local.</p>' .
            '<h3>¿Por qué operar con Valores?</h3>' .
            feats([
                ['', '33+ años de experiencia', 'La trayectoria más sólida del mercado bursátil paraguayo. Conocemos cada instrumento, emisor y dinámica del mercado local.'],
                ['', 'Regulados por la SIV', 'Operamos bajo el marco normativo más estricto. Tu inversión está protegida por la regulación vigente del Mercado de Valores.'],
                ['', 'Ejecución ágil', 'Órdenes ejecutadas con rapidez y al mejor precio. Acceso directo al sistema de negociación de la BVA.'],
                ['', 'Asesoramiento personalizado', 'Tu corredor de bolsa te conoce, entiende tus objetivos y te propone estrategias adaptadas a tu situación específica.'],
                ['', 'Información de mercado', 'Acceso a reportes, análisis de mercado y boletines de oportunidades preparados por nuestro equipo de analistas.'],
                ['', 'Mercado secundario activo', 'Facilitamos la compra y venta de instrumentos ya emitidos, dándote la liquidez que necesitas cuando la necesitas.'],
            ]),
    ],
    'mercado-internacional' => [
        'desc' => 'Accede a los mercados financieros globales a través de nuestra alianza con INVIU. ETFs, acciones y bonos internacionales desde Paraguay.',
        'sub'  => 'Accede a los mercados financieros globales desde Paraguay. Diversifica tu portafolio con activos de Estados Unidos y el mundo a través de nuestra alianza con INVIU.',
        'contenido' =>
            '<h2>El mundo entero, desde Paraguay</h2>' .
            '<p>A través de nuestra alianza con INVIU —plataforma líder de inversión regional— en Valores puedes acceder a los principales mercados financieros del mundo: NYSE, NASDAQ, mercados europeos y más.</p>' .
            '<p>Esta integración te permite diversificar geográficamente, protegerte de riesgos locales y aprovechar oportunidades en economías más grandes y desarrolladas, todo desde una sola cuenta administrada por tu asesor de confianza en Valores.</p>' .
            '<h3>Qué puedes invertir desde Paraguay</h3>' .
            feats([
                ['', 'Acciones USA y globales', 'Apple, Microsoft, Amazon, Tesla y miles de empresas más. Invierte directo en el NYSE y NASDAQ con bajas comisiones.'],
                ['', 'ETFs diversificados', 'Fondos indexados que replican mercados enteros: S&P 500, NASDAQ 100, mercados emergentes, sectores específicos.'],
                ['', 'Bonos internacionales', 'Deuda soberana y corporativa de mercados desarrollados. Renta fija en USD con riesgo crediticio diversificado.'],
                ['', 'REITs — Inmobiliario', 'Fondos de inversión inmobiliaria cotizados en bolsa. Exposición al mercado inmobiliario global con liquidez diaria.'],
                ['', 'Tesoros USA (T-Bills)', 'Letras del Tesoro americano. La inversión más segura del mundo con rendimientos atractivos en dólares.'],
                ['', 'Mercados emergentes', 'Exposición a economías en desarrollo de Asia, América Latina y África con alto potencial de crecimiento.'],
            ]),
    ],
    'analisis-financiero' => [
        'desc' => 'Toma decisiones respaldadas por análisis rigurosos. Reportes sectoriales, perspectivas macro y estrategias de portafolio elaboradas por nuestro equipo de analistas.',
        'sub'  => 'Decisiones de inversión respaldadas por análisis rigurosos, reportes sectoriales y perspectivas macroeconómicas elaboradas por nuestro equipo de analistas especializados.',
        'contenido' =>
            '<h2>Información de calidad para decidir con confianza</h2>' .
            '<p>El mercado de capitales es dinámico, complejo y lleno de variables. Para invertir bien, no basta con tener acceso a los instrumentos: necesitas entender qué está pasando, por qué y qué podría pasar.</p>' .
            '<p>Nuestro equipo de analistas produce reportes de mercado, análisis de emisores, perspectivas macroeconómicas y estrategias de portafolio que te dan una ventaja real al momento de tomar decisiones.</p>' .
            '<p>Este servicio está disponible tanto para inversores individuales como para instituciones y empresas que necesitan información financiera de calidad para su planificación estratégica.</p>' .
            feats([
                ['', 'Análisis de mercado local', 'Seguimiento semanal del mercado bursátil paraguayo: volúmenes, tasas, emisores activos y tendencias.'],
                ['', 'Perspectivas macroeconómicas', 'Análisis del contexto económico paraguayo e internacional: tipo de cambio, inflación, tasas de referencia y política monetaria.'],
                ['', 'Análisis de emisores', 'Due diligence financiero de empresas y municipios emisores: solidez, riesgo crediticio y calidad de la gestión.'],
                ['', 'Estrategias de portafolio', 'Recomendaciones de asignación de activos según perfil de riesgo, horizonte temporal y objetivos del inversor.'],
            ]),
    ],
    'app' => [
        'desc' => 'Estructuramos iniciativas privadas y proyectos de APP para conectar el sector público con la inversión privada de forma eficiente, transparente y sostenible.',
        'sub'  => 'Conectamos el sector público con la inversión privada mediante estructuras financieras eficientes, transparentes y alineadas con el desarrollo económico del Paraguay.',
        'contenido' =>
            '<h2>Infraestructura y desarrollo con capital privado</h2>' .
            '<p>Las Asociaciones Público-Privadas (APP) y las Iniciativas Privadas (IP) son mecanismos legales que permiten que empresas privadas financien, construyan y gestionen proyectos de interés público en Paraguay.</p>' .
            '<p>Valores Casa de Bolsa actúa como estructurador financiero y asesor en estos procesos, ayudando tanto al sector público a captar inversión como a los inversores privados a identificar oportunidades rentables y seguras.</p>' .
            '<p>Nuestro equipo tiene experiencia en proyectos de infraestructura vial, saneamiento, telecomunicaciones, salud y educación, acompañando cada etapa desde el análisis de viabilidad hasta el cierre financiero.</p>' .
            feats([
                ['', 'Infraestructura y obras públicas', 'Rutas, puertos, hospitales, escuelas. Proyectos de gran escala con participación del mercado de capitales.'],
                ['', 'Saneamiento y servicios básicos', 'Proyectos de agua potable, alcantarillado y gestión de residuos financiados mediante estructuras APP.'],
                ['', 'Energía y telecomunicaciones', 'Estructuración de proyectos energéticos y de conectividad con participación del sector privado.'],
                ['', 'Iniciativas Privadas', 'Propuestas de inversión presentadas por privados al Estado. Asesoramos en estructuración, presentación y negociación.'],
            ]),
    ],
    'estructuracion-fiduciaria' => [
        'desc' => 'Pioneros en fideicomisos y titularización en Paraguay. Diseñamos estructuras eficientes, seguras y transparentes para empresas, municipios e inversores institucionales.',
        'sub'  => 'Pioneros en fideicomisos y titularización en Paraguay. Diseñamos estructuras eficientes, seguras y transparentes para empresas, municipios e inversores institucionales.',
        'contenido' =>
            '<h2>Los pioneros del fideicomiso en Paraguay</h2>' .
            '<p>Desde 1997, Valores Casa de Bolsa fue la primera firma en desarrollar estructuras fiduciarias en Paraguay. Ese hito marcó el inicio de un segmento que hoy es fundamental para el financiamiento corporativo y municipal del país.</p>' .
            '<p>La titularización, el fideicomiso de garantía, el fideicomiso de administración y los fideicomisos de emisión son instrumentos que hemos perfeccionado a lo largo de décadas de experiencia real en el mercado local.</p>' .
            '<p>Con más de 100 estructuras desarrolladas, somos la referencia técnica y operativa para cualquier entidad que busque acceder al mercado de capitales mediante una estructura fiduciaria.</p>' .
            '<h3>Instrumentos fiduciarios que ofrecemos</h3>' .
            feats([
                ['', 'Fideicomiso de Garantía', 'Garantías reales sobre activos para respaldar obligaciones crediticias o de mercado. Mayor seguridad para emisores e inversores.'],
                ['', 'Fideicomiso de Administración', 'Gestión profesional de activos bajo una estructura de separación patrimonial que protege los fondos de terceros.'],
                ['', 'Fideicomiso de Emisión', 'Estructuración de emisiones de valores mobiliarios bajo un vehículo fiduciario para empresas privadas y entidades públicas.'],
                ['', 'Titularización de Activos', 'Conversión de flujos futuros o activos ilíquidos en instrumentos negociables en el mercado de capitales.'],
            ]),
    ],
    'reportes' => [
        'desc' => 'Boletines semanales, análisis de renta fija y variable, y reportes especiales. Información de calidad para inversores individuales e institucionales.',
        'sub'  => 'Boletines semanales, análisis sectoriales y reportes especiales elaborados por nuestro equipo para mantenerte siempre informado del mercado de capitales paraguayo.',
        'contenido' =>
            '<h2>Nuestras publicaciones de mercado</h2>' .
            feats([
                ['', 'Boletín Semanal BVA', 'Resumen semanal de la actividad de la Bolsa de Valores: volumen negociado, emisores activos, tasas de renta fija y movimientos de renta variable. Publicación semanal.'],
                ['', 'Monitor Macro Paraguay', 'Análisis mensual de las principales variables macroeconómicas del Paraguay: PIB, inflación, tipo de cambio, tasas de interés y perspectivas del BCP.'],
                ['', 'Análisis de Emisores', 'Informes especiales sobre nuevas emisiones: ficha técnica del instrumento, calificación crediticia, análisis del emisor y recomendación.'],
                ['', 'Oportunidades Destacadas', 'Boletín quincenal de oportunidades de inversión identificadas por nuestros analistas, con relación riesgo/retorno destacada.'],
                ['', 'Perspectivas Internacionales', 'Análisis mensual de los mercados globales: Fed, BCE, commodities y mercados emergentes, y su impacto en las estrategias desde Paraguay.'],
            ]),
    ],
];

$upd = $pdo->prepare('UPDATE servicios SET descripcion_corta = :d, contenido = :c WHERE slug = :s');
foreach ($servicios as $slug => $s) {
    $upd->execute([':d' => $s['desc'], ':c' => $s['contenido'], ':s' => $slug]);
}
echo 'Servicios actualizados: ' . count($servicios) . "\n";

// ---------------------------------------------------------------------------
//  EJECUTIVOS — bio real
// ---------------------------------------------------------------------------
$ejecutivos = [
    'Diego Christian Borja Terán'      => 'Fundador y presidente de Valores Casa de Bolsa. Más de 30 años liderando operaciones bursátiles y estructuraciones fiduciarias en el mercado paraguayo.',
    'Gustavo Mathias Angulo Turitich'  => 'Vicepresidente ejecutivo con amplia trayectoria en el desarrollo de productos financieros, emisiones de deuda y expansión de la cartera institucional.',
    'Yanina Monges Chávez'             => 'Directora titular con especialización en estructuración fiduciaria, cumplimiento regulatorio y gestión de riesgos. Referente del sector financiero femenino paraguayo.',
];
$ue = $pdo->prepare('UPDATE ejecutivos SET bio = :b WHERE nombre = :n');
foreach ($ejecutivos as $nombre => $bio) {
    $ue->execute([':b' => $bio, ':n' => $nombre]);
}
echo 'Ejecutivos actualizados: ' . count($ejecutivos) . "\n";

// ---------------------------------------------------------------------------
//  FAQS — 16 reales (limpia las 2 de ejemplo del seed y carga las reales)
// ---------------------------------------------------------------------------
$pdo->exec("DELETE FROM faqs");
$faqs = [
    ['bono', '¿Cuál es el monto mínimo para invertir en bonos?', 'El monto mínimo para invertir en bonos es de Gs. 1.000.000 para emisiones en guaraníes y USD 1.000 para emisiones en dólares. Nuestro equipo puede asesorarle para encontrar la alternativa más adecuada según su perfil de inversión.'],
    ['bono', '¿Qué pasa si necesito el dinero antes del vencimiento?', 'En caso de necesitar liquidez antes del vencimiento, Valores Casa de Bolsa puede asesorarle en la negociación y venta de sus bonos en el mercado secundario, acompañándole durante todo el proceso.'],
    ['bono', '¿Los bonos tienen garantía?', 'Existen bonos con garantía y bonos sin garantía, dependiendo de la estructura de cada emisión. Todas las emisiones cuentan con calificación de riesgo y son evaluadas según el perfil financiero de la entidad emisora.'],
    ['cda', '¿Los CDAs tienen garantía?', 'Sí. Los CDAs cuentan con una garantía máxima equivalente a 75 salarios mínimos vigentes, conforme a la normativa vigente del sistema financiero paraguayo. Esto los convierte en uno de los instrumentos de inversión más seguros del mercado.'],
    ['cda', '¿Cuál es el monto mínimo para invertir en un CDA?', 'El monto mínimo es desde USD 1.000 (o su equivalente en guaraníes). Nuestro equipo puede asesorarte para encontrar la entidad, el plazo y la tasa que mejor se ajusten a tu perfil.'],
    ['cda', '¿Puedo retirar mi dinero antes del vencimiento?', 'El CDA es un instrumento a plazo fijo. La cancelación anticipada depende de las condiciones de cada entidad emisora y puede implicar una reducción en la tasa de interés pactada. Tu asesor te explicará las condiciones específicas antes de invertir.'],
    ['contacto', '¿Cuánto tiempo tarda en activarse mi cuenta?', 'Una vez que envías toda la documentación requerida, el proceso de activación toma entre 24 y 48 horas hábiles. Un asesor te acompañará en cada paso del proceso.'],
    ['contacto', '¿Tienen sucursales en el interior del país?', 'Nuestras oficinas principales están en Asunción, pero atendemos clientes de todo el país. Para clientes del interior, realizamos todo el proceso de apertura de forma digital y ofrecemos atención remota vía videollamada, WhatsApp y email.'],
    ['contacto', '¿Puedo invertir si soy extranjero residente en Paraguay?', 'Sí. Los extranjeros con residencia en Paraguay pueden abrir cuentas de inversión presentando su documento de identidad extranjero, comprobante de residencia y demás documentación. Consulta con nuestro equipo los requisitos específicos para tu caso.'],
    ['contacto', '¿Tienen número mínimo de inversión?', 'No hay un mínimo único. Cada instrumento tiene su propio mínimo. Los CDAs pueden empezar desde USD 1.000, los bonos desde USD 3.000-5.000 según la emisión, y las acciones desde los montos de negociación del mercado. Para el mercado internacional, el mínimo inicial es de USD 1.000.'],
    ['contacto', '¿Cómo sé que mi dinero está seguro?', 'Valores Casa de Bolsa es una entidad regulada por la Superintendencia de Valores (SIV) del Banco Central del Paraguay y autorizada por la Bolsa de Valores y Productos de Asunción. Tus valores son custodiados en el sistema de la BVA, separados del patrimonio de Valores.'],
    ['contacto', '¿Cómo me comunico con mi asesor?', 'Una vez que abres tu cuenta, te asignamos un asesor personal que puedes contactar directamente por teléfono, WhatsApp o email. También puedes programar reuniones presenciales o virtuales según tu preferencia.'],
    ['inter', '¿Necesito abrir una cuenta en el exterior?', 'No. A través de la plataforma INVIU y tu cuenta en Valores, puedes operar en mercados internacionales sin necesidad de abrir cuentas en bancos extranjeros. Toda la operativa se gestiona desde Paraguay con tu asesor de Valores.'],
    ['inter', '¿Cuál es el monto mínimo para empezar?', 'El monto mínimo para invertir en cualquier bono del mercado internacional es de USD 1.000. Para estrategias diversificadas con ETFs o acciones fraccionadas, nuestro equipo puede asesorarte según tu perfil y objetivos.'],
    ['inter', '¿Qué riesgos tengo al invertir internacionalmente?', 'Los principales riesgos son: volatilidad de mercado, riesgo de tipo de cambio (si inviertes en USD y el guaraní se aprecia) y riesgo de mercado específico. Tu asesor te ayuda a diversificar para minimizar estos riesgos.'],
    ['inter', '¿Cómo retiro mis ganancias?', 'Puedes vender tus posiciones en cualquier momento y los fondos se acreditan en tu cuenta. La liquidación internacional es de T+2 (dos días hábiles). Los fondos se transfieren a tu cuenta bancaria en Paraguay en los plazos acordados.'],
];
$if = $pdo->prepare('INSERT INTO faqs (pregunta, respuesta, categoria, orden, activo) VALUES (:p, :r, :c, :o, 1)');
foreach ($faqs as $i => $f) {
    $if->execute([':p' => $f[1], ':r' => $f[2], ':c' => $f[0], ':o' => $i + 1]);
}
echo 'FAQs cargadas: ' . count($faqs) . "\n";

// ---------------------------------------------------------------------------
//  BLOQUES DE CONTENIDO editables (nosotros)
// ---------------------------------------------------------------------------
$secciones = [
    'nosotros_intro' => ['Una firma con propósito, con ética y con experiencia',
        '<p>En Valores trabajamos todos los días para acercar el mercado de capitales a las personas y a las empresas que quieren crecer de manera sólida y sostenible.</p>' .
        '<p>Somos agentes organizadores y estructuradores de emisiones de acciones y títulos de deuda para entidades privadas y municipales a nivel nacional, acompañando cada proyecto con seriedad, conocimiento y una mirada estratégica.</p>' .
        '<p>Fuimos pioneros en el desarrollo de estructuras fiduciarias en Paraguay e impulsores de la figura de la titularización, marcando hitos que ayudaron a fortalecer y modernizar el Mercado de Valores del país.</p>' .
        '<p>Con más de 33 años de trayectoria, diseñamos opciones de inversión a medida, entendiendo que detrás de cada inversor y cada emisor hay objetivos, desafíos y proyectos únicos.</p>'],
    'nosotros_mision' => ['Nuestra Misión', 'En Valores Casa de Bolsa generamos valor a través de soluciones financieras responsables, participando activamente en el mercado de capitales con ética, profesionalismo y una adecuada gestión del riesgo.'],
    'nosotros_vision' => ['Nuestra Visión', 'Ser referentes del mercado bursátil paraguayo por nuestra trayectoria, solidez e innovación, acompañando el desarrollo económico del país y contribuyendo al bienestar de nuestros clientes e inversores.'],
];
$us = $pdo->prepare('INSERT INTO secciones_contenido (clave, titulo, contenido) VALUES (:k, :t, :c)
                     ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), contenido = VALUES(contenido)');
foreach ($secciones as $clave => $s) {
    $us->execute([':k' => $clave, ':t' => $s[0], ':c' => $s[1]]);
}
echo 'Secciones de contenido: ' . count($secciones) . "\n";

// ---------------------------------------------------------------------------
//  OPORTUNIDADES — 10 instrumentos (datos ilustrativos del TEXTOS-SITIO.md).
//  Reemplaza los 2 de ejemplo del seed original. tipo: bono|cda|accion|inter.
// ---------------------------------------------------------------------------
$pdo->exec("DELETE FROM oportunidades");
// [tipo, instrumento, emisor, tasa, plazo, moneda(Gs|USD), calificacion, monto_minimo, destacado]
$oports = [
    ['bono',   'Bono Serie B — Emisión 2024',            'Banco Regional',              8.50, '36 meses',   'USD', 'AAA', 5000,    1],
    ['cda',    'CDA a plazo fijo — Renta Mensual',       'Visión Banco',                6.20, '12 meses',   'USD', 'AA+', 1000,    1],
    ['bono',   'Bono Municipal — Infraestructura',       'Municipalidad de Asunción',   9.00, '60 meses',   'Gs',  'AA',  5000000, 1],
    ['cda',    'CDA — Vencimiento único',                'Financiera El Comercio',      7.00, '24 meses',   'USD', 'AA',  2000,    0],
    ['accion', 'Acciones ordinarias nominativas',        'Industrias PROMAR S.A.',      null, 'Indefinido', 'Gs',  'A',   2000000, 0],
    ['bono',   'Bono Fiduciario — Titularización',       'Frigorífico Paraguay',        9.50, '48 meses',   'USD', 'AA-', 3000,    1],
    ['cda',    'CDA — Renta trimestral',                 'GNB Paraguay',                5.80, '6 meses',    'USD', 'AAA', 1000,    0],
    ['accion', 'Acciones preferidas (dividendo fijo 8%)','Banco Nacional de Fomento',   8.00, 'Indefinido', 'Gs',  'AA',  5000000, 0],
    ['inter',  'Vanguard S&P 500 ETF (VOO)',             'ETF Internacional — USA',     null, 'Sin venc.',  'USD', 'AAA', 500,     1],
    ['inter',  'Letras del Tesoro Americano (T-Bills)',  'Tesoro de EE.UU.',            5.10, '3 meses',    'USD', 'AAA', 500,     0],
];
$io = $pdo->prepare(
    'INSERT INTO oportunidades (tipo, instrumento, emisor, tasa, plazo, moneda, calificacion, monto_minimo, estado, destacado, orden)
     VALUES (:tipo, :inst, :emisor, :tasa, :plazo, :moneda, :calif, :min, "disponible", :dest, :orden)'
);
foreach ($oports as $i => $o) {
    $io->execute([
        ':tipo' => $o[0], ':inst' => $o[1], ':emisor' => $o[2], ':tasa' => $o[3], ':plazo' => $o[4],
        ':moneda' => $o[5], ':calif' => $o[6], ':min' => $o[7], ':dest' => $o[8], ':orden' => $i + 1,
    ]);
}
echo 'Oportunidades cargadas: ' . count($oports) . "\n";

// ---------------------------------------------------------------------------
//  GLOSARIO — términos financieros definidos inline en el sitio original.
// ---------------------------------------------------------------------------
$pdo->exec("DELETE FROM glosario");
$glosario = [
    ['Bono', 'Instrumento de deuda: al comprarlo, prestás dinero a una empresa o municipio que se compromete a devolvértelo en un plazo determinado junto con intereses periódicos (cupones).'],
    ['Renta fija', 'Inversión en la que sabés exactamente cuánto vas a cobrar y cuándo, lo que la vuelve ideal para quienes buscan previsibilidad y flujos regulares de ingresos.'],
    ['Cupón', 'Pago de intereses periódico que recibe el tenedor de un bono durante la vida del instrumento.'],
    ['CDA (Certificado de Depósito de Ahorro)', 'Instrumento de inversión emitido por entidades financieras reguladas por el Banco Central del Paraguay. Depositás tu dinero por un plazo fijo acordado y recibís una tasa de interés fija al vencimiento o de forma periódica.'],
    ['Escalera de CDAs', 'Estrategia de diversificación temporal: múltiples CDAs con distintos vencimientos para optimizar liquidez y rendimiento.'],
    ['Acción', 'Al comprar acciones de una empresa te convertís en accionista: un propietario parcial del negocio, con derecho a participar en sus ganancias (dividendos) y en el crecimiento de la empresa.'],
    ['Dividendo', 'Parte de las ganancias de una empresa que se distribuye entre sus accionistas.'],
    ['Mercado secundario', 'Mercado donde se compran y venden instrumentos ya emitidos, otorgando liquidez al inversor que necesita vender antes del vencimiento.'],
    ['ETF (Exchange Traded Fund)', 'Fondo indexado que cotiza en bolsa y replica el comportamiento de un mercado o índice entero, como el S&P 500.'],
    ['REIT', 'Fondo de inversión inmobiliaria cotizado en bolsa que da exposición al mercado inmobiliario con liquidez diaria.'],
    ['T-Bills (Letras del Tesoro USA)', 'Letras del Tesoro americano, consideradas una de las inversiones más seguras del mundo, con rendimientos en dólares.'],
    ['Titularización', 'Conversión de flujos futuros o activos ilíquidos en instrumentos negociables en el mercado de capitales.'],
    ['Liquidación T+2', 'Plazo de dos días hábiles en que se acreditan los fondos tras una operación en el mercado internacional.'],
];
$ig = $pdo->prepare('INSERT INTO glosario (termino, definicion, orden) VALUES (:t, :d, :o)');
foreach ($glosario as $i => $g) {
    $ig->execute([':t' => $g[0], ':d' => $g[1], ':o' => $i + 1]);
}
echo 'Glosario cargado: ' . count($glosario) . " términos\n";

echo "\n✓ Seed de contenido aplicado.\n";
