<?php
/**
 * Controlador admin/auth — login y logout del panel.
 */

declare(strict_types=1);

/**
 * GET: muestra el formulario. POST: procesa el login.
 * Ruta: admin/?r=auth/login
 */
function accion_login(): void
{
    // Si ya hay sesión, al dashboard.
    if (auth_check()) {
        redirigir('admin/?r=dashboard');
    }

    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $email = post('email');
        $pass  = post('password');

        if ($email === '' || $pass === '') {
            $error = 'Completá email y contraseña.';
        } else {
            $r = auth_login($email, $pass);
            if ($r['ok']) {
                flash('exito', 'Bienvenido/a.');
                redirigir('admin/?r=dashboard');
            }
            $error = $r['error'] ?? 'No se pudo iniciar sesión.';
        }
    }

    render_admin_simple('auth/login', [
        'error'        => $error,
        'email_previo' => post('email'),
    ], 'Iniciar sesión');
}

/**
 * Cierra la sesión y vuelve al login.
 * Ruta: admin/?r=auth/logout
 */
function accion_logout(): void
{
    auth_logout();
    redirigir('admin/?r=auth/login');
}
