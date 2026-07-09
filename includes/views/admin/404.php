<?php /** Vista 404 del panel (standalone, sin layout para evitar dependencias). */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>No encontrado · Valores Admin</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>
<body class="login-page">
    <div class="login-box" style="text-align:center">
        <h1>404</h1>
        <p class="sub">La página del panel que buscás no existe.</p>
        <a class="btn btn-primary" href="<?= e(url('admin/?r=dashboard')) ?>">Volver al dashboard</a>
    </div>
</body>
</html>
