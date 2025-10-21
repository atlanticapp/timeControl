<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modelo para manejar usuarios en la tabla 'users'
 */
class Usuario extends Model
{
    protected $table = 'users';

    
    public function findByCodigo($codigo)
    {
        // Inicializar variable para evitar warning del IDE
        $user = null;

        // Nota: No eliminar area_id para supervisores; ellos tambien tienen area asignada
        $sql = "SELECT id, nombre, codigo_empleado, password, tipo_usuario, area_id, maquina_id, jtWo, item, po, cliente, active_button_id FROM {$this->table} WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $codigo);  // "i" para INT
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }

        
        return $user ?? null;
    }

    public function create($data)
    {
        // Inicializar para logs
        $success = false;

        // Tipos correctos: s (nombre), i (codigo_empleado), s (password), s (tipo_usuario), i (area_id)
        $sql = "INSERT INTO {$this->table} (nombre, codigo_empleado, password, tipo_usuario, area_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "sissi",  // s (nombre), i (codigo_empleado), s (password), s (tipo_usuario), i (area_id)
            $data['nombre'],
            $data['codigo_empleado'], 
            $data['password'],
            $data['tipo_usuario'],
            $data['area_id']
        );

        $success = $stmt->execute();

        if (!$success) {
            error_log("Error MySQL en create usuario: " . $stmt->error);
        }

        $stmt->close();
        return $success;
    }

    public function getAllAreas()
    {
        $areas = [];  // Inicializar para IDE

        $sql = "SELECT id, nombre FROM area ORDER BY nombre ASC";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $areas[] = $row;
            }
        }

        return $areas;
    }

    public function getNameArea($area_id)
    {
        if ($area_id === null || $area_id === '' || $area_id === 0) {
            return "Sin área asignada";
        }

        $nombreArea = null;  // Inicialización explícita para IDE

        $areaSql = "SELECT nombre FROM area WHERE id = ?";
        $areaStmt = $this->db->prepare($areaSql);
        $areaStmt->bind_param("i", $area_id);  // "i" para INT
        $areaStmt->execute();
        $areaStmt->bind_result($nombreArea);
        $areaStmt->fetch();
        $areaStmt->close();

        // Retorno explícito
        return $nombreArea ?? "Área Desconocida";
    }

  
    public function findQAUsersByArea($areaId)
    {
        $qaUsers = [];  // Inicializar para IDE

        $sql = "SELECT * FROM {$this->table} WHERE area_id = ? AND tipo_usuario = 'qa'";  // Exacto 'qa'
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $areaId);  // "i" para INT
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $qaUsers = $result->fetch_all(MYSQLI_ASSOC);
        }

        $stmt->close();
        return $qaUsers;
    }

    
    public function isSupervisor($codigo)
    {
        $isSupervisor = false;  // Inicializar para IDE

        $sql = "SELECT tipo_usuario FROM {$this->table} WHERE codigo_empleado = ? AND tipo_usuario = 'supervisor'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $codigo);  // "i" para INT
        $stmt->execute();
        $result = $stmt->get_result();

        $isSupervisor = $result->num_rows > 0;

        $stmt->close();
        return $isSupervisor;
    }

    
    public function getIdByCodigo($codigo)
    {
        $userId = null;  // Inicializar para IDE

        $sql = "SELECT id FROM {$this->table} WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $codigo);  // "i" para INT
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row['id'];
        }

        $stmt->close();
        return $userId;
    }



   
    public function updateMaquina($codigoEmpleado, $maquinaId)
    {
        if ($maquinaId === null || $maquinaId <= 0) {
            return false;
        }

        $success = false;  // Inicializar para IDE

        $query = "UPDATE {$this->table} SET maquina_id = ? WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $maquinaId, $codigoEmpleado);  // Ambos INT
        $success = $stmt->execute();

        if (!$success) {
            error_log("Error MySQL en updateMaquina: " . $stmt->error . " | codigo: {$codigoEmpleado}, maquina: {$maquinaId}");
        }

        $stmt->close();
        return $success;
    }

  
    public function updateDatosTrabajo($codigoEmpleado, $jtWo, $item, $po, $cliente)
    {
        if (empty($jtWo) || empty($item) || empty($po) || empty($cliente)) {
            return false;
        }

        // Validar longitudes (varchar(255) en DB)
        if (strlen($jtWo) > 255 || strlen($item) > 255 || strlen($po) > 255 || strlen($cliente) > 255) {
            return false;
        }

        $success = false;  // Inicializar para IDE

        $query = "UPDATE {$this->table} SET jtWo = ?, item = ?, po = ?, cliente = ? WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssi", $jtWo, $item, $po, $cliente, $codigoEmpleado);  // sss para strings, i para INT
        $success = $stmt->execute();

        if (!$success) {
            error_log("Error MySQL en updateDatosTrabajo: " . $stmt->error . " | codigo: {$codigoEmpleado}");
        }

        $stmt->close();
        return $success;
    }

    
    public function getMaquinasByArea($areaId)
    {
        $maquinas = [];  // Inicializar para IDE

        if ($areaId <= 0) {
            return $maquinas;
        }

        $query = "SELECT id, nombre FROM maquinas WHERE area_id = ? ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $areaId);  // INT
        $stmt->execute();
        $result = $stmt->get_result();
        $maquinas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $maquinas;
    }

   
    public function getNameMaquina($maquinaId)
    {
        if ($maquinaId === null || $maquinaId <= 0) {
            return "No asignada";
        }

        $nombreMaquina = null; 

        $query = "SELECT nombre FROM maquinas WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $maquinaId);  // INT
        $stmt->execute();
        $stmt->bind_result($nombreMaquina);
        $stmt->fetch();
        $stmt->close();

        // Retorno explícito
        return $nombreMaquina ?? "Máquina Desconocida";
    }

    public function getActiveButton($codigoEmpleado)
    {
        $activeButtonId = null;  // Inicializar para IDE

        $query = "SELECT active_button_id FROM {$this->table} WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $codigoEmpleado);  // INT
        $stmt->execute();
        $stmt->bind_result($activeButtonId);
        $stmt->fetch();
        $stmt->close();

        
        return $activeButtonId ?? 'defaultButtonId';
    }

   
    public function actualizarEstadoBoton($codigoEmpleado, $nuevoTipoBoton)
    {
        $success = false;  // Inicializar para IDE

        $query = "UPDATE {$this->table} SET active_button_id = ? WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $nuevoTipoBoton, $codigoEmpleado);  // s para string, i para INT
        $success = $stmt->execute();

        if (!$success) {
            error_log("Error MySQL en actualizarEstadoBoton: " . $stmt->error . " | codigo: {$codigoEmpleado}, boton: {$nuevoTipoBoton}");
        }

        $stmt->close();
        return $success;
    }
    public function updatePassword($codigo_empleado, $nueva_password_hash)
{
    $success = false;

    $sql = "UPDATE {$this->table} SET password = ? WHERE codigo_empleado = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("si", $nueva_password_hash, $codigo_empleado); // s para string (password), i para INT (codigo_empleado)
    
    $success = $stmt->execute();

    if (!$success) {
        error_log("Error MySQL en updatePassword: " . $stmt->error);
    }

    $stmt->close();
    return $success;
}
public function getAreaIdByMaquina($maquinaId)
    {
        $areaId = null;
        
        $sql = "SELECT area_id FROM maquinas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maquinaId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $areaId = $row['area_id'];
        }
        
        $stmt->close();
        return $areaId;
    }

    /**
     * Obtener lista de operadores
     */
    public function getOperadores()
    {
        $operadores = [];
        
        $sql = "SELECT codigo_empleado, nombre, area_id 
                FROM {$this->table} 
                WHERE tipo_usuario = 'operador' 
                ORDER BY nombre ASC";
        
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $operadores[] = $row;
            }
        }
        
        return $operadores;
    }
    public function getDb()
{
    return $this->db;
}

    
    
}