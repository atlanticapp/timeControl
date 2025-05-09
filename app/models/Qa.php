<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Control;

class Qa extends Model
{
    protected $table = 'registro';

    // Obtener entregas pendientes de validación
    public function getEntregasPendientes($area_id)
    {
        $query = "
        SELECT 
            id, 
            maquina, 
            jtWo, 
            item, 
            area_id,
            codigo_empleado,
            tipo_boton,
            descripcion,
            fecha_registro,
            cantidad_produccion,
            cantidad_scrapt,
            estado_validacion
        FROM registro
        WHERE estado_validacion IN ('Pendiente')
            AND area_id = ?
            AND (
                (tipo_boton = 'Producción' AND descripcion = 'Parcial') 
                OR (tipo_boton = 'final_produccion')
            )
        ORDER BY fecha_registro DESC, maquina, jtWo, item, codigo_empleado";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $entregasAgrupadas = [];

        while ($row = $result->fetch_assoc()) {
            // Creamos una clave única usando los campos que identifican una entrega
            $key = $row['fecha_registro'] . '_' . $row['maquina'] . '_' . $row['jtWo'] . '_' .
                $row['item'] . '_' . $row['codigo_empleado'];

            if (!isset($entregasAgrupadas[$key])) {
                $entregasAgrupadas[$key] = [
                    'info_comun' => $this->prepararDatosComunes($row),
                    'entregas' => []
                ];
            }

            // Agregamos los registros de producción y scrap si existen
            if ($row['cantidad_produccion'] > 0) {
                $entregasAgrupadas[$key]['entregas'][] = [
                    'id' => $row['id'],
                    'tipo' => 'produccion',
                    'cantidad' => $row['cantidad_produccion'],
                    'estado_validacion' => 'Pendiente'
                ];
            }

            if ($row['cantidad_scrapt'] > 0) {
                $entregasAgrupadas[$key]['entregas'][] = [
                    'id' => $row['id'],
                    'tipo' => 'scrap',
                    'cantidad' => $row['cantidad_scrapt'],
                    'estado_validacion' => 'Pendiente'
                ];
            }
        }

        // Ordenar por fecha_registro de más reciente a más antigua
        uasort($entregasAgrupadas, function ($a, $b) {
            return strtotime($b['info_comun']['fecha_registro']) - strtotime($a['info_comun']['fecha_registro']);
        });

        return array_values($entregasAgrupadas);
    }

    private function prepararDatosComunes($row)
    {
        $control = new Control();
        return [
            'id' => $row['id'],
            'maquina' => $row['maquina'],
            'jtWo' => $row['jtWo'],
            'item' => $row['item'],
            'area_id' => $row['area_id'],
            'codigo_empleado' => $row['codigo_empleado'],
            'tipo_boton' => $row['tipo_boton'],
            'descripcion' => $row['descripcion'],
            'fecha_registro' => $row['fecha_registro'],
            'nombre_empleado' => $this->getNombreEmpleado($row['codigo_empleado']),
            'nombre_maquina' => $control->getNameMaquina($row['maquina'])
        ];
    }

    /**
     * Verifica los estados actuales de un conjunto de registros
     * 
     * @param array $registrosIds Arreglo de IDs de registros a verificar
     * @return array Arreglo asociativo con los IDs y estados de cada registro
     */
    public function verificarEstadosRegistros($registrosIds)
    {
        if (empty($registrosIds)) {
            return [];
        }

        // Crear placeholders para la consulta IN
        $placeholders = implode(',', array_fill(0, count($registrosIds), '?'));

        $query = "SELECT id, estado_validacion FROM registro WHERE id IN ($placeholders)";

        $stmt = $this->db->prepare($query);

        // Bindear parámetros dinámicamente
        $types = str_repeat('i', count($registrosIds));
        $stmt->bind_param($types, ...$registrosIds);

        $stmt->execute();
        $result = $stmt->get_result();

        $estados = [];
        while ($row = $result->fetch_assoc()) {
            $estados[$row['id']] = $row['estado_validacion'];
        }

        return $estados;
    }

