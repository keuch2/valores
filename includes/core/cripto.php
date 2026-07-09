<?php
/**
 * cripto.php — Cifrado en reposo de datos KYC sensibles.
 *
 * AES-256-GCM (cifrado autenticado): protege confidencialidad e integridad.
 * La clave vive en config (KYC_ENCRYPTION_KEY), fuera de la base de datos.
 */

declare(strict_types=1);

/** Devuelve la clave binaria de 32 bytes desde la config. */
function cripto_clave(): string
{
    $hex = defined('KYC_ENCRYPTION_KEY') ? KYC_ENCRYPTION_KEY : '';
    $bin = @hex2bin($hex);
    if ($bin === false || strlen($bin) !== 32) {
        throw new RuntimeException('KYC_ENCRYPTION_KEY inválida: debe ser 64 caracteres hex (32 bytes).');
    }
    return $bin;
}

/**
 * Cifra un texto plano. Devuelve un string base64 autocontenido (iv+tag+cipher)
 * con prefijo de versión para permitir rotación futura del esquema.
 */
function cripto_cifrar(string $plano): string
{
    $iv  = random_bytes(12); // GCM: 96 bits recomendado
    $tag = '';
    $cipher = openssl_encrypt($plano, 'aes-256-gcm', cripto_clave(), OPENSSL_RAW_DATA, $iv, $tag);
    if ($cipher === false) {
        throw new RuntimeException('Fallo al cifrar los datos.');
    }
    return 'v1:' . base64_encode($iv . $tag . $cipher);
}

/**
 * Descifra un valor producido por cripto_cifrar(). Devuelve el texto plano,
 * o null si el dato no está cifrado / es inválido / falla la autenticación.
 */
function cripto_descifrar(string $valor): ?string
{
    if (strncmp($valor, 'v1:', 3) !== 0) {
        return null; // no cifrado o formato desconocido
    }
    $raw = base64_decode(substr($valor, 3), true);
    if ($raw === false || strlen($raw) < 28) { // 12 iv + 16 tag + >=0
        return null;
    }
    $iv     = substr($raw, 0, 12);
    $tag    = substr($raw, 12, 16);
    $cipher = substr($raw, 28);
    $plano  = openssl_decrypt($cipher, 'aes-256-gcm', cripto_clave(), OPENSSL_RAW_DATA, $iv, $tag);
    return $plano === false ? null : $plano;
}

/** ¿El valor está cifrado con este esquema? */
function cripto_es_cifrado(string $valor): bool
{
    return strncmp($valor, 'v1:', 3) === 0;
}
