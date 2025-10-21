document.addEventListener("DOMContentLoaded", function () {
    // Cache DOM elements
    const elements = {
        timeElement: document.getElementById('current-time'),
        dateElement: document.getElementById('current-date'),
        revisionModal: document.getElementById('revisionModal'),
        validateModal: document.getElementById('validateModal'),
        submitRevisionBtn: document.getElementById('submitRevisionBtn'),
        submitValidationBtn: document.getElementById('submitValidation'),
        notaRevision: document.getElementById('notaRevision'),
        comentarioValidacion: document.getElementById('comentarioValidacion'),
        comentarioValidacionContainer: document.querySelector('label[for="comentarioValidacion"]')?.parentNode || document.createElement('div'),
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

    // URLs para QA
    const URLS = {
        getStatus: '/timeControl/public/getStatus',
        revisar: '/timeControl/public/revisar',
        solicitarCorreccion: '/timeControl/public/solicitarCorreccion',
        validarScrap: '/timeControl/public/validarScrap',
        validarProduccion: '/timeControl/public/validarProduccion',
        verificarEstadosRegistros: '/timeControl/public/verificarEstadosRegistros',
        verificarEstadoPendiente: '/timeControl/public/verificarEstadoPendiente'
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

    const fetchData = async (url, method = 'GET', data = null) => {
        try {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data ? JSON.stringify(data) : null
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
        ].filter(id => id);

        updatePendingCounter();
    };

    const checkRecordStatus = async (id) => {
        try {
            const response = await fetch(`${URLS.verificarEstadoPendiente}?id=${id}`);
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
        const row = document.querySelector(`[data-id="${id}"]`)?.closest('tr');
        if (row) row.classList.add('bg-gray-100');

        // Eliminar de pendientes
        registrosPendientes = registrosPendientes.filter(item => item !== id);
        updatePendingCounter();
    };

    const updatePendingCounter = () => {
        const pendingCounterElement = document.getElementById('pending-counter');
        if (pendingCounterElement) {
            pendingCounterElement.textContent = registrosPendientes.length;
        }
    };

    const verifyRecordsStatus = async () => {
        if (!Array.isArray(registrosPendientes) || registrosPendientes.length === 0) return;

        try {
            const idsParam = registrosPendientes.join(',');
            const response = await fetch(`${URLS.verificarEstadosRegistros}?ids=${idsParam}`);
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
        if (document.getElementById('reloadAlert')) return;

        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": false,
            "positionClass": "toast-top-full-width",
            "preventDuplicates": true,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "0",
            "extendedTimeOut": "0",
            "tapToDismiss": false,
        };

        toastr.info(
            'Todas las entregas han sido procesadas. <button id="btnRecargar" class="font-medium underline hover:text-yellow-800 transition-colors">Recargar página</button> para ver las nuevas entregas pendientes.',
            'Información',
            {
                onclick: function () {
                    document.getElementById('btnRecargar').click();
                }
            }
        );

        document.getElementById('btnRecargar').addEventListener('click', () => {
            window.location.reload();
        });

        document.querySelector('.toast-close-button').addEventListener('click', () => {
            toastr.clear();
        });
    };

    const handleModal = (modalElement, action = 'show') => {
        if (!modalElement) return;

        if (action === 'show') {
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            const modalContent = modalElement.querySelector('.bg-white');
            if (modalContent) {
                modalContent.classList.add('scale-100', 'opacity-100');
                modalContent.classList.remove('scale-95', 'opacity-0');
            }
        } else {
            const modalContent = modalElement.querySelector('.bg-white');
            if (modalContent) {
                modalContent.classList.add('scale-95', 'opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');

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
        const id = button.dataset.id;
        const isPending = await checkRecordStatus(id);

        if (!isPending) {
            toastr.warning('Este registro ya no está pendiente. Recargando...');
            setTimeout(() => window.location.reload(), 1500);
            return;
        }

        currentEntregaId = button.dataset.id;
        currentTipoEntrega = button.dataset.tipo;

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

        document.getElementById('validacionMaquina').textContent = button.dataset.maquina;
        document.getElementById('validacionItem').textContent = button.dataset.item;
        document.getElementById('validacionJtWo').textContent = button.dataset.jtwo;
        document.getElementById('validacionCantidad').textContent = button.dataset.cantidad + ' lb.';
        document.getElementById('validacionTipo').textContent = currentTipoEntrega === 'scrap' ? 'Scrap' : 'Producción';

        if (currentTipoEntrega === 'scrap') {
            elements.comentarioValidacionContainer.style.display = 'block';
            elements.validateModal.dataset.cantidad = button.dataset.cantidad;
        } else {
            elements.comentarioValidacionContainer.style.display = 'none';
        }

        elements.comentarioValidacion.value = '';
        handleModal(elements.validateModal, 'show');
    };

    // Inicialización y Event Listeners
    const init = async () => {
        const statusData = await fetchData(URLS.getStatus);
        if (statusData?.status && statusData?.message) {
            toastr[statusData.status === "success" ? 'success' : 'error'](statusData.message);
        }

        // Event Listeners para los botones de revisión
        document.querySelectorAll('.btn-review').forEach(btn => {
            btn.addEventListener('click', handleRevisionModal);
        });

        // Event Listeners para los botones de validación
        document.querySelectorAll('.btn-validate-production, .btn-validate-scrap').forEach(btn => {
            btn.addEventListener('click', handleValidationModal);
        });

        // CORREGIDO: Submit de solicitud de corrección - IGUAL QUE SUPERVISOR
        elements.submitRevisionBtn?.addEventListener('click', async () => {
            const isPending = await checkRecordStatus(currentEntregaId);
            if (!isPending) {
                toastr.warning('Este registro ya fue procesado');
                handleModal(elements.revisionModal, 'hide');
                return;
            }

            const motivo = elements.notaRevision.value.trim();
            if (!motivo) {
                toastr.warning('Por favor, ingrese un motivo para la corrección');
                return;
            }

            // Deshabilitar botón durante el proceso
            elements.submitRevisionBtn.disabled = true;
            const originalHTML = elements.submitRevisionBtn.innerHTML;
            elements.submitRevisionBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

            try {
                const formData = new FormData();
                formData.append('id', currentEntregaId);
                formData.append('tipo', currentTipoEntrega);
                formData.append('motivo', motivo);

                const response = await fetch(URLS.solicitarCorreccion, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Cerrar modal inmediatamente
                    handleModal(elements.revisionModal, 'hide');
                    
                    // Mostrar mensaje y recargar (NO ocultar fila)
                    toastr.success(data.message || 'Solicitud de corrección enviada correctamente', 'Éxito', {
                        timeOut: 1000,
                        onHidden: function() {
                            window.location.reload();
                        }
                    });
                } else {
                    toastr.error(data.message || 'Error al procesar la solicitud');
                    elements.submitRevisionBtn.disabled = false;
                    elements.submitRevisionBtn.innerHTML = originalHTML;
                }

            } catch (error) {
                console.error('Error en solicitud de corrección:', error);
                toastr.error('Error al enviar la solicitud de corrección');
                elements.submitRevisionBtn.disabled = false;
                elements.submitRevisionBtn.innerHTML = originalHTML;
            }
        });

        // CORREGIDO: Submit de validación - IGUAL QUE SUPERVISOR
        elements.submitValidationBtn?.addEventListener('click', async (event) => {
            event.preventDefault();
            
            const isPending = await checkRecordStatus(currentEntregaId);
            if (!isPending) {
                toastr.warning('Este registro ya fue procesado');
                handleModal(elements.validateModal, 'hide');
                return;
            }

            // Determinar la URL según el tipo
            const url = currentTipoEntrega === 'scrap' ? 
                URLS.validarScrap : 
                URLS.validarProduccion;

            // Deshabilitar botón durante el proceso
            elements.submitValidationBtn.disabled = true;
            const originalHTML = elements.submitValidationBtn.innerHTML;
            elements.submitValidationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando...';

            try {
                const formData = new FormData();
                formData.append('id', currentEntregaId);
                
                if (currentTipoEntrega === 'scrap') {
                    formData.append('cantidad', elements.validateModal.dataset.cantidad);
                }
                
                formData.append('comentario', elements.comentarioValidacion.value.trim());

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Cerrar modal inmediatamente
                    handleModal(elements.validateModal, 'hide');
                    
                    const mensaje = currentTipoEntrega === 'scrap' ? 'Scrap validado exitosamente' : 'Producción validada exitosamente';
                    
                    // Mostrar mensaje y recargar (NO ocultar fila)
                    toastr.success(data.message || mensaje, 'Éxito', {
                        timeOut: 1000,
                        onHidden: function() {
                            window.location.reload();
                        }
                    });
                } else {
                    toastr.error(data.message || 'Error al validar la entrega');
                    elements.submitValidationBtn.disabled = false;
                    elements.submitValidationBtn.innerHTML = originalHTML;
                }

            } catch (error) {
                console.error('Error en validación:', error);
                toastr.error('Error al validar la entrega');
                elements.submitValidationBtn.disabled = false;
                elements.submitValidationBtn.innerHTML = originalHTML;
            }
        });

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