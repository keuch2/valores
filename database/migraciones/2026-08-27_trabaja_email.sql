-- Migración 2026-08-27 — Página "Trabajá con Nosotros": casilla destino de las
-- postulaciones (editable en Admin → Configuración → Datos de contacto). Idempotente.
INSERT IGNORE INTO configuracion (clave, valor, grupo)
VALUES ('trabaja_email', 'administracion@valores.com.py', 'contacto');