    /**
     * Verifica si un registro específico sigue en estado pendiente
     * 
     * @param int $registroId ID del registro a verificar
     * @return bool True si el registro está pendiente, False en caso contrario
     */
    public function verificarRegistroPendiente($registroId)
    {
        $query = "SELECT estado_validacion FROM registro WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $registroId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $registro = $result->fetch_assoc();
        return $registro['estado_validacion'] === 'Pendiente';
    }

    public function getEntregasValidadasProduccion($userqa)
    {
        $query = "SELECT *
    FROM registro
    WHERE estado_validacion = 'Validado'
        AND validado_por = ?
        AND cantidad_produccion > 0
    ORDER BY fecha_registro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userqa);
        $stmt->execute();
        $result = $stmt->get_result();

        $entregas = [];
        $control = new Control();

        while ($row = $result->fetch_assoc()) {
            $row['nombre_maquina'] = $control->getNameMaquina($row['maquina']);
            $entregas[] = $row;
        }

        return $entregas;
    }


    private function getNombreEmpleado($codigo_empleado)
    {
        $query = "SELECT nombre FROM users WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $codigo_empleado);
        $stmt->execute();
        $result = $stmt->get_result();
        $empleado = $result->fetch_assoc();
        return $empleado ? $empleado['nombre'] : 'Desconocido';
    }

    public function obtenerRegistroPorId($id)
    {
        try {
            $query = "SELECT * FROM registros WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Obtiene el conteo de entregas pendientes por área
     */
    public function getCountEntregasPendientes($area_id)
    {
        $query = "
        SELECT
            SUM(CASE WHEN cantidad_produccion > 0 THEN 1 ELSE 0 END) AS total_produccion,
            SUM(CASE WHEN cantidad_scrapt > 0 THEN 1 ELSE 0 END) AS total_scrap,
            COUNT(*) AS total
        FROM registro
        WHERE estado_validacion = 'Pendiente'
            AND area_id = ?
            AND (
                (tipo_boton = 'Producción' AND descripcion = 'Parcial')
                OR tipo_boton = 'final_produccion'
            )
            AND (cantidad_produccion > 0 OR cantidad_scrapt > 0)
    ";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('i', $area_id);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                return [
                    'total' => (int) ($row['total'] ?? 0),
                    'total_scrap' => (int) ($row['total_scrap'] ?? 0),
                    'total_produccion' => (int) ($row['total_produccion'] ?? 0),
                ];
            } else {
                // Manejo de error en ejecución
                error_log("Error al ejecutar getCountEntregasPendientes: " . $stmt->error);
            }
            $stmt->close();
        } else {
            // Manejo de error en preparación
            error_log("Error al preparar getCountEntregasPendientes: " . $this->db->error);
        }

        // En caso de error, retorna todo en 0
        return [
            'total' => 0,
            'total_scrap' => 0,
            'total_produccion' => 0
        ];
    }


    public function getCountEntregasEnProceso($area_id)
    {
        $query = "SELECT 
            COUNT(*) as total
            FROM registro 
            WHERE estado_validacion = 'Correccion' 
            AND area_id = ?";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $area_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return $row['total'] ?? 0;
        } catch (\Exception $e) {
            error_log("Error en getCountEntregasEnProceso: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene el conteo de entregas validadas
     */
    public function getCountEntregasValidadas($area_id)
    {
        $query = "SELECT 
            SUM(
            CASE 
                WHEN estado_validacion = 'validado' AND cantidad_produccion > 0 THEN 1
                ELSE 0
            END
        ) AS total
            FROM registro
            WHERE area_id = ?
        AND (
        (tipo_boton = 'final_produccion') 
        OR (tipo_boton = 'Producción' AND descripcion = 'Parcial')
    )";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'] ?? 0;
    }

    public function getEntregasProduccionValidadas($userqa)
    {
        $query = "SELECT 
        r.id,
        r.codigo_empleado,
        u.nombre AS nombre_empleado,
        r.maquina,
        m.nombre AS nombre_maquina,
        r.item,
        r.jtWo,
        r.cantidad_produccion,
        r.estado_validacion
    FROM registro r
    LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
    LEFT JOIN maquinas m ON r.maquina = m.id
    WHERE 
        r.estado_validacion = 'Validado' 
        AND r.validado_por = ?
        AND r.cantidad_produccion > 0
    ORDER BY 
        r.fecha_registro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userqa);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    /**
     * Obtiene estadísticas para el dashboard
     */
    public function getDashboardStats($area_id)
    {
        // Obtener las estadísticas de entregas pendientes
        $pendientes = $this->getCountEntregasPendientes($area_id);

        // Instanciar el modelo de retenciones
        $retencionModel = new Retencion();
        $retenciones = $retencionModel->getRetencionesActivas($area_id);

        // Obtener las entregas en proceso
        $en_proceso = $this->getCountEntregasEnProceso($area_id);

        // Obtener las entregas validadas
        $validadas = $this->getCountEntregasValidadas($area_id);

        // Consolidar estadísticas para el dashboard
        return [
            'pendientes' => (int) $pendientes['total'],
            'produccion_pendiente' => (int) $pendientes['total_produccion'],
            'scrap_pendiente' => (int) $pendientes['total_scrap'],
            'validadas' => (int) $validadas,
            'retenciones' => $retenciones,
            'en_proceso' => (int) $en_proceso
        ];
    }


    public function getDestinosStats($areaId = null, $userId = null)
    {
        try {
            $query = "
                SELECT 
                    rd.tipo_destino,
                    COUNT(*) as total,
                    SUM(rd.cantidad) as cantidad_total
                FROM retencion_destinos rd
                INNER JOIN retenciones r ON rd.retencion_id = r.id
                INNER JOIN registro reg ON r.registro_id = reg.id
                WHERE 1=1
                " . ($areaId ? "AND reg.area_id = ?" : "") . "
                " . ($userId ? "AND r.usuario_id = ?" : "") . "
                GROUP BY rd.tipo_destino";

            $stmt = $this->db->prepare($query);

            // Preparar los parámetros de manera dinámica
            $types = '';
            $params = [];

            if ($areaId) {
                $types .= 'i';
                $params[] = $areaId;
            }
            if ($userId) {
                $types .= 'i';
                $params[] = $userId;
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $stats = [
                'produccion' => ['total' => 0, 'cantidad' => 0],
                'retrabajo' => ['total' => 0, 'cantidad' => 0],
                'destruccion' => ['total' => 0, 'cantidad' => 0]
            ];

            while ($row = $result->fetch_assoc()) {
                $tipo = $row['tipo_destino'] === 'produccion_final' ? 'produccion' : $row['tipo_destino'];
                $stats[$tipo] = [
                    'total' => (int)$row['total'],
                    'cantidad' => (float)$row['cantidad_total']
                ];
            }

            return $stats;
        } catch (\Exception $e) {
            error_log("Error en getDestinosStats: " . $e->getMessage());
            return [
                'produccion' => ['total' => 0, 'cantidad' => 0],
                'retrabajo' => ['total' => 0, 'cantidad' => 0],
                'destruccion' => ['total' => 0, 'cantidad' => 0]
            ];
        }
    }

    public function getEntregasProduccionPendientes($area_id)
    {
        $query = "
            SELECT 
                r.*,
                m.nombre as nombre_maquina,
                u.nombre as nombre_empleado
            FROM registro r
            LEFT JOIN maquinas m ON r.maquina = m.id
            LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
            WHERE r.estado_validacion = 'Pendiente'
            AND r.area_id = ?
            AND r.cantidad_produccion > 0
            ORDER BY r.fecha_registro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getEntregasScrapPendientes($area_id)
    {
        $query = "
            SELECT 
                r.*,
                m.nombre as nombre_maquina,
                u.nombre as nombre_empleado
            FROM registro r
            LEFT JOIN maquinas m ON r.maquina = m.id
            LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
            WHERE r.estado_validacion = 'Pendiente'
            AND r.area_id = ?
            AND r.cantidad_scrapt > 0
            ORDER BY r.fecha_registro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
