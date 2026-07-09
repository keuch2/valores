<?php
/**
 * helpers.php — Utilidades transversales del CMS.
 */

declare(strict_types=1);

/**
 * Escape de salida HTML. Usar SIEMPRE al renderizar cualquier dato dinámico.
 */
function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Construye una URL absoluta dentro de la app a partir de la BASE_URL.
 * url('admin/login') -> '/valores-app/admin/login'
 */
function url(string $ruta = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($ruta, '/');
}

/**
 * Redirige a una ruta interna y corta la ejecución.
 */
function redirigir(string $ruta): void
{
    header('Location: ' . url($ruta));
    exit;
}

/**
 * Genera un slug URL-safe a partir de un texto (maneja acentos del español).
 */
function slugify(string $texto): string
{
    $texto = trim($texto);

    // Transliteración explícita del español (determinista, sin depender del
    // locale del sistema — iconv('ASCII//TRANSLIT') es inconsistente en macOS).
    $mapa = [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n',
        'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','Ü'=>'u','Ñ'=>'n',
        'à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u',
        'â'=>'a','ê'=>'e','î'=>'i','ô'=>'o','û'=>'u','ç'=>'c',
    ];
    $texto = strtr($texto, $mapa);

    // mb_strtolower cubre cualquier acento residual antes de filtrar.
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    return trim($texto, '-') ?: 'item';
}

/**
 * Genera un slug único en una tabla dada (añade -2, -3… si ya existe).
 * $excluirId permite editar sin chocar con el propio registro.
 */
function slug_unico(string $base, string $tabla, ?int $excluirId = null): string
{
    // Whitelist del nombre de tabla — nunca interpolar entrada de usuario aquí.
    $tablasPermitidas = ['noticias', 'servicios'];
    if (!in_array($tabla, $tablasPermitidas, true)) {
        throw new InvalidArgumentException('Tabla no permitida para slug: ' . $tabla);
    }

    $slug = slugify($base);
    $candidato = $slug;
    $i = 2;

    while (true) {
        $sql = "SELECT COUNT(*) FROM {$tabla} WHERE slug = :slug";
        $params = [':slug' => $candidato];
        if ($excluirId !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $excluirId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if ((int) $stmt->fetchColumn() === 0) {
            return $candidato;
        }
        $candidato = $slug . '-' . $i;
        $i++;
    }
}

/**
 * Responde JSON y corta la ejecución (para endpoints AJAX del admin).
 */
function json_respuesta(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Lee un parámetro de $_POST como string recortado.
 */
function post(string $clave, string $default = ''): string
{
    return isset($_POST[$clave]) ? trim((string) $_POST[$clave]) : $default;
}

/**
 * Lee un parámetro de $_GET como string recortado.
 */
function get(string $clave, string $default = ''): string
{
    return isset($_GET[$clave]) ? trim((string) $_GET[$clave]) : $default;
}

/**
 * Valida un email; devuelve la versión normalizada o null.
 */
function email_valido(string $email): ?string
{
    $email = trim($email);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
}

/**
 * Devuelve la IP del cliente en binario para guardar (INET6_ATON-friendly).
 */
function ip_cliente_binaria(): ?string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $bin = @inet_pton($ip);
    return $bin !== false ? $bin : null;
}

/**
 * Renderiza una vista del admin dentro del layout del panel.
 *
 * @param string $vista  Ruta relativa a includes/views/admin (sin .php).
 * @param array  $datos  Variables disponibles en la vista.
 * @param string|null $titulo  Título de la página (para el <title> y header).
 */
function render_admin(string $vista, array $datos = [], ?string $titulo = null): void
{
    $archivo = APP_ROOT . '/includes/views/admin/' . $vista . '.php';
    if (!is_file($archivo)) {
        http_response_code(500);
        exit('Vista no encontrada: ' . e($vista));
    }

    $titulo = $titulo ?? 'Panel';
    extract($datos, EXTR_SKIP);

    // El contenido de la vista se captura y se inyecta en el layout.
    ob_start();
    require $archivo;
    $contenido = ob_get_clean();

    require APP_ROOT . '/includes/views/admin/_layout.php';
}

/**
 * Renderiza una vista "suelta" (sin layout), p.ej. la pantalla de login.
 */
function render_admin_simple(string $vista, array $datos = [], ?string $titulo = null): void
{
    $archivo = APP_ROOT . '/includes/views/admin/' . $vista . '.php';
    if (!is_file($archivo)) {
        http_response_code(500);
        exit('Vista no encontrada: ' . e($vista));
    }
    $titulo = $titulo ?? 'Valores · Admin';
    extract($datos, EXTR_SKIP);
    require $archivo;
}

/**
 * Marca activo el ítem de menú según el recurso actual del panel.
 */
function nav_activo(string $recurso): string
{
    $actual = explode('/', trim((string) ($_GET['r'] ?? 'dashboard'), '/'))[0];
    return $actual === $recurso ? ' activo' : '';
}

/**
 * Resuelve un id de media a su URL pública. Si no hay medio, devuelve $fallback
 * (una ruta a assets/img existente). Preserva las imágenes reales del sitio.
 */
function img_url(?int $mediaId, string $fallback = ''): string
{
    if ($mediaId) {
        $m = Media::buscar($mediaId);
        if ($m) {
            return Media::urlPublica($m);
        }
    }
    return $fallback !== '' ? url($fallback) : '';
}
