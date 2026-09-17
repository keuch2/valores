-- Migración 2026-09-17 (c) — Casilla destino del formulario de Contacto y
-- nombre del remitente para SMTP (necesario para cuentas de Google). Idempotente.
INSERT IGNORE INTO configuracion (clave, valor, grupo) VALUES
  ('contacto_form_email', '', 'formularios'),
  ('smtp_nombre', 'Valores Casa de Bolsa', 'smtp');
-- El destinatario de postulaciones pasa al grupo de formularios.
UPDATE configuracion SET grupo = 'formularios' WHERE clave = 'trabaja_email';
