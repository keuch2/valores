<?php
/**
 * Controlador público "Trabajá con Nosotros": formulario de postulación que
 * se envía por email a la casilla configurada (`trabaja_email`, por defecto
 * administracion@valores.com.py).
 */

declare(strict_types=1);

/** Muestra el formulario. */
function trabaja_form(?string $error = null, array $viejos = [], bool $enviado = false): void
{
    vista_publica('trabaja', ['error' => $error, 'viejos' => $viejos, 'enviado' => $enviado], [
        'title' => 'Trabajá con Nosotros — Valores',
        'desc'  => 'Sumate al equipo de Valores Casa de Bolsa. Dejanos tus datos y nos ponemos en contacto.',
        'activo'=> 'trabaja',
    ]);
}

/** Procesa el envío (POST): valida y manda el email. */
function trabaja_enviar(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigir('trabaja-con-nosotros');
    }
    csrf_exigir();

    // Anti-spam: honeypot.
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        redirigir('trabaja-con-nosotros');
    }

    $viejos = [
        'nombre'      => mb_substr(post('nombre'), 0, 100),
        'apellido'    => mb_substr(post('apellido'), 0, 100),
        'email'       => mb_substr(post('email'), 0, 190),
        'telefono'    => mb_substr(post('telefono'), 0, 50),
        'comentarios' => mb_substr(post('comentarios'), 0, 3000),
    ];

    foreach (['nombre', 'apellido', 'email', 'telefono', 'comentarios'] as $c) {
        if ($viejos[$c] === '') {
            trabaja_form('Completá todos los campos.', $viejos);
            return;
        }
    }
    if (email_valido($viejos['email']) === null) {
        trabaja_form('Ingresá un correo electrónico válido.', $viejos);
        return;
    }

    $para = (string) (Config::get('trabaja_email', '') ?: 'administracion@valores.com.py');
    $cuerpo = "Nueva postulación desde el sitio web (Trabajá con Nosotros).\n\n"
        . "Nombre: {$viejos['nombre']} {$viejos['apellido']}\n"
        . "Correo electrónico: {$viejos['email']}\n"
        . "Teléfono: {$viejos['telefono']}\n\n"
        . "Comentarios:\n{$viejos['comentarios']}\n\n"
        . "Enviado el " . date('d/m/Y H:i') . "\n";

    require_once APP_ROOT . '/includes/core/mailer.php';
    if (!mailer_enviar($para, 'Postulación — Trabajá con Nosotros: ' . $viejos['nombre'] . ' ' . $viejos['apellido'], $cuerpo)) {
        error_log('trabaja_enviar: no se pudo enviar el email a ' . $para);
        trabaja_form('No pudimos enviar tu postulación en este momento. Probá de nuevo más tarde o escribinos a ' . $para . '.', $viejos);
        return;
    }

    trabaja_form(null, [], true);
}
