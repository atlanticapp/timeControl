<?php

namespace App\Models;

use App\Core\Model;

class Supervisor extends Model
{
    public function getOperacionesAbiertas($area_id, $codigo_empleado = '', $tipo_boton = '', $maquina = '')
    {

        $sql = "SELECT r.*, u.nombre, m.nombre AS nombre_maquina 
                FROM registro r 
                JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                JOIN maquinas m ON r.maquina = m.id
                WHERE r.fecha_fin IS NULL AND u.area_id = ?";

        $params = [$area_id];

        if (!empty($codigo_empleado)) {
            $sql .= " AND r.codigo_empleado = ?";
            $params[] = $codigo_empleado;
        }

        if (!empty($tipo_boton)) {
            $sql .= " AND r.tipo_boton = ?";
            $params[] = $tipo_boton;
        }

        if (!empty($maquina)) {
            $sql .= " AND r.maquina = ?";
            $params[] = $maquina;
        }

        $stmt = $this->db->prepare($sql);

        if ($stmt) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            $operaciones_abiertas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $operaciones_abiertas;
        }

        throw new \Exception("Error al preparar la consulta de operaciones abiertas");
    }

    public function getEmpleadosPorArea($area_id)
    {

        $sql = "SELECT DISTINCT u.codigo_empleado, u.nombre 
                FROM registro r 
                JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                WHERE r.fecha_fin IS NULL AND u.area_id = ?";

        $stmt = $this->db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $area_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $empleados = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $empleados;
        }

        throw new \Exception("Error al obtener los empleados");
    }

    public function getTiposBotones()
    {
        $sql = "SELECT DISTINCT tipo_boton FROM registro WHERE fecha_fin IS NULL";
        $result = $this->db->query($sql);

        if ($result) {
            $botones = $result->fetch_all(MYSQLI_ASSOC);
            return $botones;
        }

        throw new \Exception("Error al obtener los tipos de botones");
    }

    public function getMaquinasPorArea($area_id)
    {
        $sql = "SELECT DISTINCT m.id, m.nombre 
                FROM registro r 
                JOIN maquinas m ON r.maquina = m.id 
                JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                WHERE r.fecha_fin IS NULL AND u.area_id = ?";

        $stmt = $this->db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $area_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $maquinas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $maquinas;
        }

        throw new \Exception("Error al obtener las máquinas");
    }

    public function getProduccionDiaria($area_id, $item = null, $jtWo = null)
    {
        $sql = "SELECT r.*, u.codigo_empleado, u.nombre AS nombre_empleado, 
                m.nombre AS nombre_maquina, r.fecha_registro
            FROM registro r 
            JOIN users u ON r.codigo_empleado = u.codigo_empleado 
            JOIN maquinas m ON r.maquina = m.id
            WHERE (r.tipo_boton = 'final_produccion' OR r.descripcion = 'Parcial') 
            AND u.area_id = ? 
            AND DATE(r.fecha_registro) = CURDATE()";

        $params = [$area_id];
        $param_types = 'i';

        if (!empty($item)) {
            $sql .= " AND r.item = ?";
            $params[] = $item;
            $param_types .= 's';
        }

        if (!empty($jtWo)) {
            $sql .= " AND r.jtWo = ?";
            $params[] = $jtWo;
            $param_types .= 's';
        }

        if (!$stmt = $this->db->prepare($sql)) {
            throw new \Exception("Error al preparar la consulta de producción diaria");
        }

        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $totalProduccion = 0;
        $totalScrap = 0;
        $produccion_por_maquina_empleado = [];

        while ($row = $result->fetch_assoc()) {
            $maquina_id = $row['maquina'];
            $empleado_codigo = $row['codigo_empleado'];

            $produccion_por_maquina_empleado[$maquina_id]['nombre_maquina'] = $row['nombre_maquina'];
            $produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['nombre_empleado'] = $row['nombre_empleado'];

            $produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['total_produccion'] =
                ($produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['total_produccion'] ?? 0) + $row['cantidad_produccion'];

            $produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['total_scrap'] =
                ($produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['total_scrap'] ?? 0) + $row['cantidad_scrapt'];

            // Guardar fecha de entrega
            $produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['fecha_registro'] = $row['fecha_registro'];

            $totalProduccion += $row['cantidad_produccion'];
            $totalScrap += $row['cantidad_scrapt'];
        }


        $stmt->close();

        return [
            'produccion_por_maquina_empleado' => $produccion_por_maquina_empleado,
            'totalProduccion' => $totalProduccion,
            'totalScrap' => $totalScrap
        ];
    }
}
