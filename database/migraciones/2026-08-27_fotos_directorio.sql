-- Migración 2026-08-27 — Fotos de la plana directiva (assets versionados,
-- registrados en `media` y enlazados a `ejecutivos`). Idempotente.
INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, tamano_bytes, ancho, alto, alt_text)
SELECT * FROM (SELECT 'presidente-diego-borja.jpg' AS na, 'presidente-diego-borja.jpg' AS no, 'assets/img/directorio/presidente-diego-borja.jpg' AS ruta, 'imagen' AS tipo, 'image/jpeg' AS mime, 0 AS tam, 800 AS an, 800 AS al, 'Diego Christian Borja Terán — Presidente' AS alt) AS t
WHERE NOT EXISTS (SELECT 1 FROM media WHERE ruta = 'assets/img/directorio/presidente-diego-borja.jpg');
INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, tamano_bytes, ancho, alto, alt_text)
SELECT * FROM (SELECT 'vicepresidente-gustavo-angulo.jpg' AS na, 'vicepresidente-gustavo-angulo.jpg' AS no, 'assets/img/directorio/vicepresidente-gustavo-angulo.jpg' AS ruta, 'imagen' AS tipo, 'image/jpeg' AS mime, 0 AS tam, 800 AS an, 800 AS al, 'Gustavo Mathias Angulo Turitich — Vicepresidente' AS alt) AS t
WHERE NOT EXISTS (SELECT 1 FROM media WHERE ruta = 'assets/img/directorio/vicepresidente-gustavo-angulo.jpg');
INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, tamano_bytes, ancho, alto, alt_text)
SELECT * FROM (SELECT 'directora-titular-yanina-monges.jpg' AS na, 'directora-titular-yanina-monges.jpg' AS no, 'assets/img/directorio/directora-titular-yanina-monges.jpg' AS ruta, 'imagen' AS tipo, 'image/jpeg' AS mime, 0 AS tam, 800 AS an, 800 AS al, 'Yanina Monges Chávez — Directora Titular' AS alt) AS t
WHERE NOT EXISTS (SELECT 1 FROM media WHERE ruta = 'assets/img/directorio/directora-titular-yanina-monges.jpg');

UPDATE ejecutivos SET foto_id = (SELECT id FROM media WHERE ruta = 'assets/img/directorio/presidente-diego-borja.jpg')        WHERE nombre = 'Diego Christian Borja Terán';
UPDATE ejecutivos SET foto_id = (SELECT id FROM media WHERE ruta = 'assets/img/directorio/vicepresidente-gustavo-angulo.jpg') WHERE nombre = 'Gustavo Mathias Angulo Turitich';
UPDATE ejecutivos SET foto_id = (SELECT id FROM media WHERE ruta = 'assets/img/directorio/directora-titular-yanina-monges.jpg') WHERE nombre = 'Yanina Monges Chávez';
