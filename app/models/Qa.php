<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Control;
use PDO;
use PDOException;

class Qa extends Model
{
    protected $table = 'registro';

    // Obtener entregas pendientes de validación
    public function getEntregasPendientes($area_id)
    {
        // Consulta base para obtener todos los registros pendientes
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
            estado
        FROM registro
        WHERE estado = 'Pendiente'
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

        $entregas_produccion = [];
        $entregas_scrap = [];
        $control = new Control();

        while ($row = $result->fetch_assoc()) {
            $nombre_empleado = $this->getNombreEmpleado($row['codigo_empleado']);
            $nombre_maquina = $control->getNameMaquina($row['maquina']);

            // Datos comunes
            $datos_comunes = [
                'id' => $row['id'],
                'maquina' => $row['maquina'],
                'jtWo' => $row['jtWo'],
                'item' => $row['item'],
                'area_id' => $row['area_id'],
                'codigo_empleado' => $row['codigo_empleado'],
                'tipo_boton' => $row['tipo_boton'],
                'descripcion' => $row['descripcion'],
                'fecha_registro' => $row['fecha_registro'],
                'nombre_empleado' => $nombre_empleado,
                'nombre_maquina' => $nombre_maquina
            ];

            // Crear registro para producción si hay cantidad
            if ($row['cantidad_produccion'] > 0) {
                $produccion = $datos_comunes;
                $produccion['cantidad'] = $row['cantidad_produccion'];
                $produccion['tipo_registro'] = 'produccion';
                $entregas_produccion[] = $produccion;
            }

            // Crear registro para scrap si hay cantidad
            if ($row['cantidad_scrapt'] > 0) {
                $scrap = $datos_comunes;
                $scrap['cantidad'] = $row['cantidad_scrapt'];
                $scrap['tipo_registro'] = 'scrap';
                $entregas_scrap[] = $scrap;
            }
        }

        return [
            'entregas_produccion' => $entregas_produccion,
            'entregas_scrap' => $entregas_scrap
        ];
    }


    // Validar entrega (aceptar)
    public function validarEntrega($codigo_empleado_qa, $idEntrega)
    {
        $query = "
            UPDATE registro
            SET estado = 'Validado',
                validado_por = ?,
                fecha_validacion = NOW()
            WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $codigo_empleado_qa, $idEntrega);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // Enviar corrección al operador
    public function enviarCorreccion($codigo_empleado, $maquina_id, $item, $jtwo, $comentario)
    {
        $query = "
            UPDATE registro
            SET estado = 'Correccion',
                comentario_qa = ?
            WHERE codigo_empleado = ?
            AND maquina = ?
            AND item = ?
            AND jtWo = ?
            AND tipo_boton = 'final_produccion'
            AND descripcion = 'Parcial'";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("siiss", $comentario, $codigo_empleado, $maquina_id, $item, $jtwo);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // Obtener entregas validadas para el panel principal
    public function getEntregasValidadas($userqa)
    {
        $query = "
      SELECT 
            id,
            tipo_boton,
            codigo_empleado,
            maquina,
            item,
            jtWo,
            cantidad_produccion,
            cantidad_scrapt,
            fecha_validacion,
            validado_por,
            descripcion,
            estado
        FROM registro
        WHERE estado = 'Validado'
            AND validado_por = ?";

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
        $query = "
            WITH entregas_finales AS (
            SELECT maquina, jtWo, item, codigo_empleado
            FROM registro
            WHERE tipo_boton = 'final_produccion'
            AND estado = 'Pendiente'
            AND area_id = ?
            ),
            entregas_parciales AS (
            SELECT maquina, jtWo, item, codigo_empleado
            FROM registro
            WHERE tipo_boton = 'Producción'
            AND descripcion = 'Parcial'
            AND estado = 'Pendiente'
            AND area_id = ?
            )
            SELECT 
            COUNT(*) AS total_entregas
            FROM (
            SELECT maquina, jtWo, item, codigo_empleado FROM entregas_finales
            UNION ALL 
            SELECT maquina, jtWo, item, codigo_empleado FROM entregas_parciales
            ) AS todas_entregas";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $area_id, $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total_entregas'] ?? 0;
    }

    /**
     * Obtiene el conteo de entregas validadas
     */
    public function getCountEntregasValidadas($area_id)
    {
        $query = "
            SELECT COUNT(DISTINCT CONCAT(codigo_empleado, maquina, jtWo, item)) as total
            FROM registro
            WHERE area_id = ?            AND tipo_boton = 'final_produccion' 
            AND descripcion = 'Parcial'
            AND estado = 'Validado'";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    // /**
    //  * Obtiene estadísticas de producción por máquina
    //  */
    // public function getEstadisticasPorMaquina()
    // {
    //     $query = "
    //         SELECT 
    //             r.maquina,
    //             m.nombre AS nombre_maquina,
    //             r.jtWo,
    //             SUM(r.cantidad_produccion) AS produccion,
    //             SUM(r.cantidad_scrapt) AS scrapt,
    //             COUNT(DISTINCT CONCAT(r.codigo_empleado, r.jtWo, r.item)) AS entregas
    //         FROM registro r
    //         JOIN maquinas m ON r.maquina = m.id
    //         WHERE (r.tipo_boton = 'final_produccion' OR (r.tipo_boton = 'Producción' AND r.descripcion = 'Parcial'))
    //         AND r.estado = 'Pendiente'
    //         GROUP BY r.maquina, m.nombre, r.jtWo
    //         ORDER BY produccion DESC";

    //     $stmt = $this->db->prepare($query);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $stats = [];

    //     while ($row = $result->fetch_assoc()) {
    //         $stats[] = $row;
    //     }

    //     return $stats;
    // }



    /**
     * Obtiene estadísticas para el dashboard
     */
    public function getDashboardStats($area_id)
    {
        $stats = [
            'pendientes' => $this->getCountEntregasPendientes($area_id),
            'validadas' => $this->getCountEntregasValidadas($area_id)
        ];

        return $stats;
    }
}
