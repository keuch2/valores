-- ============================================================================
--  Valores Casa de Bolsa — CMS a medida
--  schema.sql — Estructura completa de la base de datos
--  MySQL 8 / utf8mb4 / InnoDB / PDO
--
--  Convenciones: nombres en español snake_case, claves foráneas explícitas,
--  created_at / updated_at donde aplica. Todo el acceso desde PHP es vía
--  PDO con sentencias preparadas.
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS valores_cms
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE valores_cms;

-- ----------------------------------------------------------------------------
--  NÚCLEO DEL CMS
-- ----------------------------------------------------------------------------

-- Usuarios del panel de administración
CREATE TABLE IF NOT EXISTS admin_users (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre          VARCHAR(150)  NOT NULL,
  email           VARCHAR(190)  NOT NULL,
  password_hash   VARCHAR(255)  NOT NULL,
  rol             VARCHAR(30)   NOT NULL DEFAULT 'superadmin',  -- diseñado para crecer
  activo          TINYINT(1)    NOT NULL DEFAULT 1,
  ultimo_acceso   DATETIME      NULL,
  intentos_fallidos TINYINT UNSIGNED NOT NULL DEFAULT 0,        -- anti fuerza bruta
  bloqueado_hasta DATETIME      NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admin_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Biblioteca de medios
CREATE TABLE IF NOT EXISTS media (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_archivo  VARCHAR(255) NOT NULL,                         -- nombre seguro en disco
  nombre_original VARCHAR(255) NOT NULL,
  ruta            VARCHAR(500) NOT NULL,                         -- relativa a /uploads/media
  tipo            ENUM('imagen','video','pdf','otro') NOT NULL DEFAULT 'otro',
  mime_type       VARCHAR(120) NOT NULL,
  tamano_bytes    BIGINT UNSIGNED NOT NULL DEFAULT 0,
  ancho           INT UNSIGNED NULL,
  alto            INT UNSIGNED NULL,
  alt_text        VARCHAR(255) NULL,
  video_url       VARCHAR(500) NULL,                             -- YouTube/Vimeo embebido (sin subir)
  subido_por      INT UNSIGNED NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_media_tipo (tipo),
  CONSTRAINT fk_media_user FOREIGN KEY (subido_por) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuración global clave/valor (contacto, SMTP, redes, tasas del simulador, etc.)
CREATE TABLE IF NOT EXISTS configuracion (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  clave       VARCHAR(120) NOT NULL,
  valor       TEXT NULL,
  grupo       VARCHAR(60)  NOT NULL DEFAULT 'general',           -- contacto|smtp|redes|simulador|apertura...
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_config_clave (clave),
  KEY idx_config_grupo (grupo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bloques de contenido editables (hero, cifras, secciones del home/páginas)
CREATE TABLE IF NOT EXISTS secciones_contenido (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  clave       VARCHAR(120) NOT NULL,
  titulo      VARCHAR(255) NULL,
  contenido   MEDIUMTEXT NULL,
  imagen_id   INT UNSIGNED NULL,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_seccion_clave (clave),
  CONSTRAINT fk_seccion_media FOREIGN KEY (imagen_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
--  ENTIDADES DE CONTENIDO
-- ----------------------------------------------------------------------------

-- Noticias / Blog  (NUEVO: se administra en el CMS pero queda OCULTO en el front por ahora)
CREATE TABLE IF NOT EXISTS noticias (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titulo              VARCHAR(255) NOT NULL,
  slug                VARCHAR(255) NOT NULL,
  resumen             VARCHAR(500) NULL,
  contenido           MEDIUMTEXT NULL,
  imagen_destacada_id INT UNSIGNED NULL,
  categoria           VARCHAR(60) NULL,                          -- mercado|macro|inter|empresa|regulacion
  estado              ENUM('borrador','publicado') NOT NULL DEFAULT 'borrador',
  visible_front       TINYINT(1) NOT NULL DEFAULT 0,             -- flag: Noticias oculto en el front por ahora
  fecha_publicacion   DATE NULL,
  autor               VARCHAR(150) NULL,
  seo_title           VARCHAR(255) NULL,
  seo_description     VARCHAR(300) NULL,
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_noticia_slug (slug),
  KEY idx_noticia_estado (estado),
  KEY idx_noticia_categoria (categoria),
  CONSTRAINT fk_noticia_media FOREIGN KEY (imagen_destacada_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Oportunidades de inversión (bonos / CDAs / etc.) — alta rotación, máximo ROI del CMS
CREATE TABLE IF NOT EXISTS oportunidades (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo          ENUM('bono','cda','accion','inter') NOT NULL DEFAULT 'bono',
  instrumento   VARCHAR(150) NOT NULL,
  emisor        VARCHAR(150) NULL,
  tasa          DECIMAL(6,2) NULL,                               -- % anual
  plazo         VARCHAR(60)  NULL,                               -- ej. "360 días", "2 años"
  moneda        ENUM('Gs','USD') NOT NULL DEFAULT 'Gs',
  calificacion  VARCHAR(10)  NULL,                               -- AAA|AA|A...
  monto_minimo  DECIMAL(15,2) NULL,
  estado        ENUM('disponible','agotado') NOT NULL DEFAULT 'disponible',
  destacado     TINYINT(1) NOT NULL DEFAULT 0,
  orden         INT NOT NULL DEFAULT 0,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_oport_tipo (tipo),
  KEY idx_oport_estado (estado),
  KEY idx_oport_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Servicios
CREATE TABLE IF NOT EXISTS servicios (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titulo            VARCHAR(150) NOT NULL,
  slug              VARCHAR(150) NOT NULL,
  icono             VARCHAR(80) NULL,                            -- clase Font Awesome u otra
  descripcion_corta VARCHAR(500) NULL,
  contenido         MEDIUMTEXT NULL,
  imagen_id         INT UNSIGNED NULL,
  orden             INT NOT NULL DEFAULT 0,
  activo            TINYINT(1) NOT NULL DEFAULT 1,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_servicio_slug (slug),
  KEY idx_servicio_orden (orden),
  CONSTRAINT fk_servicio_media FOREIGN KEY (imagen_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ejecutivos / Equipo  (campos nuevos: foto real, email, teléfono, whatsapp)
CREATE TABLE IF NOT EXISTS ejecutivos (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(150) NOT NULL,
  cargo       VARCHAR(150) NULL,
  bio         TEXT NULL,
  foto_id     INT UNSIGNED NULL,
  email       VARCHAR(190) NULL,
  telefono    VARCHAR(50) NULL,
  whatsapp    VARCHAR(50) NULL,
  linkedin    VARCHAR(255) NULL,
  orden       INT NOT NULL DEFAULT 0,
  activo      TINYINT(1) NOT NULL DEFAULT 1,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ejec_orden (orden),
  CONSTRAINT fk_ejec_media FOREIGN KEY (foto_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQ
CREATE TABLE IF NOT EXISTS faqs (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pregunta    VARCHAR(500) NOT NULL,
  respuesta   TEXT NOT NULL,
  categoria   VARCHAR(60) NULL,                                  -- bono|cda|contacto|inter|general
  orden       INT NOT NULL DEFAULT 0,
  activo      TINYINT(1) NOT NULL DEFAULT 1,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_faq_categoria (categoria),
  KEY idx_faq_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Glosario financiero (NUEVO)
CREATE TABLE IF NOT EXISTS glosario (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  termino     VARCHAR(150) NOT NULL,
  definicion  TEXT NOT NULL,
  orden       INT NOT NULL DEFAULT 0,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_glosario_termino (termino)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Academy (artículos / webinars) (NUEVO)
CREATE TABLE IF NOT EXISTS academy_recursos (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo        ENUM('articulo','webinar') NOT NULL DEFAULT 'articulo',
  titulo      VARCHAR(255) NOT NULL,
  descripcion TEXT NULL,
  video_url   VARCHAR(500) NULL,
  media_id    INT UNSIGNED NULL,
  fecha       DATE NULL,
  orden       INT NOT NULL DEFAULT 0,
  activo      TINYINT(1) NOT NULL DEFAULT 1,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_academy_tipo (tipo),
  CONSTRAINT fk_academy_media FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Suscriptores al newsletter
CREATE TABLE IF NOT EXISTS newsletter_suscriptores (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email       VARCHAR(190) NOT NULL,
  intereses   VARCHAR(255) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_nl_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
--  APERTURA DE CUENTA
-- ----------------------------------------------------------------------------

-- Agentes receptores de solicitudes (round-robin)
CREATE TABLE IF NOT EXISTS agentes (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre            VARCHAR(150) NOT NULL,
  email             VARCHAR(190) NOT NULL,
  activo            TINYINT(1) NOT NULL DEFAULT 1,
  orden             INT NOT NULL DEFAULT 0,
  total_asignadas   INT UNSIGNED NOT NULL DEFAULT 0,
  ultima_asignacion DATETIME NULL,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_agente_activo (activo),
  KEY idx_agente_rotacion (activo, ultima_asignacion, orden, id)  -- soporte al round-robin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Solicitudes de apertura de cuenta
-- tipo_persona incluye 'conjunta' (decisión confirmada: se conserva la Cuenta Conjunta)
CREATE TABLE IF NOT EXISTS solicitudes_apertura (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo_persona        ENUM('fisica','juridica','conjunta') NOT NULL,
  estado              ENUM('nueva','en_proceso','aprobada','rechazada') NOT NULL DEFAULT 'nueva',
  agente_asignado_id  INT UNSIGNED NULL,
  nombre_referencia   VARCHAR(200) NULL,                          -- nombre o razón social (indexable)
  documento_referencia VARCHAR(60) NULL,                          -- CI/RUC (indexable)
  email_contacto      VARCHAR(190) NULL,
  telefono_contacto   VARCHAR(50) NULL,
  datos               LONGTEXT NULL,                              -- detalle completo del wizard (KYC/FATCA/PEP/titulares) CIFRADO AES-256-GCM en reposo

  firma_media_id      INT UNSIGNED NULL,
  pdf_path            VARCHAR(500) NULL,
  ip_solicitante      VARBINARY(16) NULL,                         -- INET6_ATON
  user_agent          VARCHAR(255) NULL,
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_sol_estado (estado),
  KEY idx_sol_tipo (tipo_persona),
  KEY idx_sol_agente (agente_asignado_id),
  KEY idx_sol_documento (documento_referencia),
  KEY idx_sol_nombre (nombre_referencia),
  CONSTRAINT fk_sol_agente FOREIGN KEY (agente_asignado_id) REFERENCES agentes(id) ON DELETE SET NULL,
  CONSTRAINT fk_sol_firma  FOREIGN KEY (firma_media_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adjuntos extra de una solicitud (firmas múltiples en conjunta, respaldos)
CREATE TABLE IF NOT EXISTS solicitud_archivos (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  solicitud_id  INT UNSIGNED NOT NULL,
  media_id      INT UNSIGNED NOT NULL,
  tipo          ENUM('firma','respaldo') NOT NULL DEFAULT 'respaldo',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_solarch_solicitud (solicitud_id),
  CONSTRAINT fk_solarch_sol   FOREIGN KEY (solicitud_id) REFERENCES solicitudes_apertura(id) ON DELETE CASCADE,
  CONSTRAINT fk_solarch_media FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log de auditoría (quién vio / cambió qué solicitud — datos sensibles)
CREATE TABLE IF NOT EXISTS auditoria (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_user_id INT UNSIGNED NULL,
  accion        VARCHAR(60) NOT NULL,                            -- ver|cambiar_estado|reasignar|exportar...
  entidad       VARCHAR(60) NOT NULL,                            -- solicitudes_apertura, etc.
  entidad_id    INT UNSIGNED NULL,
  detalle       VARCHAR(500) NULL,
  ip            VARBINARY(16) NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_audit_entidad (entidad, entidad_id),
  CONSTRAINT fk_audit_user FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
