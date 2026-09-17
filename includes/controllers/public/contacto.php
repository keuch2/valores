<?php
/**
 * Controlador público del formulario de Contacto.
 *
 * El mensaje se envía por email a la casilla configurada en el panel
 * (`contacto_form_email`, con fallback al email institucional).
 */

declare(strict_types=1);

/** Muestra la página de contacto. */
function contacto_form(?string $error = null, array $viejos = [], bool $enviado = false): void
{
    vista_publica('contacto', [
        'faqs'    => Publico::faqs('contacto'),
        'error'   => $error,
        'viejos'  => $viejos,
        'enviado' => $enviado,
    ], ['title' => 'Contacto — Valores', 'activo' => 'contacto']);
}

/** Procesa el envío (POST): valida y manda el email. */
function contacto_enviar(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigir('contacto');
    }
    csrf_exigir();

    // Anti-spam: honeypot.
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        redirigir('contacto');
    }

    $viejos = [
        'nombre'   => mb_substr(post('nombre'), 0, 100),
        'apellido' => mb_substr(post('apellido'), 0, 100),
        'email'    => mb_substr(post('email'), 0, 190),
        'telefono' => mb_substr(post('telefono'), 0, 50),
        'mensaje'  => mb_substr(post('mensaje'), 0, 3000),
    ];

    foreach (['nombre', 'apellido', 'email', 'mensaje'] as $c) {
        if ($viejos[$c] === '') {
            contacto_form('Completá los campos obligatorios.', $viejos);
            return;
        }
    }
    if (email_valido($viejos['email']) === null) {
        contacto_form('Ingresá un correo electrónico válido.', $viejos);
        return;
    }

    $para = (string) (Config::get('contacto_form_email', '') ?: Config::get('contacto_email', 'valores@valores.com.py'));
    $cuerpo = "Nuevo mensaje desde el formulario de Contacto del sitio web.\n\n"
        . "Nombre: {$viejos['nombre']} {$viejos['apellido']}\n"
        . "Correo electrónico: {$viejos['email']}\n"
        . "Teléfono: " . ($viejos['telefono'] !== '' ? $viejos['telefono'] : '(no indicado)') . "\n\n"
        . "Mensaje:\n{$viejos['mensaje']}\n\n"
        . "Enviado el " . date('d/m/Y H:i') . "\n";

    require_once APP_ROOT . '/includes/core/mailer.php';
    if (!mailer_enviar($para, 'Consulta desde el sitio web: ' . $viejos['nombre'] . ' ' . $viejos['apellido'], $cuerpo, $viejos['email'])) {
        error_log('contacto_enviar: no se pudo enviar el email a ' . $para);
        contacto_form('No pudimos enviar tu mensaje en este momento. Probá de nuevo más tarde o escribinos a ' . $para . '.', $viejos);
        return;
    }

    contacto_form(null, [], true);
}
