public function index()
{
    if (!AuthHelper::isAuthenticated()) {
        $this->redirectWithMessage('/timeControl/public/login', 'error', 'Debes iniciar sesión.');
        return;
    }

    $user = AuthHelper::getCurrentUser();

    if (!$user || $user->tipo_usuario !== 'operador') {
        $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado.');
        return;
    }

    try {
        // Obtener el nombre del area
        $area = $this->userModel->getNameArea($user->area_id);
        
        // Obtener el nombre de la maquina
        $maquina = $this->userModel->getNameMaquina($user->maquina_id);
        
        // Obtener operaciones de preparacion y contratiempos
        $preparacion = $this->operacionModel->getPreparacion($user->maquina_id);
        $bad_copy = $this->operacionModel->getContratiempos($user->maquina_id);
        
        // Obtener el historial de entregas parciales
        $historial = $this->registroModel->getHistorialParcial(
            $user->codigo_empleado, 
            $user->maquina_id, 
            $user->item, 
            $user->jtWo
        );

        // **CAMBIO IMPORTANTE: Obtener correcciones por codigo de empleado**
        $correccionesModel = new \App\Models\CorreccionesOperador();
        $correcciones_pendientes = $correccionesModel->getCorreccionesPendientesPorOperador($user->codigo_empleado);
        
        // Log para debug
        Logger::info('Cargando vista de control para operador', [
            'codigo_empleado' => $user->codigo_empleado,
            'maquina_id' => $user->maquina_id,
            'correcciones_encontradas' => count($correcciones_pendientes)
        ]);
        
        // Verificar si hay correcciones pendientes
        $mostrar_correcciones = !empty($correcciones_pendientes);

        // Renderizar la vista con todos los datos incluyendo las correcciones
        $this->view('operador/control', [
            'area' => $area,
            'maquina' => $maquina,
            'data' => (array) $user,
            'preparacion' => $preparacion,
            'bad_copy' => $bad_copy,
            'historial' => $historial,
            'active_button_id' => $user->active_button_id ?? 'defaultButtonId',
            'correcciones_pendientes' => $correcciones_pendientes,
            'mostrar_correcciones' => $mostrar_correcciones
        ]);

    } catch (\Exception $e) {
        Logger::exception($e, [
            'controller' => 'ControlController',
            'method' => 'index',
            'user_id' => $user->codigo_empleado ?? 'unknown'
        ]);
        $this->redirectWithMessage('/timeControl/public/login', 'error', 'Error al cargar el control: ' . $e->getMessage());
    }
}