document.addEventListener("DOMContentLoaded", function () {
    // Cache DOM elements
    const elements = {
        dateElement: document.getElementById('current-date'),
        timeElement: document.getElementById('current-time'),
        revisionModal: document.getElementById('revisionModal'),
        validateModal: document.getElementById('validateModal'),
        submitRevisionBtn: document.getElementById('submitRevisionBtn'),
        submitValidationBtn: document.getElementById('submitValidation'),
        notaRevision: document.getElementById('notaRevision'),
        comentarioValidacion: document.getElementById('comentarioValidacion'),
        validateModalLabel: document.getElementById('validateModalLabel')
    };

    // Constants
    const URLS = {
        getStatus: '/timeControl/public/getStatus',
        revisar: '/timeControl/public/revisar',
        validarScrap: '/timeControl/public/validarScrap',
        validarProduccion: '/timeControl/public/validarProduccion'
    };

    const TOAST_TYPES = {
        success: toastr.success,
        error: toastr.error,
        warning: toastr.warning,
        info: toastr.info
    };

    // Configure toastr
    toastr.options = {
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

    // Toast handling function
    window.showBackendToast = function (message, type = "success", redirectUrl = null) {
        const toastrFunction = TOAST_TYPES[type] || TOAST_TYPES.info;
        toastrFunction.call(toastr, message);

        if (redirectUrl) {
            window.pendingRedirect = redirectUrl;
        }
    };

    // Helper functions
    function updateDateTime() {
        const now = new Date();
        if (elements.dateElement) elements.dateElement.textContent = now.toLocaleDateString('es-ES');
        if (elements.timeElement) elements.timeElement.textContent = now.toLocaleTimeString('es-ES');
    }

    function showModal(modalElement) {
        if (!modalElement) return;
        const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modalInstance.show();
    }

    function hideModal(modalElement) {
        if (!modalElement) return;
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) modalInstance.hide();
    }

    function showFieldError(field, message) {
        if (!field) return;
        field.classList.add('is-invalid');
        
        // Remove existing error messages
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();
        
        // Create new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.innerText = message;
        field.parentNode.appendChild(errorDiv);
        
        setTimeout(() => {
            field.classList.remove('is-invalid');
            if (errorDiv.parentNode === field.parentNode) {
                field.parentNode.removeChild(errorDiv);
            }
        }, 3000);
    }

    function showValidationModal(entregaId, tipo, title, commentDisplayStyle, cantidad = 0) {
        if (elements.validateModalLabel) elements.validateModalLabel.textContent = title;

        if (elements.submitValidationBtn) {
            elements.submitValidationBtn.setAttribute('data-id', entregaId);
            elements.submitValidationBtn.setAttribute('data-tipo', tipo);

            if (cantidad > 0) {
                elements.submitValidationBtn.setAttribute('data-entrega', cantidad);
            }
        }

        // Show/hide comment field
        const comentarioContainer = document.querySelector('#validateModal textarea[data-id="data-comentario"]')?.closest('.mb-3');
        if (comentarioContainer) {
            comentarioContainer.style.display = commentDisplayStyle;
        }

        // Reset comment field
        if (elements.comentarioValidacion) elements.comentarioValidacion.value = '';

        // Show modal
        showModal(elements.validateModal);
    }

    async function fetchData(url, method = 'GET', formData = null) {
        try {
            const options = {
                method: method
            };
            
            if (formData) {
                options.body = formData;
            }
            
            const response = await fetch(url, options);
            
            if (response.redirected) {
                window.location.href = response.url;
                return { redirected: true };
            }
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error in request');
            }
            
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            return { success: false, message: error.message };
        }
    }

    // Initialize
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Fetch initial status
    fetchData(URLS.getStatus)
        .then(data => {
            if (data.status && data.message) {
                const toastrFunction = data.status === "success" ? TOAST_TYPES.success : TOAST_TYPES.error;
                toastrFunction(data.message, '', { timeOut: 2000 });
            }
        });

    // Event delegation for buttons
    document.addEventListener('click', function (event) {
        // Review button
        const reviewButton = event.target.closest('.btn-review');
        if (reviewButton) {
            const entregaId = reviewButton.getAttribute('data-id');
            const tipo = reviewButton.getAttribute('data-tipo');
            
            if (elements.submitRevisionBtn) {
                elements.submitRevisionBtn.setAttribute('data-id', entregaId);
                elements.submitRevisionBtn.setAttribute('data-tipo', tipo);
            }

            // Clear note field
            if (elements.notaRevision) elements.notaRevision.value = '';

            showModal(elements.revisionModal);
            return;
        }

        // Production validation button
        const validateProductionButton = event.target.closest('.btn-validate-production');
        if (validateProductionButton) {
            const entregaId = validateProductionButton.getAttribute('data-id');
            showValidationModal(entregaId, 'produccion', 'Validar Entrega de Producción', 'none');
            return;
        }

        // Scrap validation button
        const validateScrapButton = event.target.closest('.btn-validate-scrap');
        if (validateScrapButton) {
            const entregaId = validateScrapButton.getAttribute('data-id');
            const cantidad = validateScrapButton.getAttribute('data-entrega');
            showValidationModal(entregaId, 'scrap', 'Validar Entrega de Scrap', 'block', cantidad);
        }
    });

    // Handle revision submission
    if (elements.submitRevisionBtn) {
        elements.submitRevisionBtn.addEventListener('click', function () {
            const entregaId = this.getAttribute('data-id');
            const tipo = this.getAttribute('data-tipo');
            const nota = elements.notaRevision?.value || '';

            // Basic validation
            if (!nota.trim()) {
                showFieldError(elements.notaRevision, 'Por favor ingrese una nota para la corrección o revisión');
                return;
            }

            // Close modal
            hideModal(elements.revisionModal);

            // Prepare data
            const formData = new FormData();
            formData.append('id', entregaId);
            formData.append('tipo', tipo);
            formData.append('nota', nota);

            // Send request
            fetchData(URLS.revisar, 'POST', formData)
                .then(data => {
                    if (data && data.message) {
                        showBackendToast(data.message, data.success ? 'success' : 'error');
                    }

                    if (data.success) {
                        setTimeout(() => location.reload(), 1500);
                    }
                });
        });
    }

    // Handle validation submission
    if (elements.submitValidationBtn) {
        elements.submitValidationBtn.addEventListener('click', function () {
            const entregaId = this.getAttribute('data-id');
            const tipo = this.getAttribute('data-tipo');
            const cantidad = this.getAttribute('data-entrega') || '0';
            const comentario = elements.comentarioValidacion?.value || '';

            // Type-specific validations
            if (tipo === 'scrap') {
                const comentarioContainer = document.querySelector('#validateModal textarea[data-id="data-comentario"]')?.closest('.mb-3');
                if (comentarioContainer && 
                    comentarioContainer.style.display !== 'none' && 
                    !comentario.trim()) {
                    showFieldError(elements.comentarioValidacion, 'Por favor ingrese observaciones para el scrap');
                    return;
                }
            }

            // Close modal
            hideModal(elements.validateModal);

            // Prepare data
            const formData = new FormData();
            formData.append('id', entregaId);
            formData.append('tipo', tipo);
            formData.append('comentario', comentario);
            formData.append('cantidad', cantidad);

            // Determine URL by type
            const url = tipo === 'scrap' ? URLS.validarScrap : URLS.validarProduccion;

            // Send request
            fetchData(url, 'POST', formData)
                .then(data => {
                    if (data.redirected) return;

                    if (data && data.message) {
                        showBackendToast(data.message, data.success ? 'success' : 'error');
                        if (data.success) {
                            setTimeout(() => location.reload(), 1500);
                        }
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                });
        });
    }
});