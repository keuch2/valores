<?php
/**
 * Controlador admin/configuracion — datos de contacto, redes, SMTP, tasas, apertura.
 */

declare(strict_types=1);

/** Grupos y sus campos (clave => etiqueta). */
function config_grupos(): array
{
    return [
        'contacto' => ['titulo' => 'Datos de contacto', 'campos' => [
            'contacto_telefono' => 'Teléfono',
            'contacto_whatsapp' => 'WhatsApp',
            'contacto_email' => 'Email',
            'contacto_direccion' => 'Dirección / oficina',
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
            'smtp_host' => 'Host', 'smtp_port' => 'Puerto', 'smtp_user' => 'Usuario',
            'smtp_pass' => 'Contraseña', 'smtp_remitente' => 'Remitente', 'smtp_encriptacion' => 'Encriptación (tls/ssl)',
        ]],
        'apertura' => ['titulo' => 'Módulo de apertura', 'campos' => [
            'apertura_firma_max_bytes' => 'Tamaño máx. firma (bytes)',
            'apertura_firma_formatos' => 'Formatos permitidos',
            'apertura_email_agente_asunto' => 'Asunto email al agente',
            'apertura_email_cliente_asunto' => 'Asunto email al cliente',
        ]],
    ];
}

function accion_index(): void
{
    $grupos = config_grupos();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $pares = [];
        foreach ($grupos as $g) {
            foreach ($g['campos'] as $clave => $lbl) {
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
        foreach ($g['campos'] as $clave => $lbl) {
            $valores[$clave] = Config::get($clave, '');
        }
    }

    render_admin('configuracion/index', [
        'grupos' => $grupos, 'valores' => $valores,
    ], 'Configuración');
}
