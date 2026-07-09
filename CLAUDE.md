# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Qué es

CMS a medida para **Valores Casa de Bolsa** (sitio institucional dinámico + panel admin) en **PHP 8 vanilla + MySQL 8 vía PDO**. Sin frameworks, sin Composer, sin build step, sin `node_modules`. Todo el código de aplicación es PHP que se sirve directamente por Apache. El README (en español) es la referencia funcional; este archivo cubre lo operativo y arquitectónico. El código, comentarios, nombres de tabla/columna y mensajes de UI están **en español** — seguí esa convención al escribir código nuevo.

> Nota de contexto: el proyecto vive en `/opt/homebrew/var/www/valores-app`, dentro del checkout git de Homebrew (`/opt/homebrew`), pero **no está versionado por ese repo** — es un directorio untracked. No corras comandos git asumiendo que operan sobre este proyecto.

## Entorno local y comandos

Stack local = **Homebrew** (httpd + php + mysql vía `brew`), docroot `= /opt/homebrew/var/www`. La app corre en `http://localhost/valores-app/`. MySQL local: usuario `root` **sin contraseña**.

```bash
# (Re)crear la base de datos y datos iniciales
mysql -uroot valores_cms < database/schema.sql   # o: mysql -uroot < database/schema.sql (crea la BD)
mysql -uroot valores_cms < database/seed.sql

# Verificar sintaxis de un archivo PHP (no hay linter/formatter configurado)
php -l includes/models/Solicitud.php

# Levantar servicios (si no están corriendo)
brew services start httpd
brew services start mysql

# Config local (no versionada; ya existe en este checkout)
cp includes/config/config.sample.php includes/config/config.php   # solo si falta
```

No hay tests, ni linter, ni CI. La verificación es manual: `php -l` para sintaxis y probar el flujo en el navegador. `includes/config/config.php` está en `.gitignore` y **no se versiona** (contiene credenciales de BD y `KYC_ENCRYPTION_KEY`).

Panel admin: `http://localhost/valores-app/admin/` — seed: `admin@valores.com.py` / `Valores2026!`.

## Arquitectura

Dos front-controllers, un bootstrap común:

- **`index.php`** — sitio público. El `.htaccess` raíz reescribe todo lo que no sea archivo/carpeta real ni `/admin` hacia aquí. Traduce URLs amigables (`/servicios/slug`) a vistas en `includes/views/public/` vía un `switch` sobre el primer segmento.
- **`admin/index.php`** — panel. Apache **no** reescribe `/admin`, así que el router usa query string: `admin/?r=recurso/accion`. Mapea a `includes/controllers/admin/{recurso}.php`, que debe definir una función `accion_{accion}()`. Todo exige sesión admin salvo `auth/login` y `auth/logout`.
- **`includes/core/bootstrap.php`** — único `require` de ambos front-controllers. Carga config → core de seguridad (`db`, `helpers`, `sesiones`, `csrf`, `auth`, `cripto`) → registra el autoload de modelos (`includes/models/<Clase>.php`) → arranca sesión endurecida. El orden importa.

### El patrón central: CRUD declarativo dirigido por config

La mayoría de las entidades de contenido (noticias, oportunidades, servicios, ejecutivos, faqs, glosario, academy) **no tienen lógica propia**. Su comportamiento se define en dos archivos declarativos, y los controladores/modelos genéricos hacen el resto:

- **`includes/models/Crud.php`** — `const ENTIDADES`: whitelist de tabla + columnas editables + orden. Es la única fuente de nombres de columna que llegan al SQL; **el SQL nunca usa nombres provenientes del input del usuario**.
- **`includes/controllers/admin/_entidades.php`** — `entidades_config()`: define los formularios (etiquetas, tipos de campo, opciones de select, requeridos).
- **`includes/controllers/admin/_contenido.php`** — motor genérico: `contenido_index/crear/editar/eliminar`. Lee el POST según la config, valida, y delega en `Crud`.
- Cada controlador por entidad (`noticias.php`, `servicios.php`, …) es de ~10 líneas: sólo llama a los helpers genéricos con su clave.

**Para agregar una entidad de contenido nueva**: (1) tabla en `schema.sql`, (2) entrada en `Crud::ENTIDADES`, (3) entrada en `entidades_config()`, (4) controlador de 4 líneas que delega en `_contenido.php`. No escribas SQL a mano para CRUD estándar.

Los controladores con lógica propia (fuera del CRUD genérico) son: `media`, `solicitudes`, `agentes`, `configuracion`, `usuarios`, `auth`, `dashboard`.

