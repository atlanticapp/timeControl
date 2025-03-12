<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Comentario;

class ComentarioController extends Controller
{
    public function addComentario()
    {
        try {
            $this->validateRequestMethod('POST');

            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            $this->startSessionIfNeeded();

            $comentario = new Comentario();

            // Obtener el último registro del usuario
            $id_registro = $comentario->getLastRegistro($user->codigo_empleado);
            if (!$id_registro) {
                $this->redirectWithMessage('/timeControl/public/control', 'error', 'No se encontró un registro activo.');
            }

            $comentario_text = trim($_POST['comentario'] ?? '');
            if (empty($comentario_text)) {
                $this->redirectWithMessage('/timeControl/public/control', 'error', 'El comentario no puede estar vacío.');
            }

            // Agregar comentario al registro
            if (!$comentario->addComentario($comentario_text, $id_registro)) {
                throw new \Exception("Error al agregar el comentario.");
            }

            $this->redirectWithMessage('/timeControl/public/control', 'success', 'Comentario agregado correctamente.');
        } catch (\Exception $e) {
            $this->redirectWithMessage('/timeControl/public/error', 'error', 'Ocurrió un error inesperado.');
        }
    }

    /**
     * Método auxiliar para validar el método de solicitud
     */
    private function validateRequestMethod($method)
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            throw new \Exception("Método no permitido");
        }
    }

    /**
     * Método auxiliar para iniciar sesión si no está iniciada
     */
    private function startSessionIfNeeded()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Método auxiliar para establecer mensajes en sesión y redirigir
     */
    private function redirectWithMessage($url, $status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: $url");
        exit();
    }
}
