<?php
/**
 * mailer.php — Envío de correos vía SMTP configurado en el CMS.
 *
 * Usa PHPMailer si está disponible en includes/lib/PHPMailer/ (recomendado);
 * si no, cae a mail() nativo. Los fallos NO deben perder la solicitud: se
 * loguean para reintento manual.
 *
 * @return bool true si el envío se despachó sin error inmediato.
 */

declare(strict_types=1);

function mailer_enviar(string $para, string $asunto, string $cuerpo): bool
{
    $remitente = Config::get('smtp_remitente', 'no-reply@valores.com.py');
    $host = Config::get('smtp_host', '');

    // Ruta a PHPMailer incluido manualmente (sin Composer), si existe.
    $phpmailer = APP_ROOT . '/includes/lib/PHPMailer/PHPMailer.php';

    try {
        if ($host !== '' && is_file($phpmailer)) {
            require_once APP_ROOT . '/includes/lib/PHPMailer/Exception.php';
            require_once APP_ROOT . '/includes/lib/PHPMailer/PHPMailer.php';
            require_once APP_ROOT . '/includes/lib/PHPMailer/SMTP.php';

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->Port = (int) Config::get('smtp_port', '587');
            $usuario = Config::get('smtp_user', '');
            if ($usuario !== '') {
                $mail->SMTPAuth = true;
                $mail->Username = $usuario;
                $mail->Password = (string) Config::get('smtp_pass', '');
            }
            $enc = Config::get('smtp_encriptacion', 'tls');
            if ($enc) { $mail->SMTPSecure = $enc; }
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($remitente, 'Valores Casa de Bolsa');
            $mail->addAddress($para);
            $mail->Subject = $asunto;
            $mail->Body = $cuerpo;
            $mail->send();
            return true;
        }

        // Fallback: mail() nativo (suficiente en entornos con MTA local).
        $headers = 'From: ' . $remitente . "\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";
        $ok = @mail($para, $asunto, $cuerpo, $headers);
        if (!$ok) {
            error_log("[Mailer] Fallo al enviar a {$para} (asunto: {$asunto}). SMTP no configurado o mail() falló.");
        }
        return $ok;
    } catch (Throwable $e) {
        error_log('[Mailer] Excepción al enviar a ' . $para . ': ' . $e->getMessage());
        return false;
    }
}
