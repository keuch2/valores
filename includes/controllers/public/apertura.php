<?php
/**
 * Controlador público de apertura de cuenta (flujo simple).
 *
 * El usuario elige el tipo de cuenta, deja sus datos de contacto y, tras
 * guardarse la solicitud (round-robin de agentes + datos cifrados como
 * siempre), se lo lleva a WhatsApp con el resumen precargado. El número
 * destino se configura en el panel (clave `apertura_whatsapp`, con
 * fallback a `contacto_whatsapp`).
 *
 * El wizard KYC multi-paso quedó desactivado; `pasos.php` se conserva
 * porque el detalle admin de solicitudes históricas usa sus etiquetas.
 */

declare(strict_types=1);

require_once APP_ROOT . '/includes/apertura/pasos.php';

/** Etiquetas legibles de los tipos de cuenta. */
function apertura_tipos(): array
{
    return ['fisica' => 'Persona Física', 'conjunta' => 'Cuenta Conjunta', 'juridica' => 'Persona Jurídica'];
}

/** Rangos de inversión (clave => etiqueta). */
function apertura_rangos(): array
{
    return [
        'r1'   => 'Entre Gs. 10 millones y Gs. 100 millones',
        'r2'   => 'Entre Gs. 100 millones y Gs. 500 millones',
        'r3'   => 'Entre Gs. 500 millones y Gs. 5.000 millones',
        'r4'   => 'Más de Gs. 5.000 millones (aprox. USD 700.000)',
        'otro' => 'Otro',
    ];
}

/** Muestra el formulario de apertura. */
function apertura_wizard(): void
{
    vista_publica('apertura', ['error' => null, 'viejos' => []], [
        'title' => 'Abrir cuenta — Valores', 'activo' => '',
    ]);
}

/** Re-renderiza el formulario con un error y los valores ya cargados. */
function apertura_reintentar(string $error, array $viejos): void
{
    vista_publica('apertura', ['error' => $error, 'viejos' => $viejos], [
        'title' => 'Abrir cuenta — Valores', 'activo' => '',
    ]);
    exit;
}

/** Procesa el envío: valida, guarda y muestra la confirmación con enlace a WhatsApp. */
function apertura_enviar(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigir('apertura-de-cuenta');
    }
    csrf_exigir();

    // Anti-spam: honeypot.
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        redirigir('apertura-de-cuenta');
    }

    $tipos = apertura_tipos();
    $tipo  = post('tipo_persona');

    // Largos alineados a las columnas de referencia de solicitudes_apertura.
    $nombre     = mb_substr(post('nombre_completo'), 0, 200);
    $telefono   = mb_substr(post('telefono'), 0, 50);
    $email      = mb_substr(post('email'), 0, 190);
    $ciudadPais = mb_substr(post('ciudad_pais'), 0, 120);
    $rango      = post('rango_inversion');
    $rangos     = apertura_rangos();

    $viejos = [
        'tipo_persona'    => $tipo,
        'nombre_completo' => $nombre,
        'telefono'        => $telefono,
        'email'           => $email,
        'ciudad_pais'     => $ciudadPais,
        'rango_inversion' => $rango,
    ];

    if (!isset($tipos[$tipo])) {
        apertura_reintentar('Seleccioná el tipo de cuenta.', $viejos);
    }
    if ($nombre === '' || $telefono === '' || $email === '' || $ciudadPais === '') {
        apertura_reintentar('Completá todos los campos obligatorios.', $viejos);
    }
    if (email_valido($email) === null) {
        apertura_reintentar('Ingresá un correo electrónico válido.', $viejos);
    }
    if (!isset($rangos[$rango])) {
        apertura_reintentar('Indicá el rango aproximado que estás considerando invertir.', $viejos);
    }
    $rangoLbl = $rangos[$rango];

    $datos = [
        'tipo_persona'    => $tipo,
        'nombre_completo' => $nombre,
        'telefono'        => $telefono,
        'email'           => $email,
        'ciudad_pais'     => $ciudadPais,
        'rango_inversion' => $rangoLbl,
    ];
    $ref = ['nombre' => $nombre, 'documento' => '', 'email' => $email, 'telefono' => $telefono];

    $r = Solicitud::crear($tipo, $datos, $ref, null);
    if (!$r['ok']) {
        apertura_reintentar($r['error'], $viejos);
    }

    // Emails (no bloquear la confirmación si fallan).
    require_once APP_ROOT . '/includes/core/mailer.php';
    apertura_notificar((int) $r['id'], $tipo, $ref, $r['agente'] ?? null, $ciudadPais, $rangoLbl);

    // Enlace a WhatsApp con el resumen de la solicitud.
    $numRaw = (string) (Config::get('apertura_whatsapp', '') ?: Config::get('contacto_whatsapp', ''));
    $num    = preg_replace('/[^0-9]/', '', $numRaw);
    $waUrl  = null;
    if ($num !== '') {
        $msj = "Hola, quiero abrir una cuenta en Valores Casa de Bolsa.\n"
            . "Tipo de cuenta: {$tipos[$tipo]}\n"
            . "Nombre completo: {$nombre}\n"
            . "Teléfono: {$telefono}\n"
            . "Email: {$email}\n"
            . "Ciudad / País: {$ciudadPais}\n"
            . "Rango a invertir: {$rangoLbl}\n"
            . "Solicitud N° {$r['id']}";
        $waUrl = 'https://wa.me/' . $num . '?text=' . rawurlencode($msj);
    }

    vista_publica('apertura-ok', [
        'numero' => $r['id'],
        'agente' => $r['agente'] ?? null,
        'waUrl'  => $waUrl,
    ], ['title' => 'Solicitud recibida — Valores', 'activo' => '']);
}

/** Envía los correos al agente asignado y (opcional) al cliente. */
function apertura_notificar(int $id, string $tipo, array $ref, ?array $agente, string $ciudadPais = '', string $rango = ''): void
{
    $panelUrl = (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : '')
        . url('admin/?r=solicitudes/ver&id=' . $id);

    // Al agente: aviso con enlace al panel (sin datos sensibles en el cuerpo).
    if ($agente && !empty($agente['email'])) {
        $asunto = Config::get('apertura_email_agente_asunto', 'Nueva solicitud de apertura de cuenta');
        $cuerpo = "Se registró una nueva solicitud de apertura (#{$id}).\n\n"
            . "Tipo: {$tipo}\n"
            . "Referencia: {$ref['nombre']}\n"
            . "Contacto: {$ref['email']} / {$ref['telefono']}\n"
            . ($ciudadPais !== '' ? "Ciudad / País: {$ciudadPais}\n" : '')
            . ($rango !== '' ? "Rango a invertir: {$rango}\n" : '')
            . "\nVer el detalle completo en el panel:\n{$panelUrl}\n";
        mailer_enviar($agente['email'], $asunto, $cuerpo);
    }

    // Al cliente: confirmación (opcional/recomendado).
    if (!empty($ref['email'])) {
        $asunto = Config::get('apertura_email_cliente_asunto', 'Recibimos tu solicitud — Valores Casa de Bolsa');
        $cuerpo = "Hola {$ref['nombre']},\n\n"
            . "Recibimos tu solicitud de apertura de cuenta. Tu número de solicitud es #{$id}.\n"
            . "Un asesor de Valores se comunicará con vos a la brevedad.\n\n"
            . "Gracias por elegirnos.\nValores Casa de Bolsa S.A.\n";
        mailer_enviar($ref['email'], $asunto, $cuerpo);
    }
}
