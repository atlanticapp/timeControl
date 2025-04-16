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
        onHidden: function() {
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

    const handleRevisionModal = (event) => {
        const button = event.currentTarget;
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

    const handleValidationModal = (event) => {
        const button = event.currentTarget;
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
            formData.append('id', currentEntregaId);
            formData.append('nota', elements.notaRevision.value);
            formData.append('tipo', currentTipoEntrega);

            const data = await fetchData(URLS.revisar, 'POST', formData);
            if (data.success) {
                toastr.success('Revisión enviada correctamente');
                handleModal(elements.revisionModal, 'hide');
                ocultarFila(currentEntregaId);
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

    init();
});