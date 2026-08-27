-- Migración 2026-08-27 — Historial de reportes diarios de Oportunidades. Idempotente.
CREATE TABLE IF NOT EXISTS oportunidades_reportes (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  media_id    INT UNSIGNED NOT NULL,
  subido_por  INT UNSIGNED NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_oprep_media (media_id),
  CONSTRAINT fk_oprep_media FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE,
  CONSTRAINT fk_oprep_user  FOREIGN KEY (subido_por) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backfill: el reporte vigente (config) entra al historial si aún no está.
INSERT INTO oportunidades_reportes (media_id, subido_por, created_at)
SELECT m.id, m.subido_por, m.created_at FROM media m
WHERE m.id = (SELECT CAST(valor AS UNSIGNED) FROM configuracion WHERE clave = 'oportunidades_pdf_id' AND valor REGEXP '^[0-9]+$')
  AND NOT EXISTS (SELECT 1 FROM oportunidades_reportes r WHERE r.media_id = m.id);