### Modelos

- **`Crud`** — CRUD genérico whitelisted (arriba).
- **`Publico`** — todas las consultas de lectura del sitio público (servicios activos, oportunidades por tipo, ejecutivos, faqs, glosario, tasas del simulador). El público nunca toca `Crud`.
- **`Config`** — store clave/valor (`Config::get('clave', $default)`) para contacto, redes, SMTP, y **tasas del simulador** (antes hardcodeadas en `main.js`).
- **`Media`** — biblioteca de medios: subida con whitelist de extensión **+ MIME real (finfo)**, renombrado aleatorio, o video embebido por URL.
- **`Solicitud`** — apertura de cuenta (ver abajo).
- **`Usuario`** — admins del panel.

### Apertura de cuenta (la feature no trivial)

Wizard público multi-paso (`/apertura-de-cuenta`) con tres ramas: **física**, **jurídica**, **conjunta** (conjunta reutiliza los campos de física + anexo de titulares). Los pasos/campos se declaran en `includes/apertura/pasos.php`. Puntos clave del flujo (`includes/controllers/public/apertura.php` → `Solicitud::crear`):

- **KYC cifrado en reposo**: el JSON completo del wizard se guarda en `solicitudes_apertura.datos` cifrado con **AES-256-GCM** (`includes/core/cripto.php`, clave `KYC_ENCRYPTION_KEY`). Sólo columnas de referencia no sensibles (nombre, documento, email, tel) quedan en claro para indexar/buscar. Se descifra únicamente en el detalle del panel (`Solicitud::buscarDescifrada`).
- **Round-robin de agentes**: dentro de una transacción con `SELECT … FOR UPDATE` sobre `agentes` (evita colisiones entre envíos simultáneos).
- **Auditoría**: cada acceso a datos sensibles o cambio de estado se registra en la tabla `auditoria` vía `Solicitud::auditar()`. Preservá esto al tocar el flujo de solicitudes.
- **Firma**: se sube a `/uploads/solicitudes` (bloqueado por `.htaccess`) y se sirve **sólo por script con sesión admin** (`admin/?r=solicitudes/firma&id=N`, con `readfile()` + auditoría) — nunca por URL pública.

## Invariantes de seguridad (respetar siempre)

Este proyecto se diseñó con la seguridad como requisito central. Al escribir código:

- **PDO con sentencias preparadas en el 100% de las consultas.** Nunca concatenar valores en SQL. Los únicos nombres de tabla/columna interpolables provienen de whitelists en constantes (`Crud::ENTIDADES`, `slug_unico()`), nunca del usuario.
- **Escapar toda salida** con `e()` (`htmlspecialchars`) al renderizar cualquier dato dinámico en las vistas.
- **CSRF**: todo formulario incluye `csrf_campo()`; todo handler POST empieza con `csrf_exigir()` (POST sin token válido → 403). El wizard público también.
- **Guard de sesión**: los controladores admin ya están protegidos por el router (`auth_exigir()`); no lo desactives.
- **Subidas**: pasá siempre por `Media::subir*()` (valida extensión + MIME real + renombra). No sirvas archivos de `/uploads` con ejecución PHP.
- **Datos KYC**: cifrar al guardar, descifrar sólo en el panel, y **auditar cada acceso**.

La protección de `config.php`, `/includes`, `/database` y `/uploads/solicitudes` depende de `.htaccess` (`Deny from all` + reglas en el `.htaccess` raíz), porque todo vive dentro del webroot. Requiere `AllowOverride All` en Apache. Asumir HTTPS en producción.

## Convenciones y decisiones ya tomadas

- Idioma: **español** en todo (código, comentarios, DB, UI). `declare(strict_types=1);` en cada archivo PHP.
- Front-end: **Tailwind por CDN** + `assets/css/styles.css` (diseño heredado del sitio estático original; preservarlo). JS vanilla en `assets/js/` (`main.js`, `admin.js`, `apertura.js`). No hay bundler.
- **Noticias** existe en el CMS pero está **oculta en el front** (flag `visible_front`; `/noticias` devuelve 404). Es una decisión del cliente — no la actives sin pedido explícito.
- **Cuenta Conjunta** se conserva como tercera rama del wizard (decisión confirmada del cliente).
- Persona **Jurídica**: el set de campos en `pasos.php` es **provisional/inferido**; ajustar cuando el cliente entregue su KYC específico.
- Tablas y columnas usan `snake_case` en español; FKs explícitas; `created_at`/`updated_at` donde aplica.
