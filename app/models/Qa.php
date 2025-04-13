<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Control;
use PDOException;

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
        ORDER BY fecha_registro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $entregas = [
            'entregas_produccion' => [],
            'entregas_scrap' => []
        ];

        while ($row = $result->fetch_assoc()) {
            $datosComunes = $this->prepararDatosComunes($row);

            switch ($row['estado_validacion']) {
                case 'Pendiente':
                    if ($row['cantidad_produccion'] > 0) {
                        $entregas['entregas_produccion'][] = array_merge($datosComunes, [
                            'cantidad' => $row['cantidad_produccion'],
                            'tipo_registro' => 'produccion',
                            'estado_validacion' => 'Pendiente'
                        ]);
                    }

                    if ($row['cantidad_scrapt'] > 0) {
                        $entregas['entregas_scrap'][] = array_merge($datosComunes, [
                            'cantidad' => $row['cantidad_scrapt'],
                            'tipo_registro' => 'scrap',
                            'estado_validacion' => 'Pendiente'
                        ]);
                    }
                    break;
            }
        }

        return $entregas;
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

    public function getEntregasValidadasProduccion($userqa)
    {
        $query = "SELECT *
    FROM registro
    WHERE estado_validacion = 'Validado'
        AND validado_por = ?
        AND cantidad_produccion > 0";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userqa);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
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
        $query = "SELECT 
                SUM(CASE 
                    WHEN estado_validacion = 'Pendiente' AND cantidad_produccion > 0 THEN 1 
                    ELSE 0 
                END) AS total_produccion,
                SUM(CASE 
                    WHEN estado_validacion = 'Pendiente' AND cantidad_scrapt > 0 THEN 1 
                    ELSE 0 
                END) AS total_scrap,
                COUNT(*) AS total
            FROM registro
            WHERE estado_validacion = 'Pendiente'
            AND area_id = ?
            AND (
                (tipo_boton = 'Producción' AND descripcion = 'Parcial') 
                OR tipo_boton = 'final_produccion'
            )";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return [
            'total' => $row['total'] ?? 0,
            'total_scrap' => $row['total_scrap'] ?? 0,
            'total_produccion' => $row['total_produccion'] ?? 0
        ];
    }

    public function getCountEntregasEnProceso($area_id)
    {
        $query = "SELECT 
                SUM(CASE 
                        WHEN estado_validacion = 'Corregir' THEN 1 
                        ELSE 0 
                        END) AS total
                FROM registro
                WHERE area_id = ?";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$area_id]);
            $result = $stmt->fetch();  // Sin usar PDO::FETCH_ASSOC

            return $result['total'] ?? 0;  // Retorna el total o 0 si no hay datos
        } catch (PDOException $e) {
            // En caso de error, loguea el error y retorna 0
            error_log("Error en getCountEntregasEnProceso: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene el conteo de entregas validadas
     */
    public function getCountEntregasValidadas($area_id)
    {
        $query = "  SELECT 
            SUM(
            CASE 
                WHEN estado_validacion = 'validado' THEN 1
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

        // Obtener las entregas en proceso
        $en_proceso = $this->getCountEntregasEnProceso($area_id);

        // Recopilar estadísticas para el dashboard
        $stats = [
            'pendientes' => $pendientes['total'],
            'scrap_pendientes' => $pendientes['total_scrap'],
            'produccion_pendiente' => $pendientes['total_produccion'],
            'validadas' => $this->getCountEntregasValidadas($area_id),
            'en_proceso' => $en_proceso // Número de entregas en proceso (estado 'Corregir')
        ];

        return $stats;
    }
}
