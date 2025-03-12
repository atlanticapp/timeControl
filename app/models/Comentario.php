<?php

namespace App\Models;

use App\Core\Model;

class Comentario extends Model
{
    protected $table = 'registro';

    public function getLastRegistro($codigo_empleado)
    {
        $sql_get_last_registro = "SELECT id FROM registro WHERE codigo_empleado = ? AND fecha_fin IS NULL ORDER BY id DESC LIMIT 1";
        $stmt_get_last_registro = $this->db->prepare($sql_get_last_registro);
        $stmt_get_last_registro->bind_param("i", $codigo_empleado);
        $stmt_get_last_registro->execute();
        $stmt_get_last_registro->bind_result($id_registro);
        $stmt_get_last_registro->fetch();
        $stmt_get_last_registro->close();

        return $id_registro;
    }

    public function addComentario($comentario, $id_registro)
    {
        $sql_insert_comentario = "UPDATE registro SET comentario = ? WHERE id = ?";
        $stmt_insert_comentario = $this->db->prepare($sql_insert_comentario);
        $stmt_insert_comentario->bind_param("si", $comentario, $id_registro);

        if ($stmt_insert_comentario->execute()) {
            $stmt_insert_comentario->close();
            return true;
        } else {
            $stmt_insert_comentario->close();
            return false;
        }
    }
}
