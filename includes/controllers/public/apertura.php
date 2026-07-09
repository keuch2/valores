<?php
/**
 * Controlador público de apertura de cuenta (wizard).
 */

declare(strict_types=1);

require_once APP_ROOT . '/includes/apertura/pasos.php';

/** Muestra el wizard (Paso 0 + pasos por rama). */
function apertura_wizard(): void
{
    vista_publica('apertura', [
        'pasosFisica'   => apertura_pasos_fisica(),
        'pasosJuridica' => apertura_pasos_juridica(),
    ], ['title' => 'Abrir cuenta — Valores', 'activo' => '']);
}

/**
 * Procesa el envío del wizard (POST). Valida, sube firma, guarda con round-robin,
 * dispara emails y muestra la confirmación.
 */
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

    $tipo = post('tipo_persona');
    if (!in_array($tipo, ['fisica', 'juridica', 'conjunta'], true)) {
        flash('error', 'Seleccioná el tipo de persona.');
        redirigir('apertura-de-cuenta');
    }

    // La rama de campos base es física (conjunta reutiliza física); jurídica aparte.
    $ramaCampos = ($tipo === 'juridica') ? 'juridica' : 'fisica';
    $pasos = apertura_pasos($ramaCampos);

    // Recolectar datos declarados en los pasos (whitelist de nombres de campo).
    $datos = ['tipo_persona' => $tipo];
    $faltaReq = null;
    foreach ($pasos as $paso) {
        // Los repeaters (jurídica) se recogen como arreglos por nombre[].
        if (!empty($paso['repeater'])) {
            $filas = $_POST[$paso['clave']] ?? [];
            $datos[$paso['clave']] = is_array($filas) ? array_values($filas) : [];
            continue;
        }
        foreach ($paso['campos'] as $c) {
            $n = $c['n'];
            $v = $c['t'] === 'checkbox' ? (isset($_POST[$n]) ? 'si' : 'no') : post($n);
            $datos[$n] = $v;
            if (!empty($c['req']) && ($v === '' || $v === 'no' && $c['t'] === 'checkbox')) {
                $faltaReq = $faltaReq ?? $c['l'];
            }
        }
    }

    // Titulares adicionales de la Cuenta Conjunta (anexo).
    if ($tipo === 'conjunta' && !empty($_POST['titulares']) && is_array($_POST['titulares'])) {
        $datos['titulares_adicionales'] = array_values($_POST['titulares']);
    }

    if ($faltaReq !== null) {
        flash('error', 'Falta completar: ' . $faltaReq);
        redirigir('apertura-de-cuenta');
    }

    // Firma (imagen obligatoria en el último paso).
    $firmaId = null;
    if (!empty($_FILES['firma']) && ($_FILES['firma']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $maxFirma = (int) Config::get('apertura_firma_max_bytes', (string) UPLOAD_MAX_BYTES);
        $rf = Media::subirFirma($_FILES['firma'], $maxFirma);
        if (!$rf['ok']) {
            flash('error', $rf['error']);
            redirigir('apertura-de-cuenta');
        }
        $firmaId = $rf['id'];
    }

    // Datos de referencia indexables (según la rama).
    $ref = ($tipo === 'juridica')
        ? ['nombre' => $datos['razon_social'] ?? '', 'documento' => $datos['ruc'] ?? '', 'email' => $datos['email'] ?? '', 'telefono' => $datos['celular'] ?? '']
        : ['nombre' => $datos['nombres'] ?? '', 'documento' => $datos['documento'] ?? '', 'email' => $datos['email'] ?? '', 'telefono' => $datos['celular'] ?? ''];

    $r = Solicitud::crear($tipo, $datos, $ref, $firmaId);
    if (!$r['ok']) {
        flash('error', $r['error']);
        redirigir('apertura-de-cuenta');
    }

    // Emails (no bloquear la confirmación si fallan).
    require_once APP_ROOT . '/includes/core/mailer.php';
    apertura_notificar($r['id'], $tipo, $ref, $r['agente'] ?? null);

    // Confirmación.
    vista_publica('apertura-ok', [
        'numero' => $r['id'],
        'agente' => $r['agente'] ?? null,
    ], ['title' => 'Solicitud recibida — Valores', 'activo' => '']);
}

/** Envía los correos al agente asignado y (opcional) al cliente. */
function apertura_notificar(int $id, string $tipo, array $ref, ?array $agente): void
{
    $panelUrl = (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : '')
        . url('admin/?r=solicitudes/ver&id=' . $id);

    // Al agente: aviso con enlace al panel (sin datos sensibles en el cuerpo).
    if ($agente && !empty($agente['email'])) {
        $asunto = Config::get('apertura_email_agente_asunto', 'Nueva solicitud de apertura de cuenta');
        $cuerpo = "Se registró una nueva solicitud de apertura (#{$id}).\n\n"
            . "Tipo: {$tipo}\n"
            . "Referencia: {$ref['nombre']} — Doc: {$ref['documento']}\n"
            . "Contacto: {$ref['email']} / {$ref['telefono']}\n\n"
            . "Ver el detalle completo en el panel:\n{$panelUrl}\n";
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
