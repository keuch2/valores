-- Migración 2026-08-27 — Fotos de la plana directiva (assets versionados,
-- registrados en `media` y enlazados a `ejecutivos`). Idempotente.
INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, tamano_bytes, ancho, alto, alt_text)
SELECT * FROM (SELECT 'presidente-diego-borja.jpg', 'presidente-diego-borja.jpg', 'assets/img/directorio/presidente-diego-borja.jpg', 'imagen', 'image/jpeg', 0, 800, 800, 'Diego Christian Borja Terán — Presidente') AS t
WHERE NOT EXISTS (SELECT 1 FROM media WHERE ruta = 'assets/img/directorio/presidente-diego-borja.jpg');
INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, tamano_bytes, ancho, alto, alt_text)
SELECT * FROM (SELECT 'vicepresidente-gustavo-angulo.jpg', 'vicepresidente-gustavo-angulo.jpg', 'assets/img/directorio/vicepresidente-gustavo-angulo.jpg', 'imagen', 'image/jpeg', 0, 800, 800, 'Gustavo Mathias Angulo Turitich — Vicepresidente') AS t
WHERE NOT EXISTS (SELECT 1 FROM media WHERE ruta = 'assets/img/directorio/vicepresidente-gustavo-angulo.jpg');
INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, tamano_bytes, ancho, alto, alt_text)
SELECT * FROM (SELECT 'directora-titular-yanina-monges.jpg', 'directora-titular-yanina-monges.jpg', 'assets/img/directorio/directora-titular-yanina-monges.jpg', 'imagen', 'image/jpeg', 0, 800, 800, 'Yanina Monges Chávez — Directora Titular') AS t
WHERE NOT EXISTS (SELECT 1 FROM media WHERE ruta = 'assets/img/directorio/directora-titular-yanina-monges.jpg');

UPDATE ejecutivos SET foto_id = (SELECT id FROM media WHERE ruta = 'assets/img/directorio/presidente-diego-borja.jpg')        WHERE nombre = 'Diego Christian Borja Terán';
UPDATE ejecutivos SET foto_id = (SELECT id FROM media WHERE ruta = 'assets/img/directorio/vicepresidente-gustavo-angulo.jpg') WHERE nombre = 'Gustavo Mathias Angulo Turitich';
UPDATE ejecutivos SET foto_id = (SELECT id FROM media WHERE ruta = 'assets/img/directorio/directora-titular-yanina-monges.jpg') WHERE nombre = 'Yanina Monges Chávez';
