document.addEventListener("DOMContentLoaded", function () {
    // Cache DOM elements
    const elements = {
        timeElement: document.getElementById('current-time'),
        revisionModal: document.getElementById('revisionModal'),
        validateModal: document.getElementById('validateModal'),
        submitRevisionBtn: document.getElementById('submitRevisionBtn'),
        submitValidationBtn: document.getElementById('submitValidation'),
        notaRevision: document.getElementById('notaRevision'),
        comentarioValidacion: document.getElementById('comentarioValidacion'),
        comentarioValidacionContainer: document.querySelector('label[for="comentarioValidacion"]').parentNode, // Referencia al div contenedor del comentario
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
    const verificationInterval = 5000; // 5 segundos

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

    // Funciones principales
    const updateDateTime = () => {
        const now = new Date();
        const dateOptions = { dateStyle: 'medium' };
        const timeOptions = { timeStyle: 'medium' };

        if (elements.dateElement) {
            elements.dateElement.textContent = now.toLocaleDateString('es-ES', dateOptions);
        }
        if (elements.timeElement) {
            elements.timeElement.textContent = now.toLocaleTimeString('es-ES', timeOptions);
        }
    };

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
        // Eliminar duplicados
        registrosPendientes = [...new Set(registrosPendientes)];
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
    };

    const verifyRecordsStatus = async () => {
        if (registrosPendientes.length === 0) return;

        try {
            const response = await fetch(`/timeControl/public/verificarEstadosRegistros?ids=${registrosPendientes.join(',')}`);
            const data = await response.json();

            if (data.success) {
                let changesDetected = false;

                Object.entries(data.estados).forEach(([id, estado]) => {
                    if (estado !== 'Pendiente') {
                        changesDetected = true;
                        updateUIForProcessedRecord(id);
                    }
                });

                if (changesDetected) {
                    toastr.info('Algunos registros han sido actualizados', 'Cambios detectados');
                    updatePendingCounter();

                    if (registrosPendientes.length === 0) {
                        showReloadMessage();
                    }
                }
            }
        } catch (error) {
            console.error('Error en verificación periódica:', error);
        }
    };

    const updatePendingCounter = () => {
        const counterElements = document.querySelectorAll('.contador-pendientes');
        counterElements.forEach(el => {
            el.textContent = registrosPendientes.length;
        });
    };

    const showReloadMessage = () => {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }

        const alertHTML = `
        <div class="fixed top-4 inset-x-0 flex justify-center z-50" id="reloadAlert">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded shadow-lg flex items-center justify-between max-w-3xl w-full mx-4">
                <div class="flex items-center">
                    <div class="text-yellow-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Todas las entregas han sido procesadas. 
                            <button id="btnRecargar" class="font-medium underline hover:text-yellow-800 transition-colors">
                                Recargar página
                            </button> 
                            para ver las nuevas entregas pendientes.
                        </p>
                    </div>
                </div>
                <button id="btnCerrarAlerta" class="text-yellow-600 hover:text-yellow-800 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('afterbegin', alertHTML);

        document.getElementById('btnRecargar').addEventListener('click', () => {
            window.location.reload();
        });

        document.getElementById('btnCerrarAlerta').addEventListener('click', () => {
            document.getElementById('reloadAlert').remove();
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
            setTimeout(() => {
                row.remove();
                updatePendingCounter(); // Actualizar contador después de remover
            }, 500);
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
        updateDateTime();
        setInterval(updateDateTime, 1000);

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
            const isPending = await checkRecordStatus(currentEntregaId);
            if (!isPending) {
                toastr.warning('Este registro ya fue procesado');
                handleModal(elements.validateModal, 'hide');
                return;
            }
            formData.append('id', currentEntregaId);
            formData.append('nota', elements.notaRevision.value);
            formData.append('tipo', currentTipoEntrega);

            const data = await fetchData(URLS.revisar, 'POST', formData);
            if (data.success) {
                toastr.success('Revisión enviada correctamente');
                handleModal(elements.revisionModal, 'hide');
                ocultarFila(currentEntregaId);

                registrosPendientes = registrosPendientes.filter(item => item !== currentEntregaId);
                updatePendingCounter();
            }
        });

        elements.submitValidationBtn?.addEventListener('click', async () => {
            const url = currentTipoEntrega === 'scrap' ? URLS.validarScrap : URLS.validarProduccion;
            const formData = new FormData();
            formData.append('id', currentEntregaId);

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

            const data = await fetchData(url, 'POST', formData);
            if (data.success) {
                toastr.success('Entrega validada correctamente');
                handleModal(elements.validateModal, 'hide');
                ocultarFila(currentEntregaId);

                registrosPendientes = registrosPendientes.filter(item => item !== currentEntregaId);
                updatePendingCounter();
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

        collectPendingIds();
        if (registrosPendientes.length > 0) {
            intervalId = setInterval(verifyRecordsStatus, verificationInterval);
            verifyRecordsStatus(); 
        }

        // Actualizar contador inicial
        updatePendingCounter();
    };

    init();
});