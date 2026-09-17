<?php
/**
 * Controlador admin/configuracion — datos de contacto, redes, SMTP, tasas,
 * apertura y códigos de tracking.
 *
 * Los campos se declaran como `clave => etiqueta` (input de texto) o como
 * `clave => ['etiqueta' => …, 'tipo' => 'textarea', 'hint' => …]`.
 */

declare(strict_types=1);

/** Etiqueta de un campo, venga como string o como array. */
function config_campo_etiqueta(string|array $def): string
{
    return is_array($def) ? (string) ($def['etiqueta'] ?? '') : $def;
}

/** Grupos y sus campos (clave => etiqueta, o clave => ['etiqueta','tipo','hint']). */
function config_grupos(): array
{
    return [
        'sitio' => ['titulo' => 'Sitio', 'campos' => [
            'favicon_media_id' => [
                'etiqueta' => 'Favicon (ícono de la pestaña del navegador)',
                'tipo'     => 'imagen',
                'hint'     => 'Subí un PNG o JPG cuadrado (recomendado 512×512 px). Reemplaza al favicon actual en todo el sitio.',
            ],
        ]],
        'contacto' => ['titulo' => 'Datos de contacto', 'campos' => [
            'contacto_telefono' => 'Teléfono',
            'contacto_whatsapp' => 'WhatsApp',
            'contacto_email' => 'Email',
            'contacto_direccion' => 'Dirección / oficina',
        ]],
        'formularios' => ['titulo' => 'Destinatarios de los formularios', 'campos' => [
            'contacto_form_email' => [
                'etiqueta' => 'Email que recibe el formulario de Contacto',
                'hint'     => 'Si se deja vacío, los mensajes van al email institucional configurado arriba.',
            ],
            'trabaja_email' => [
                'etiqueta' => 'Email que recibe Trabajá con Nosotros',
                'hint'     => 'Casilla a la que llegan las postulaciones enviadas desde el sitio.',
            ],
        ]],
        'redes' => ['titulo' => 'Redes sociales', 'campos' => [
            'red_linkedin' => 'LinkedIn', 'red_facebook' => 'Facebook',
            'red_instagram' => 'Instagram', 'red_twitter' => 'Twitter/X', 'red_youtube' => 'YouTube',
        ]],
        'simulador' => ['titulo' => 'Tasas del simulador (% anual)', 'campos' => [
            'tasa_bono' => 'Bono', 'tasa_cda' => 'CDA', 'tasa_accion' => 'Acciones',
            'tasa_inter' => 'Mercado internacional', 'tasa_letra' => 'Letra del Tesoro',
        ]],
        'smtp' => ['titulo' => 'SMTP (envío de correos)', 'campos' => [
            'smtp_host' => ['etiqueta' => 'Host', 'hint' => 'Para una cuenta de Google (Gmail o Google Workspace): <strong>smtp.gmail.com</strong>'],
            'smtp_port' => ['etiqueta' => 'Puerto', 'hint' => 'Google: <strong>587</strong> con TLS (o 465 con SSL).'],
            'smtp_user' => ['etiqueta' => 'Usuario', 'hint' => 'La dirección completa, por ejemplo <strong>info@valores.com.py</strong>.'],
            'smtp_pass' => ['etiqueta' => 'Contraseña', 'tipo' => 'password', 'hint' => 'Con Google se requiere una <strong>contraseña de aplicación</strong> de 16 caracteres (Cuenta de Google → Seguridad → Verificación en 2 pasos → Contraseñas de aplicaciones). La contraseña normal de la cuenta no funciona.'],
            'smtp_remitente' => ['etiqueta' => 'Remitente (From)', 'hint' => 'Google exige que coincida con el usuario o con un alias verificado de esa cuenta.'],
            'smtp_nombre' => ['etiqueta' => 'Nombre del remitente', 'hint' => 'Nombre visible en la bandeja de quien recibe. Por defecto: Valores Casa de Bolsa.'],
            'smtp_encriptacion' => ['etiqueta' => 'Encriptación', 'hint' => 'tls (puerto 587) o ssl (puerto 465).'],
        ]],
        'tracking' => ['titulo' => 'Códigos de tracking', 'campos' => [
            'tracking_head' => [
                'etiqueta' => 'Códigos para el &lt;head&gt; (Google, Meta y otros)',
                'tipo'     => 'textarea',
                'hint'     => 'Pegá acá las etiquetas completas que entrega cada plataforma (Google Analytics, Google Tag Manager, píxel de Meta, etc.), incluidas sus &lt;script&gt;. Se insertan tal cual en el &lt;head&gt; de todas las páginas públicas. Dejá el campo vacío para no cargar ninguno.',
            ],
        ]],
        'apertura' => ['titulo' => 'Módulo de apertura', 'campos' => [
            'apertura_whatsapp' => 'WhatsApp para solicitudes (con código de país)',
            'apertura_firma_max_bytes' => 'Tamaño máx. firma (bytes)',
            'apertura_firma_formatos' => 'Formatos permitidos',
            'apertura_email_agente_asunto' => 'Asunto email al agente',
            'apertura_email_cliente_asunto' => 'Asunto email al cliente',
        ]],
    ];
}

/**
 * Procesa la subida de un campo de imagen de configuración.
 * Devuelve el id de media como string, o null si no se envió archivo
 * (en cuyo caso se conserva el valor actual).
 */
function config_subir_imagen(string $clave): ?string
{
    $file = $_FILES[$clave] ?? null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png', 'jpg', 'jpeg'], true)) {
        flash('error', 'El favicon debe ser un archivo PNG o JPG.');
        redirigir('admin/?r=configuracion');
    }

    $r = Media::subir($file, (int) auth_usuario()['id']);
    if (!$r['ok']) {
        flash('error', $r['error']);
        redirigir('admin/?r=configuracion');
    }
    return (string) $r['id'];
}

function accion_index(): void
{
    $grupos = config_grupos();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $pares = [];
        foreach ($grupos as $g) {
            foreach ($g['campos'] as $clave => $def) {
                $tipo = is_array($def) ? ($def['tipo'] ?? 'text') : 'text';
                if ($tipo === 'imagen') {
                    // Los campos de imagen llegan por $_FILES; sin archivo, se conserva el actual.
                    $sub = config_subir_imagen($clave);
                    if ($sub !== null) {
                        $pares[$clave] = $sub;
                    }
                    continue;
                }
                if (isset($_POST[$clave])) {
                    $pares[$clave] = trim((string) $_POST[$clave]);
                }
            }
        }
        Config::guardar($pares);
        flash('exito', 'Configuración guardada.');
        redirigir('admin/?r=configuracion');
    }

    // Valores actuales
    $valores = [];
    foreach ($grupos as $g) {
        foreach ($g['campos'] as $clave => $def) {
            $valores[$clave] = Config::get($clave, '');
        }
    }

    render_admin('configuracion/index', [
        'grupos' => $grupos, 'valores' => $valores,
    ], 'Configuración');
}
