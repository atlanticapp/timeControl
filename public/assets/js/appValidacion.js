document.addEventListener("DOMContentLoaded", function () {
    // Cache DOM elements
    const elements = {
        timeElement: document.getElementById('current-time'),
        dateElement: document.getElementById('current-date'), // Añadido este elemento que faltaba
        revisionModal: document.getElementById('revisionModal'),
        validateModal: document.getElementById('validateModal'),
        submitRevisionBtn: document.getElementById('submitRevisionBtn'),
        submitValidationBtn: document.getElementById('submitValidation'),
        notaRevision: document.getElementById('notaRevision'),
        comentarioValidacion: document.getElementById('comentarioValidacion'),
        comentarioValidacionContainer: document.querySelector('label[for="comentarioValidacion"]')?.parentNode || document.createElement('div'), // Añadido fallback
        tabButtons: document.querySelectorAll('.tab-btn'),
        tabPanels: document.querySelectorAll('.tab-panel'),
        btnReview: document.querySelectorAll('.btn-review'),
        btnValidateProduction: document.querySelectorAll('.btn-validate-production'),
        btnValidateScrap: document.querySelectorAll('.btn-validate-scrap'),
        modalCloseButtons: document.querySelectorAll('.modal-close'),
        modalBackdrops: document.querySelectorAll('.modal-backdrop')
    };

    // Estado temporal
    let currentEntregaId = null;
    let currentTipoEntrega = null;
    let registrosPendientes = [];
    let intervalId = null;
    const verificationInterval = 5000;

    // URLs para las peticiones AJAX
    const URLS = {
        getStatus: '/timeControl/public/getStatus',
        revisar: '/timeControl/public/revisar',
        validarScrap: '/timeControl/public/validarScrap',
        validarProduccion: '/timeControl/public/validarProduccion'
    };

    // Configuración de Toastr
    const TOAST_CONFIG = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000,
        extendedTimeOut: 1000,
        preventDuplicates: true,
        onHidden: function () {
            if (window.pendingRedirect) {
                window.location.href = window.pendingRedirect;
                window.pendingRedirect = null;
            }
        }
    };

    // Inicializar toastr con la configuración
    toastr.options = TOAST_CONFIG;

    const handleApiResponse = async (response) => {
        if (response.redirected) {
            window.location.href = response.url;
            return { redirected: true };
        }

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Error en la solicitud');
        }
        return data;
    };

    const fetchData = async (url, method = 'GET', formData = null) => {
        try {
            const options = {
                method,
                headers: method === 'GET' ? {} : {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            };

            const response = await fetch(url, options);
            return await handleApiResponse(response);
        } catch (error) {
            console.error('Error en fetch:', error);
            toastr.error(error.message);
            return { success: false, message: error.message };
        }
    };

    const collectPendingIds = () => {
        registrosPendientes = [];

        registrosPendientes = [
            ...new Set([
                ...Array.from(elements.btnReview, btn => btn.dataset.id),
                ...Array.from(elements.btnValidateProduction, btn => btn.dataset.id),
                ...Array.from(elements.btnValidateScrap, btn => btn.dataset.id)
            ])
        ].filter(id => id); // Filtra IDs vacíos

        updatePendingCounter();
    };

    const checkRecordStatus = async (id) => {
        try {
            const response = await fetch(`/timeControl/public/verificarEstadoPendiente?id=${id}`);
            if (!response.ok) throw new Error('Error en la respuesta');
            const data = await response.json();
            return data.pendiente ?? false;
        } catch (error) {
            console.error('Error verificando estado:', error);
            toastr.error('Error al verificar estado del registro');
            return false;
        }
    };

    const updateUIForProcessedRecord = (id) => {
        // Desactivar botones
        document.querySelectorAll(`[data-id="${id}"]`).forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.title = 'Este registro ya no está pendiente';
        });

        // Marcar fila
        const row = document.querySelector(`[data-id="${id}"]`).closest('tr');
        if (row) row.classList.add('bg-gray-100');

        // Eliminar de pendientes
        registrosPendientes = registrosPendientes.filter(item => item !== id);
        updatePendingCounter();
    };

    const updatePendingCounter = () => {
        // Esta función faltaba implementarse
        const pendingCounterElement = document.getElementById('pending-counter');
        if (pendingCounterElement) {
            pendingCounterElement.textContent = registrosPendientes.length;
        }
    };

    const verifyRecordsStatus = async () => {
        if (!Array.isArray(registrosPendientes) || registrosPendientes.length === 0) return;

        try {
            const query = registrosPendientes.join(',');
            const response = await fetch(`/timeControl/public/verificarEstadosRegistros?ids=${encodeURIComponent(query)}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.estados) {
                let changesDetected = false;

                for (const [id, estado] of Object.entries(data.estados)) {
                    if (estado !== 'Pendiente') {
                        changesDetected = true;
                        updateUIForProcessedRecord(id);
                    }
                }

                if (changesDetected) {
                    toastr.info('Algunos registros han sido actualizados', 'Cambios detectados');
                    updatePendingCounter();

                    // Solo muestra el mensaje si aún no se ha mostrado
                    if (!document.getElementById('reloadAlert')) {
                        showReloadMessage();
                    }
                }
            }
        } catch (error) {
            console.error('Error al verificar estados de los registros:', error);
            toastr.error('Ocurrió un error al verificar los registros.', 'Error');
        }
    };


    const showReloadMessage = () => {
        // Evitar múltiples alertas
        if (document.getElementById('reloadAlert')) return;

        // Detener intervalo si existe
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }

        // Mostrar notificación con Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": false, // Desactivamos la barra de progreso
            "positionClass": "toast-top-full-width", // Toastr ocupará todo el ancho
            "preventDuplicates": true,
            "showDuration": "300", // Duración de la animación al mostrar
            "hideDuration": "1000", // Duración de la animación al ocultar
            "timeOut": "0", // No se oculta automáticamente, el usuario debe cerrar
            "extendedTimeOut": "0", // No se oculta automáticamente
            "tapToDismiss": false, // No se puede descartar con un click en el mensaje
        };

        // Crear el contenido del toast
        toastr.info(
            'Todas las entregas han sido procesadas. <button id="btnRecargar" class="font-medium underline hover:text-yellow-800 transition-colors">Recargar página</button> para ver las nuevas entregas pendientes.',
            'Información',
            {
                onclick: function () {
                    document.getElementById('btnRecargar').click(); // Si se hace click en el mensaje, recarga la página
                }
            }
        );

        // Escuchar el click del botón de recarga
        document.getElementById('btnRecargar').addEventListener('click', () => {
            window.location.reload();
        });

        // Escuchar el click en el botón de cerrar
        document.querySelector('.toast-close-button').addEventListener('click', () => {
            toastr.clear(); // Cerrar el toast manualmente
        });
    };


    // Funciones de UI
    const handleModal = (modalElement, action = 'show') => {
        if (!modalElement) return;

        if (action === 'show') {
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            // Añadir animación de entrada
            const modalContent = modalElement.querySelector('.bg-white');
            if (modalContent) {
                modalContent.classList.add('scale-100', 'opacity-100');
                modalContent.classList.remove('scale-95', 'opacity-0');
            }
        } else {
            // Añadir animación de salida
            const modalContent = modalElement.querySelector('.bg-white');
            if (modalContent) {
                modalContent.classList.add('scale-95', 'opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');

                // Esperar a que termine la animación
                setTimeout(() => {
                    modalElement.classList.add('hidden');
                    modalElement.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }, 200);
            } else {
                modalElement.classList.add('hidden');
                modalElement.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        }
    };

    const handleRevisionModal = async (event) => {
        const button = event.currentTarget;
        const id = button.dataset.id; // Añadida esta línea que faltaba
        const isPending = await checkRecordStatus(id);

        if (!isPending) {
            toastr.warning('Este registro ya no está pendiente. Recargando...');
            setTimeout(() => window.location.reload(), 1500);
            return;
        }
        currentEntregaId = button.dataset.id;
        currentTipoEntrega = button.dataset.tipo;

        // Actualizar campos del modal con los data attributes del botón
        document.getElementById('revisionMaquina').textContent = button.dataset.maquina;
        document.getElementById('revisionItem').textContent = button.dataset.item;
        document.getElementById('revisionJtWo').textContent = button.dataset.jtwo;
        document.getElementById('revisionCantidad').textContent = button.dataset.cantidad + ' lb.';
        document.getElementById('revisionTipo').textContent = button.dataset.tipo === 'scrap' ? 'Scrap' : 'Producción';

        elements.notaRevision.value = '';
        handleModal(elements.revisionModal, 'show');
    };

    const handleValidationModal = async (event) => {
        const button = event.currentTarget;
        const id = button.dataset.id;
        const isPending = await checkRecordStatus(id);
        if (!isPending) {
            toastr.warning('Este registro ya no está pendiente. Recargando...');
            setTimeout(() => window.location.reload(), 2000);
            return;
        }

        currentEntregaId = button.dataset.id;
        currentTipoEntrega = button.dataset.tipo;

        // Actualizar campos del modal con los data attributes del botón
        document.getElementById('validacionMaquina').textContent = button.dataset.maquina;
        document.getElementById('validacionItem').textContent = button.dataset.item;
        document.getElementById('validacionJtWo').textContent = button.dataset.jtwo;
        document.getElementById('validacionCantidad').textContent = button.dataset.cantidad + ' lb.';
        document.getElementById('validacionTipo').textContent = currentTipoEntrega === 'scrap' ? 'Scrap' : 'Producción';

        // Mostrar u ocultar el campo de comentario según el tipo
        if (currentTipoEntrega === 'scrap') {
            // Mostrar el campo de comentario para scrap
            elements.comentarioValidacionContainer.style.display = 'block';
            elements.validateModal.dataset.cantidad = button.dataset.cantidad;
        } else {
            // Ocultar el campo de comentario para producción
            elements.comentarioValidacionContainer.style.display = 'none';
        }

        // Reiniciar el valor del comentario
        elements.comentarioValidacion.value = '';

        handleModal(elements.validateModal, 'show');
    };

    const ocultarFila = (id) => {
        const row = document.querySelector(`[data-id="${id}"]`)?.closest('tr');
        if (row) {
            row.style.transition = 'opacity 0.5s ease-out';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 500);
        }
    };

    // Event Handlers
    const handleTabClick = (event) => {
        const button = event.target.closest('.tab-btn');
        if (!button) return;

        elements.tabButtons.forEach(btn => {
            btn.classList.remove('active', 'bg-blue-50', 'text-blue-700');
        });
        button.classList.add('active', 'bg-blue-50', 'text-blue-700');

        elements.tabPanels.forEach(panel => {
            panel.classList.add('hidden');
        });
        document.getElementById(button.dataset.target)?.classList.remove('hidden');
    };

    // Inicialización y Event Listeners
    const init = async () => {
        // Verificar estado inicial
        const statusData = await fetchData(URLS.getStatus);
        if (statusData?.status && statusData?.message) {
            toastr[statusData.status === "success" ? 'success' : 'error'](statusData.message);
        }

        // Event Listeners
        elements.tabButtons.forEach(button => {
            button.addEventListener('click', handleTabClick);
        });

        // Event Listeners para los botones de revisión
        document.querySelectorAll('.btn-review').forEach(btn => {
            btn.addEventListener('click', handleRevisionModal);
        });

        // Event Listeners para los botones de validación
        document.querySelectorAll('.btn-validate-production, .btn-validate-scrap').forEach(btn => {
            btn.addEventListener('click', handleValidationModal);
        });

        elements.submitRevisionBtn?.addEventListener('click', async () => {
            const formData = new FormData();
            formData.append('id', currentEntregaId);
            formData.append('nota', elements.notaRevision.value);
            formData.append('tipo', currentTipoEntrega);

            const isPending = await checkRecordStatus(currentEntregaId);
            if (!isPending) {
                toastr.warning('Este registro ya fue procesado');
                handleModal(elements.revisionModal, 'hide');
                return;
            }

            const data = await fetchData(URLS.revisar, 'POST', formData);
            if (data.success) {
                handleModal(elements.revisionModal, 'hide');
                ocultarFila(currentEntregaId);
                registrosPendientes = registrosPendientes.filter(item => item !== currentEntregaId);
                updatePendingCounter(); // Añadida esta línea
            }
        });

        elements.submitValidationBtn?.addEventListener('click', async () => {
            const url = currentTipoEntrega === 'scrap' ? URLS.validarScrap : URLS.validarProduccion;
            const formData = new FormData();
            formData.append('id', currentEntregaId);
            const isPending = await checkRecordStatus(currentEntregaId);

            // Solo añadir comentario si es scrap (cuando el campo está visible)
            if (currentTipoEntrega === 'scrap') {
                formData.append('comentario', elements.comentarioValidacion.value);

                // Solo para scrap se envía la cantidad
                const cantidad = elements.validateModal.dataset.cantidad;
                formData.append('cantidad', cantidad);
            } else {
                // Para producción enviamos comentario vacío
                formData.append('comentario', '');
            }

            if (!isPending) {
                toastr.warning('Este registro ya fue procesado');
                handleModal(elements.validateModal, 'hide');
                return;
            }

            const data = await fetchData(url, 'POST', formData);
            if (data.success) {
                toastr.success('Entrega validada correctamente');
                handleModal(elements.validateModal, 'hide');
                ocultarFila(currentEntregaId);
                registrosPendientes = registrosPendientes.filter(item => item !== currentEntregaId);
                updatePendingCounter(); // Añadida esta línea
            }
        });

        // Manejar entregaId almacenado
        const entregaIdToHide = sessionStorage.getItem('ocultarEntregaId');
        if (entregaIdToHide) {
            ocultarFila(entregaIdToHide);
            sessionStorage.removeItem('ocultarEntregaId');
        }

        // Agregar listeners para cerrar modales
        elements.modalCloseButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.modal, #revisionModal, #validateModal');
                handleModal(modal, 'hide');
            });
        });

        // Cerrar modal al hacer clic fuera
        [elements.revisionModal, elements.validateModal].forEach(modal => {
            if (!modal) return;

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    handleModal(modal, 'hide');
                }
            });
        });
    };

    collectPendingIds();
    updatePendingCounter();
    if (registrosPendientes.length > 0) {
        intervalId = setInterval(verifyRecordsStatus, verificationInterval);
        verifyRecordsStatus();
    }

    init();
});