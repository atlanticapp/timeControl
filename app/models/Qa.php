<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Control;
use Exception;

class Qa extends Model
{
    protected $table = 'registro';

    
    public function getEntregasValidadasProduccion($userqa)
    {
        try {
            
            $query = "SELECT r.*,
                        u.nombre AS nombre_empleado,
                        m.nombre AS nombre_maquina,
                        supervisor.nombre AS nombre_supervisor
                FROM registro r
                LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                LEFT JOIN maquinas m ON r.maquina = m.id
                LEFT JOIN users supervisor ON r.validado_por = supervisor.codigo_empleado
                WHERE r.estado_validacion = 'Validado'
                    AND r.cantidad_produccion > 0
                    AND (
                        r.validado_por = ? 
                        OR EXISTS (
                            SELECT 1 FROM users s 
                            WHERE s.codigo_empleado = r.validado_por 
                            AND s.tipo_usuario = 'supervisor'
                            AND s.area_id = (SELECT area_id FROM users WHERE codigo_empleado = ?)
                        )
                    )
                ORDER BY r.fecha_registro DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ss", $userqa, $userqa);
            $stmt->execute();
            $result = $stmt->get_result();

            $entregas = [];
            $control = new Control();

            while ($row = $result->fetch_assoc()) {
                
                $row['validado_por_nombre'] = $row['nombre_supervisor'] ?: 'Sistema';
                $row['validado_por_tipo'] = $this->getTipoUsuario($row['validado_por']);
                
                
                if (!$row['nombre_maquina']) {
                    $row['nombre_maquina'] = $control->getNameMaquina($row['maquina']);
                }
                
                $entregas[] = $row;
            }

            $stmt->close();
            return $entregas;
        } catch (Exception $e) {
            error_log("Error en getEntregasValidadasProduccion: " . $e->getMessage() . " | userqa: $userqa");
            return [];
        }
    }

    
    private function getTipoUsuario($codigoEmpleado)
    {
        try {
            $query = "SELECT tipo_usuario FROM users WHERE codigo_empleado = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $codigoEmpleado);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $usuario = $result->fetch_assoc();
                return $usuario['tipo_usuario'];
            }
            
            return 'desconocido';
        } catch (Exception $e) {
            error_log("Error en getTipoUsuario: " . $e->getMessage());
            return 'error';
        }
    }

  
    public function getEntregasValidadasProduccionSimple($userqa)
    {
        try {
            $query = "SELECT r.*,
                        u.nombre AS nombre_empleado,
                        m.nombre AS nombre_maquina,
                        validador.nombre AS validado_por_nombre,
                        validador.tipo_usuario AS validado_por_tipo
                FROM registro r
                LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                LEFT JOIN maquinas m ON r.maquina = m.id
                LEFT JOIN users validador ON r.validado_por = validador.codigo_empleado
                WHERE r.estado_validacion = 'Validado'
                    AND r.cantidad_produccion > 0
                    AND r.area_id = (SELECT area_id FROM users WHERE codigo_empleado = ?)
                ORDER BY r.fecha_registro DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $userqa);
            $stmt->execute();
            $result = $stmt->get_result();

            $entregas = [];
            $control = new Control();

            while ($row = $result->fetch_assoc()) {
                // Mantener compatibilidad
                if (!$row['nombre_maquina']) {
                    $row['nombre_maquina'] = $control->getNameMaquina($row['maquina']);
                }
                
                $entregas[] = $row;
            }

            $stmt->close();
            return $entregas;
        } catch (Exception $e) {
            error_log("Error en getEntregasValidadasProduccionSimple: " . $e->getMessage() . " | userqa: $userqa");
            return [];
        }
    }

    

    // Obtener entregas pendientes de validacion
    public function getEntregasPendientes($area_id)
    {
        try {
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
                $key = $row['fecha_registro'] . '_' . $row['maquina'] . '_' . $row['jtWo'] . '_' .
                    $row['item'] . '_' . $row['codigo_empleado'];

                if (!isset($entregasAgrupadas[$key])) {
                    $entregasAgrupadas[$key] = [
                        'info_comun' => $this->prepararDatosComunes($row),
                        'entregas' => []
                    ];
                }

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

            uasort($entregasAgrupadas, function ($a, $b) {
                return strtotime($b['info_comun']['fecha_registro']) - strtotime($a['info_comun']['fecha_registro']);
            });

            $stmt->close();
            return array_values($entregasAgrupadas);
        } catch (Exception $e) {
            error_log("Error en getEntregasPendientes: " . $e->getMessage() . " | area_id: $area_id");
            return [];
        }
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

    public function verificarEstadosRegistros($registrosIds, $area_id)
    {
        try {
            if (empty($registrosIds)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($registrosIds), '?'));
            $query = "SELECT id, estado_validacion FROM registro WHERE id IN ($placeholders) AND area_id = ?";
            $stmt = $this->db->prepare($query);
            $types = str_repeat('i', count($registrosIds)) . 'i';
            $params = array_merge($registrosIds, [$area_id]);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            $estados = [];
            while ($row = $result->fetch_assoc()) {
                $estados[$row['id']] = $row['estado_validacion'];
            }

            $stmt->close();
            return $estados;
        } catch (Exception $e) {
            error_log("Error en verificarEstadosRegistros: " . $e->getMessage() . " | area_id: $area_id, registrosIds: " . json_encode($registrosIds));
            return [];
        }
    }

    public function verificarRegistroPendiente($registroId, $area_id)
    {
        try {
            $query = "SELECT estado_validacion FROM registro WHERE id = ? AND area_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $registroId, $area_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return false;
            }

            $registro = $result->fetch_assoc();
            $stmt->close();
            return $registro['estado_validacion'] === 'Pendiente';
        } catch (Exception $e) {
            error_log("Error en verificarRegistroPendiente: " . $e->getMessage() . " | registroId: $registroId, area_id: $area_id");
            return false;
        }
    }

    private function getNombreEmpleado($codigo_empleado)
    {
        try {
            $query = "SELECT nombre FROM users WHERE codigo_empleado = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $codigo_empleado);
            $stmt->execute();
            $result = $stmt->get_result();
            $empleado = $result->fetch_assoc();
            $stmt->close();
            return $empleado ? $empleado['nombre'] : 'Desconocido';
        } catch (Exception $e) {
            error_log("Error en getNombreEmpleado: " . $e->getMessage() . " | codigo_empleado: $codigo_empleado");
            return 'Desconocido';
        }
    }

    public function obtenerRegistroPorId($id)
    {
        try {
            $query = "SELECT * FROM registro WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $registro = $result->fetch_assoc();
            $stmt->close();
            return $registro ?: false;
        } catch (Exception $e) {
            error_log("Error en obtenerRegistroPorId: " . $e->getMessage() . " | id: $id");
            return false;
        }
    }

    public function getCountEntregasPendientes($area_id)
    {
        try {
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
                    AND (cantidad_produccion > 0 OR cantidad_scrapt > 0)";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $area_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();
            return [
                'total' => (int) ($row['total'] ?? 0),
                'total_scrap' => (int) ($row['total_scrap'] ?? 0),
                'total_produccion' => (int) ($row['total_produccion'] ?? 0),
            ];
        } catch (Exception $e) {
            error_log("Error en getCountEntregasPendientes: " . $e->getMessage() . " | area_id: $area_id");
            return [
                'total' => 0,
                'total_scrap' => 0,
                'total_produccion' => 0
            ];
        }
    }

    public function getCountEntregasEnProceso($area_id)
    {
        try {
            $query = "SELECT 
                COUNT(*) as total
                FROM registro 
                WHERE estado_validacion = 'Correccion' 
                AND area_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $area_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();
            return $row['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Error en getCountEntregasEnProceso: " . $e->getMessage() . " | area_id: $area_id");
            return 0;
        }
    }

    public function getCountEntregasValidadas($area_id)
    {
        try {
            $query = "SELECT 
                SUM(
                    CASE 
                        WHEN estado_validacion = 'Validado' AND cantidad_produccion > 0 THEN 1
                        ELSE 0
                    END
                ) AS total
                FROM registro
                WHERE area_id = ?
                AND (
                    (tipo_boton = 'Producción' AND descripcion = 'Parcial')
                    OR tipo_boton = 'final_produccion'
                )";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $area_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();
            return $row['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Error en getCountEntregasValidadas: " . $e->getMessage() . " | area_id: $area_id");
            return 0;
        }
    }

    public function getEntregasProduccionValidadas($userqa)
    {
        try {
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

            $entregas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $entregas;
        } catch (Exception $e) {
            error_log("Error en getEntregasProduccionValidadas: " . $e->getMessage() . " | userqa: $userqa");
            return [];
        }
    }

    public function getDashboardStats($area_id)
    {
        try {
            $pendientes = $this->getCountEntregasPendientes($area_id);
            $retencionModel = new Retencion();
            $retenciones = $retencionModel->getRetencionesActivas($area_id);
            $en_proceso = $this->getCountEntregasEnProceso($area_id);
            $validadas = $this->getCountEntregasValidadas($area_id);

            return [
                'pendientes' => (int) $pendientes['total'],
                'produccion_pendiente' => (int) $pendientes['total_produccion'],
                'scrap_pendiente' => (int) $pendientes['total_scrap'],
                'validadas' => (int) $validadas,
                'retenciones' => $retenciones,
                'en_proceso' => (int) $en_proceso
            ];
        } catch (Exception $e) {
            error_log("Error en getDashboardStats: " . $e->getMessage() . " | area_id: $area_id");
            return [
                'pendientes' => 0,
                'produccion_pendiente' => 0,
                'scrap_pendiente' => 0,
                'validadas' => 0,
                'retenciones' => [],
                'en_proceso' => 0
            ];
        }
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

            $stmt->close();
            return $stats;
        } catch (Exception $e) {
            error_log("Error en getDestinosStats: " . $e->getMessage() . " | areaId: $areaId, userId: $userId");
            return [
                'produccion' => ['total' => 0, 'cantidad' => 0],
                'retrabajo' => ['total' => 0, 'cantidad' => 0],
                'destruccion' => ['total' => 0, 'cantidad' => 0]
            ];
        }
    }

    public function getEntregasProduccionPendientes($area_id)
    {
        try {
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
            $result = $stmt->get_result();
            $entregas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $entregas;
        } catch (Exception $e) {
            error_log("Error en getEntregasProduccionPendientes: " . $e->getMessage() . " | area_id: $area_id");
            return [];
        }
    }

    public function getEntregasScrapPendientes($area_id)
    {
        try {
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
            $result = $stmt->get_result();
            $entregas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $entregas;
        } catch (Exception $e) {
            error_log("Error en getEntregasScrapPendientes: " . $e->getMessage() . " | area_id: $area_id");
            return [];
        }
    }
    public function getEntregasValidadasScrap($userqa)
{
    try {
        $query = "SELECT r.*,
                    u.nombre AS nombre_empleado,
                    m.nombre AS nombre_maquina,
                    supervisor.nombre AS nombre_supervisor
            FROM registro r
            LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
            LEFT JOIN maquinas m ON r.maquina = m.id
            LEFT JOIN users supervisor ON r.validado_por = supervisor.codigo_empleado
            WHERE r.estado_validacion = 'Validado'
                AND r.cantidad_scrapt > 0
                AND (
                    r.validado_por = ? 
                    OR EXISTS (
                        SELECT 1 FROM users s 
                        WHERE s.codigo_empleado = r.validado_por 
                        AND s.tipo_usuario = 'supervisor'
                        AND s.area_id = (SELECT area_id FROM users WHERE codigo_empleado = ?)
                    )
                )
            ORDER BY r.fecha_registro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $userqa, $userqa);
        $stmt->execute();
        $result = $stmt->get_result();

        $entregas = [];
        $control = new Control();

        while ($row = $result->fetch_assoc()) {
            $row['validado_por_nombre'] = $row['nombre_supervisor'] ?: 'Sistema';
            $row['validado_por_tipo'] = $this->getTipoUsuario($row['validado_por']);
            $row['tipo_entrega'] = 'scrap'; // IMPORTANTE
            
            if (!$row['nombre_maquina']) {
                $row['nombre_maquina'] = $control->getNameMaquina($row['maquina']);
            }
            
            $entregas[] = $row;
        }

        $stmt->close();
        return $entregas;
        
    } catch (Exception $e) {
        error_log("Error en getEntregasValidadasScrap: " . $e->getMessage());
        return [];
    }
}



}
