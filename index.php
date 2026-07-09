<?php
/**
 * index.php — Front-controller del sitio público.
 *
 * Traduce rutas amigables a plantillas en includes/views/public/.
 * El .htaccess raíz envía aquí todo lo que no es archivo/carpeta real ni /admin.
 */

declare(strict_types=1);

require __DIR__ . '/includes/core/bootstrap.php';

// Ruta relativa a la base de la app.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$base = rtrim(BASE_URL, '/');
$rel  = preg_replace('#^' . preg_quote($base, '#') . '#', '', $path);
$rel  = trim((string) $rel, '/');           // '' | 'servicios' | 'servicios/slug' ...
$seg  = $rel === '' ? [] : explode('/', $rel);

$pagina = $seg[0] ?? '';
$sub    = $seg[1] ?? null;

// Datos comunes (header/footer) disponibles para las vistas vía $GLOBALS['_sitio'].
$GLOBALS['_sitio'] = [
    'contacto_telefono'  => Config::get('contacto_telefono', '+595 (021) 600 450'),
    'contacto_whatsapp'  => Config::get('contacto_whatsapp', '+595 994 100 003'),
    'contacto_email'     => Config::get('contacto_email', 'valores@valores.com.py'),
    'contacto_direccion' => Config::get('contacto_direccion', 'Torre 3, Piso 10, Paseo la Galería, Asunción'),
    'redes' => [
        'linkedin'  => Config::get('red_linkedin', '#'),
        'facebook'  => Config::get('red_facebook', '#'),
        'twitter'   => Config::get('red_twitter', '#'),
        'instagram' => Config::get('red_instagram', '#'),
        'youtube'   => Config::get('red_youtube', '#'),
    ],
];

/** Renderiza una vista pública con su layout (header+footer). */
function vista_publica(string $vista, array $datos = [], array $meta = []): void
{
    $archivo = APP_ROOT . '/includes/views/public/' . $vista . '.php';
    if (!is_file($archivo)) {
        publica_404();
        return;
    }
    $meta = array_merge([
        'title' => 'Valores Casa de Bolsa',
        'desc'  => 'La Casa de Bolsa con mayor trayectoria del mercado de capitales paraguayo.',
        'activo'=> '',
    ], $meta);
    extract($datos, EXTR_SKIP);
    require APP_ROOT . '/includes/views/public/_header.php';
    require $archivo;
    require APP_ROOT . '/includes/views/public/_footer.php';
}

function publica_404(): void
{
    http_response_code(404);
    $meta = ['title' => 'Página no encontrada', 'desc' => '', 'activo' => ''];
    require APP_ROOT . '/includes/views/public/_header.php';
    require APP_ROOT . '/includes/views/public/404.php';
    require APP_ROOT . '/includes/views/public/_footer.php';
    exit;
}

// --- Enrutamiento ---
switch ($pagina) {
    case '':
        vista_publica('home', [
            'servicios'     => Publico::servicios(),
            'oportunidades' => Publico::oportunidades(),
            'tasas'         => Publico::tasasSimulador(),
        ], ['title' => 'Valores Casa de Bolsa — Inversiones con trayectoria', 'activo' => 'inicio']);
        break;

    case 'servicios':
        if ($sub) {
            $s = Publico::servicioPorSlug($sub);
            if (!$s) { publica_404(); }
            vista_publica('servicio-detalle', ['s' => $s], ['title' => $s['titulo'] . ' — Valores', 'activo' => 'servicios']);
        } else {
            vista_publica('servicios', ['servicios' => Publico::servicios()], ['title' => 'Servicios — Valores', 'activo' => 'servicios']);
        }
        break;

    case 'oportunidades':
        $tipo = get('tipo') ?: null;
        vista_publica('oportunidades', [
            'oportunidades' => Publico::oportunidades($tipo),
            'tipoActivo'    => $tipo,
        ], ['title' => 'Oportunidades de inversión — Valores', 'activo' => 'invierte']);
        break;

    case 'nosotros':
        vista_publica('nosotros', ['ejecutivos' => Publico::ejecutivos()], ['title' => 'Nosotros — Valores', 'activo' => 'nosotros']);
        break;

    case 'contacto':
        vista_publica('contacto', ['faqs' => Publico::faqs('contacto')], ['title' => 'Contacto — Valores', 'activo' => 'contacto']);
        break;

    case 'glosario':
        vista_publica('glosario', ['terminos' => Publico::glosario()], ['title' => 'Glosario financiero — Valores', 'activo' => 'invierte']);
        break;

    case 'apertura-de-cuenta':
        require APP_ROOT . '/includes/controllers/public/apertura.php';
        if ($sub === 'enviar') {
            apertura_enviar();
        } else {
            apertura_wizard();
        }
        break;

    // Noticias: existe en el CMS pero OCULTA en el front por ahora (decisión del cliente).
    case 'noticias':
        publica_404();
        break;

    default:
        publica_404();
}
