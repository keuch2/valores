# Valores Casa de Bolsa — Sitio dinámico + CMS a medida

Sitio institucional dinámico con CMS propio en **PHP 8 vanilla + MySQL 8 (PDO)**, sin frameworks.
Convierte el sitio estático original en administrable, preservando su diseño (Tailwind CDN + `styles.css`).

## Estado del desarrollo

| Fase | Estado |
|---|---|
| 0. Modelo de datos (`schema.sql` + `seed.sql`) | ✅ |
| 2. Núcleo de seguridad (PDO, CSRF, auth, sesiones) | ✅ |
| 3. CMS base (login, usuarios admin, dashboard) | ✅ |
| 4. Biblioteca de medios | ✅ |
| 5. Contenidos (noticias, oportunidades, servicios, ejecutivos, FAQ, glosario, academy) + Config + Agentes | ✅ |
| 6. Sitio público dinámico (preservando diseño) | ✅ |
| 7. Wizard de apertura (física/conjunta/jurídica) + firma + cifrado KYC + round-robin + emails + sección Solicitudes CMS | ✅ |
| 9. Pasada de seguridad final | pendiente |

### Nota sobre la Fase 7

- **Persona Física**: campos tomados del *Formulario de Registro de Cliente Persona Física* real de Valores (referencia en `includes/kyc-referencia/`).
- **Cuenta Conjunta**: reutiliza los campos de Física + un anexo de titulares adicionales (según el propio formulario, "Anexo KYC 2").
- **Persona Jurídica**: set de campos **provisional** (inferido); ajustar cuando Valores entregue su KYC específico. Marcado en `includes/apertura/pasos.php`.
- **Datos KYC cifrados en reposo**: la columna `solicitudes_apertura.datos` guarda el JSON del wizard cifrado con **AES-256-GCM** (clave `KYC_ENCRYPTION_KEY` en config). Solo se descifra en el detalle del panel, y cada acceso queda en `auditoria`.
- **Firma**: se sube a `/uploads/solicitudes` (bloqueado por `.htaccess`) y se sirve únicamente por script con sesión admin.

## Requisitos

- PHP 8.1+ (probado en 8.4) con extensiones `pdo_mysql`, `fileinfo`, `mbstring`.
- MySQL 8 / MariaDB equivalente.
- Apache con `mod_rewrite` y `mod_headers`, y `AllowOverride All` sobre el docroot.

## Instalación

1. **Base de datos** (usuario local por defecto: `root` sin contraseña):
   ```bash
   mysql -uroot < database/schema.sql
   mysql -uroot < database/seed.sql
   ```
   Esto crea la BD `valores_cms`, todas las tablas y datos iniciales.

2. **Configuración**: copiá y editá las credenciales.
   ```bash
   cp includes/config/config.sample.php includes/config/config.php
   ```
   Ajustá `BASE_URL` (por defecto `/valores-app/`), credenciales de BD y `APP_ENV` (`dev`/`prod`).
   `config.php` **no se versiona** (está en `.gitignore`).

3. **Servir**: la app corre en `http://localhost/valores-app/` (docroot Apache = `/opt/homebrew/var/www`).

## Acceso al panel

- URL: `http://localhost/valores-app/admin/`
- Usuario: `admin@valores.com.py`
- Contraseña: `Valores2026!`  ← **cambiar tras el primer login**.

## Arquitectura

```
valores-app/                  webroot del proyecto
├── index.php                 front-controller público (rutas amigables)
├── .htaccess                 rewrites + cabeceras de seguridad + bloqueos
├── admin/index.php           router del CMS (?r=recurso/accion)
├── assets/                   css (styles.css + admin.css), js (main.js + admin.js), img
├── includes/                 [.htaccess Deny] núcleo de la app
│   ├── config/               config.php (credenciales, no versionar)
│   ├── core/                 bootstrap, db (PDO), auth, csrf, sesiones, helpers
│   ├── models/               Usuario, Media, Crud, Config, Publico
│   ├── controllers/admin/    un controlador por sección
│   └── views/                public/ y admin/
├── uploads/                  [.htaccess: sin ejecución PHP]
│   ├── media/                biblioteca general
│   └── solicitudes/          [Deny] adjuntos KYC — se sirven por script con sesión admin
└── database/                 [Deny] schema.sql + seed.sql
```

## Seguridad implementada

- **PDO con sentencias preparadas** en el 100% de las consultas; whitelist de tablas/columnas en el CRUD genérico.
- **Escape de salida** con `htmlspecialchars()` (`e()`) en todo dato renderizado.
- **CSRF token** en todos los formularios (público y admin); POST sin token → 403.
- **Sesiones endurecidas**: `HttpOnly` + `SameSite=Strict` + `Secure` (auto bajo HTTPS), regeneración de ID en login, timeout por inactividad.
- **Contraseñas** con `password_hash()` (bcrypt) + rehash automático.
- **Anti fuerza bruta**: bloqueo temporal tras N intentos fallidos.
- **Subida de archivos**: whitelist de extensión + **MIME real (finfo)**, renombrado aleatorio, sin ejecución PHP en `/uploads`.
- **Cabeceras** `X-Frame-Options`, `X-Content-Type-Options`, CSP básica vía `.htaccess`.
- Carpetas internas (`includes`, `database`, `uploads/solicitudes`) con `Deny from all`.

> Nota de despliegue: al vivir todo dentro del webroot (por simplicidad de rutas), la protección de
> `config.php` y adjuntos KYC depende de los `.htaccess`. Requiere `AllowOverride All`. Asumir HTTPS en producción.

## Decisiones del cliente

- **Cuenta Conjunta**: se conserva como tercera rama del wizard (además de física/jurídica).
- **Noticias**: se administra en el CMS pero está **oculta en el front** (flag `visible_front`); las rutas `/noticias` devuelven 404 hasta activarla.
- **Tasas del simulador**: administrables desde Configuración (antes hardcodeadas en `main.js`).
