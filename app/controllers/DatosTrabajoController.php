<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Usuario;
use App\Models\CorreccionesOperador;
use App\Helpers\Logger;

class DatosTrabajoController extends Controller
{
    private $usuarioModel;
    private $correccionesOperador;
    private $user;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->correccionesOperador = new CorreccionesOperador();
        $this->user = AuthHelper::getCurrentUser();

        if (!$this->user) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Debes iniciar sesión para continuar.');
            return;
        }

        if ($this->user->tipo_usuario !== 'operador') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado. Solo operadores pueden acceder.');
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function datos()
    {
        if (!$this->user) {
            Logger::error('No user found in DatosTrabajoController::datos', []);
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Sesión no válida. Inicia sesión de nuevo.');
            return;
        }

        $maquinaNombre = $this->usuarioModel->getNameMaquina($this->user->maquina_id);
        if (!$this->user->maquina_id) {
            Logger::warning('No machine selected for user', ['codigo_empleado' => $this->user->codigo_empleado]);
            $this->redirectWithMessage('/timeControl/public/datos_trabajo_maquina', 'error', 'Debes seleccionar una máquina primero.');
            return;
        }

        
        $correccionesPendientes = $this->correccionesOperador->getCorreccionesPendientesPorOperador($this->user->codigo_empleado);
        $mostrarCorrecciones = !empty($correccionesPendientes);

        Logger::info('Fetching corrections in DatosTrabajoController::datos', [
            'codigo_empleado' => $this->user->codigo_empleado,
            'correcciones_count' => count($correccionesPendientes),
            'mostrar_correcciones' => $mostrarCorrecciones,
            'user_data' => ['maquina_id' => $this->user->maquina_id, 'area_id' => $this->user->area_id]
        ]);

        $activeButtonId = $this->usuarioModel->getActiveButton($this->user->codigo_empleado);

        $this->view('operador/datos_trabajo', [
            'maquina' => $maquinaNombre,
            'active_button_id' => $activeButtonId,
            'correcciones_pendientes' => $correccionesPendientes,
            'mostrar_correcciones' => $mostrarCorrecciones
        ]);
    }

    public function maquina()
    {
        if (!$this->user) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Sesión no válida. Inicia sesión de nuevo.');
            return;
        }

        $maquinas = $this->usuarioModel->getMaquinasByArea($this->user->area_id);
        $this->usuarioModel->updateMaquina($this->user->codigo_empleado, null);

        $correccionesPendientes = $this->correccionesOperador->getCorreccionesPendientesPorOperador($this->user->codigo_empleado);
        $mostrarCorrecciones = !empty($correccionesPendientes);

        $this->view('operador/seleccionar_maquina', [
            'maquinas' => $maquinas,
            'correcciones_pendientes' => $correccionesPendientes,
            'mostrar_correcciones' => $mostrarCorrecciones
        ]);
    }

    public function seleccionarMaquina()
    {
        if (!$this->user) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Sesión no válida. Inicia sesión de nuevo.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage('/timeControl/public/datos_trabajo_maquina', 'error', 'Método no permitido. Usa POST.');
            return;
        }

        $maquinaId = filter_input(INPUT_POST, 'maquina_id', FILTER_VALIDATE_INT);

        if (!$maquinaId || $maquinaId <= 0) {
            $this->redirectWithMessage('/timeControl/public/datos_trabajo_maquina', 'error', 'Selecciona una máquina válida.');
            return;
        }

        $updated = $this->usuarioModel->updateMaquina($this->user->codigo_empleado, $maquinaId);
        if (!$updated) {
            Logger::error("Error updating machine for user", [
                'codigo_empleado' => $this->user->codigo_empleado,
                'maquina_id' => $maquinaId
            ]);
            $this->redirectWithMessage('/timeControl/public/datos_trabajo_maquina', 'error', 'Error al guardar máquina. Intenta de nuevo.');
            return;
        }

        $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'success', 'Máquina seleccionada correctamente. Ahora ingresa los datos del trabajo.');
    }

    public function seleccionarData()
    {
        if (!$this->user) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Sesión no válida. Inicia sesión de nuevo.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Método no permitido. Usa POST.');
            return;
        }

        $jtWo = trim($_POST['jtWo'] ?? '');
        $item = trim($_POST['item'] ?? '');
        $po = trim($_POST['po'] ?? '');
        $cliente = trim($_POST['cliente'] ?? '');

        if (empty($jtWo) || empty($item) || empty($po) || empty($cliente)) {
            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Todos los campos (JT/WO, Item, PO, Cliente) son obligatorios.');
            return;
        }

        if (strlen($jtWo) > 255 || strlen($item) > 255 || strlen($po) > 255 || strlen($cliente) > 255) {
            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Los campos no pueden exceder 255 caracteres.');
            return;
        }

        if (!$this->user->maquina_id) {
            $this->redirectWithMessage('/timeControl/public/datos_trabajo_maquina', 'error', 'Debes seleccionar una máquina primero.');
            return;
        }

        $updated = $this->usuarioModel->updateDatosTrabajo(
            $this->user->codigo_empleado,
            $jtWo,
            $item,
            $po,
            $cliente
        );

        if (!$updated) {
            Logger::error("Error updating work data", [
                'codigo_empleado' => $this->user->codigo_empleado,
                'jtWo' => $jtWo
            ]);
            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Error al guardar datos. Verifica la conexión MySQL e intenta de nuevo.');
            return;
        }

        $this->usuarioModel->actualizarEstadoBoton($this->user->codigo_empleado, 'defaultButtonId');

        if (isset($_SESSION['status'])) unset($_SESSION['status']);
        if (isset($_SESSION['message'])) unset($_SESSION['message']);

        $this->redirectWithMessage('/timeControl/public/control', 'success', 'Datos del trabajo guardados correctamente. Ahora puedes registrar producción o eventos.');
    }

    private function redirectWithMessage($url, $status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: {$url}");
        exit();
    }
}