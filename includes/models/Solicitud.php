<?php
/**
 * Modelo Solicitud — apertura de cuenta.
 *
 * Guarda la solicitud con el JSON `datos` CIFRADO en reposo (AES-256-GCM),
 * asigna un agente por round-robin dentro de una transacción con FOR UPDATE,
 * y expone la lectura descifrada solo para el panel admin.
 */

declare(strict_types=1);

final class Solicitud
{
    /**
     * Crea una solicitud y asigna agente por round-robin, todo en una transacción.
     *
     * @param string $tipoPersona  fisica|juridica|conjunta
     * @param array  $datos        detalle completo del wizard (se cifra)
     * @param array  $ref          [nombre, documento, email, telefono] para columnas indexadas
     * @param int|null $firmaMediaId  id del medio de la firma (o null)
     * @return array{ok:bool, error?:string, id?:int, agente?:array|null}
     */
    public static function crear(string $tipoPersona, array $datos, array $ref, ?int $firmaMediaId): array
    {
        if (!in_array($tipoPersona, ['fisica', 'juridica', 'conjunta'], true)) {
            return ['ok' => false, 'error' => 'Tipo de persona inválido.'];
        }

        $pdo = db();
        try {
            $pdo->beginTransaction();

            // 1) Elegir agente activo con ultima_asignacion más antigua (NULL primero),
            //    bloqueando la fila para evitar colisiones entre envíos simultáneos.
            $stmt = $pdo->query(
                "SELECT id, nombre, email FROM agentes
                 WHERE activo = 1
                 ORDER BY (ultima_asignacion IS NOT NULL), ultima_asignacion ASC, orden ASC, id ASC
                 LIMIT 1 FOR UPDATE"
            );
            $agente = $stmt->fetch() ?: null;

            // 2) Insertar la solicitud con datos CIFRADOS.
            $jsonPlano = json_encode($datos, JSON_UNESCAPED_UNICODE);
            $jsonCifrado = cripto_cifrar($jsonPlano);

            $ins = $pdo->prepare(
                "INSERT INTO solicitudes_apertura
                 (tipo_persona, estado, agente_asignado_id, nombre_referencia, documento_referencia,
                  email_contacto, telefono_contacto, datos, firma_media_id, ip_solicitante, user_agent)
                 VALUES
                 (:tipo, 'nueva', :agente, :nombre, :doc, :email, :tel, :datos, :firma, :ip, :ua)"
            );
            $ins->bindValue(':tipo', $tipoPersona);
            $ins->bindValue(':agente', $agente['id'] ?? null, $agente ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $ins->bindValue(':nombre', mb_substr((string) ($ref['nombre'] ?? ''), 0, 200));
            $ins->bindValue(':doc', mb_substr((string) ($ref['documento'] ?? ''), 0, 60));
            $ins->bindValue(':email', mb_substr((string) ($ref['email'] ?? ''), 0, 190));
            $ins->bindValue(':tel', mb_substr((string) ($ref['telefono'] ?? ''), 0, 50));
            $ins->bindValue(':datos', $jsonCifrado);
            $ins->bindValue(':firma', $firmaMediaId, $firmaMediaId ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $ins->bindValue(':ip', ip_cliente_binaria(), PDO::PARAM_LOB);
            $ins->bindValue(':ua', mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255));
            $ins->execute();
            $id = (int) $pdo->lastInsertId();

            // 3) Actualizar contadores del agente.
            if ($agente) {
                $pdo->prepare(
                    "UPDATE agentes SET ultima_asignacion = NOW(), total_asignadas = total_asignadas + 1
                     WHERE id = :id"
                )->execute([':id' => $agente['id']]);
            }

            $pdo->commit();
            return ['ok' => true, 'id' => $id, 'agente' => $agente];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('[Solicitud] Error al crear: ' . $e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo registrar la solicitud. Intentá de nuevo.'];
        }
    }

    /** Lista solicitudes con filtros para el panel. */
    public static function listar(array $filtros = []): array
    {
        $sql = "SELECT s.*, a.nombre AS agente_nombre
                FROM solicitudes_apertura s
                LEFT JOIN agentes a ON a.id = s.agente_asignado_id
                WHERE 1=1";
        $p = [];
        if (!empty($filtros['estado']) && in_array($filtros['estado'], ['nueva','en_proceso','aprobada','rechazada'], true)) {
            $sql .= " AND s.estado = :estado"; $p[':estado'] = $filtros['estado'];
        }
        if (!empty($filtros['tipo']) && in_array($filtros['tipo'], ['fisica','juridica','conjunta'], true)) {
            $sql .= " AND s.tipo_persona = :tipo"; $p[':tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['agente'])) {
            $sql .= " AND s.agente_asignado_id = :ag"; $p[':ag'] = (int) $filtros['agente'];
        }
        if (!empty($filtros['q'])) {
            $sql .= " AND (s.nombre_referencia LIKE :q OR s.documento_referencia LIKE :q)";
            $p[':q'] = '%' . $filtros['q'] . '%';
        }
        $sql .= " ORDER BY s.created_at DESC";
        $stmt = db()->prepare($sql);
        $stmt->execute($p);
        return $stmt->fetchAll();
    }

    public static function contarNuevas(): int
    {
        return (int) db()->query("SELECT COUNT(*) FROM solicitudes_apertura WHERE estado='nueva'")->fetchColumn();
    }

    /** Busca una solicitud y DESCIFRA su JSON `datos` (solo para admin). */
    public static function buscarDescifrada(int $id): ?array
    {
        $stmt = db()->prepare(
            "SELECT s.*, a.nombre AS agente_nombre, INET6_NTOA(s.ip_solicitante) AS ip_texto
             FROM solicitudes_apertura s
             LEFT JOIN agentes a ON a.id = s.agente_asignado_id
             WHERE s.id = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) { return null; }

        $plano = $row['datos'] ? cripto_descifrar($row['datos']) : null;
        $row['datos_desc'] = $plano ? json_decode($plano, true) : [];
        return $row;
    }

    /** Cambia el estado, registrando en auditoría. */
    public static function cambiarEstado(int $id, string $estado, int $adminId): bool
    {
        if (!in_array($estado, ['nueva','en_proceso','aprobada','rechazada'], true)) {
            return false;
        }
        db()->prepare("UPDATE solicitudes_apertura SET estado = :e WHERE id = :id")
            ->execute([':e' => $estado, ':id' => $id]);
        self::auditar($adminId, 'cambiar_estado', $id, 'Nuevo estado: ' . $estado);
        return true;
    }

    /** Reasigna manualmente el agente (override del round-robin). */
    public static function reasignar(int $id, int $agenteId, int $adminId): void
    {
        db()->prepare("UPDATE solicitudes_apertura SET agente_asignado_id = :a WHERE id = :id")
            ->execute([':a' => $agenteId, ':id' => $id]);
        self::auditar($adminId, 'reasignar', $id, 'Agente: ' . $agenteId);
    }

    /** Registra en auditoría un acceso/acción sobre datos sensibles. */
    public static function auditar(int $adminId, string $accion, int $solicitudId, string $detalle = ''): void
    {
        try {
            $stmt = db()->prepare(
                "INSERT INTO auditoria (admin_user_id, accion, entidad, entidad_id, detalle, ip)
                 VALUES (:u, :a, 'solicitudes_apertura', :eid, :d, :ip)"
            );
            $stmt->bindValue(':u', $adminId, PDO::PARAM_INT);
            $stmt->bindValue(':a', $accion);
            $stmt->bindValue(':eid', $solicitudId, PDO::PARAM_INT);
            $stmt->bindValue(':d', mb_substr($detalle, 0, 500));
            $stmt->bindValue(':ip', ip_cliente_binaria(), PDO::PARAM_LOB);
            $stmt->execute();
        } catch (Throwable $e) {
            error_log('[Auditoria] ' . $e->getMessage());
        }
    }
}
