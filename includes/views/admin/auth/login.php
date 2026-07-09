<?php /** Vista: login del panel. Recibe $error, $email_previo. */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Valores Admin</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>
<body class="login-page">
    <form class="login-box" method="post" action="<?= e(url('admin/?r=auth/login')) ?>">
        <h1>Valores CMS</h1>
        <p class="sub">Panel de administración</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" style="margin:0 0 16px"><?= e($error) ?></div>
        <?php endif; ?>

        <?= csrf_campo() ?>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input class="form-input" type="email" id="email" name="email"
                   value="<?= e($email_previo ?? '') ?>" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Contraseña</label>
            <input class="form-input" type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%">Ingresar</button>
    </form>
</body>
</html>
