<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Usuario;
use App\Models\CorreccionesOperador;
use App\Helpers\Logger;

class DataController extends Controller
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

    public function index()
    {
        if (!$this->user) {
            Logger::error('No user found in DataController::index', []);
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

        Logger::info('Fetching corrections in DataController::index', [
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

    /**
     * Método para manejar Espera de Trabajo
     */
    public function esperaTrabajo()
    {
        if (!$this->user) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Sesión no válida. Inicia sesión de nuevo.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Método no permitido. Usa POST.');
            return;
        }

        // Verificar que el operador tiene una máquina asignada
        if (!$this->user->maquina_id || $this->user->maquina_id <= 0) {
            Logger::warning('Intento de espera sin máquina asignada', [
                'codigo_empleado' => $this->user->codigo_empleado
            ]);
            $this->redirectWithMessage('/timeControl/public/datos_trabajo_maquina', 'error', 'Debes seleccionar una máquina antes de registrar espera de trabajo.');
            return;
        }

        $tipoBoton = $_POST['tipo_boton'] ?? '';

        if ($tipoBoton !== 'Espera_trabajo') {
            Logger::error('Tipo de botón inválido para espera', [
                'codigo_empleado' => $this->user->codigo_empleado,
                'tipo_boton' => $tipoBoton
            ]);
            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Tipo de registro inválido.');
            return;
        }

        try {
            // Obtener la conexión a la base de datos desde el modelo
            $db = $this->usuarioModel->getDb();

            // Preparar los datos para insertar
            $codigo_empleado = (int)$this->user->codigo_empleado;
            $area_id = (int)$this->user->area_id;
            $maquina = (int)$this->user->maquina_id;
            $item = $this->user->item ?? 'N/A';
            $jtWo = $this->user->jtWo ?? 'N/A';
            $po = $this->user->po ?? 'N/A';
            $cliente = $this->user->cliente ?? 'N/A';
            $tipo_boton = 'Espera_trabajo';
            $descripcion = 'Espera de trabajo';
            $comentario = 'Operador en espera de trabajo';
            $cantidad_scrapt = 0;
            $cantidad_produccion = 0;
            $estado_validacion = 'Validado';

            // Insertar el registro de espera
            $sql = "INSERT INTO registro 
                    (tipo_boton, codigo_empleado, area_id, maquina, item, jtWo, po, cliente, 
                     descripcion, cantidad_scrapt, cantidad_produccion, comentario, estado_validacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            
            if (!$stmt) {
                throw new \Exception('Error al preparar la consulta: ' . $db->error);
            }

            $stmt->bind_param(
                'siiiissssddss',
                $tipo_boton,
                $codigo_empleado,
                $area_id,
                $maquina,
                $item,
                $jtWo,
                $po,
                $cliente,
                $descripcion,
                $cantidad_scrapt,
                $cantidad_produccion,
                $comentario,
                $estado_validacion
            );

            if (!$stmt->execute()) {
                throw new \Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $registroId = $stmt->insert_id;
            $stmt->close();

            // Actualizar el estado del botón activo
            $this->usuarioModel->actualizarEstadoBoton($this->user->codigo_empleado, 'Espera_trabajo');

            Logger::info('Espera de trabajo registrada exitosamente', [
                'codigo_empleado' => $this->user->codigo_empleado,
                'registro_id' => $registroId,
                'maquina_id' => $this->user->maquina_id
            ]);

            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'success', 'Espera de trabajo registrada correctamente.');

        } catch (\Exception $e) {
            Logger::error('Error al registrar espera de trabajo', [
                'codigo_empleado' => $this->user->codigo_empleado,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Error al registrar espera de trabajo. Intenta de nuevo.');
        }
    }

    private function redirectWithMessage($url, $status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: {$url}");
        exit();
    }
}