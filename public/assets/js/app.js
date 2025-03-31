// Required JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar bibliotecas y configuraciones
    initializeDateTime();
    initializeValidationHandlers();
    checkServerStatus();
    
    // Función para actualizar fecha y hora
    function initializeDateTime() {
        function updateDateTime() {
            const now = new Date();
            const dateEl = document.getElementById('current-date');
            const timeEl = document.getElementById('current-time');
            
            if (dateEl) dateEl.textContent = now.toLocaleDateString('es-ES');
            if (timeEl) timeEl.textContent = now.toLocaleTimeString('es-ES');
        }
        
        updateDateTime();
        setInterval(updateDateTime, 1000);
    }
    
    // Funciones para manejar validaciones
    function initializeValidationHandlers() {
        // Función para mostrar modal de validación
        function showValidationModal(entregaId, tipo, title, showComments) {
            const modalEl = document.getElementById('validateModal');
            if (!modalEl) return;
            
            const modalLabel = document.getElementById('validateModalLabel');
            if (modalLabel) modalLabel.textContent = title;
            
            const submitBtn = document.getElementById('submitValidation');
            if (submitBtn) {
                submitBtn.setAttribute('data-id', entregaId);
                submitBtn.setAttribute('data-tipo', tipo);
            }
            
            const comentarioContainer = document.querySelector('#validateModal textarea[data-id="data-comentario"]')?.closest('.mb-3');
            if (comentarioContainer) {
                comentarioContainer.style.display = showComments ? 'block' : 'none';
            }
            
            const validateModal = new bootstrap.Modal(modalEl);
            validateModal.show();
        }
        
        // Mostrar toast con mensajes de estado
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('toastMessage');
            if (!toastEl) return;
            
            const toastBody = document.getElementById('toastBody');
            if (!toastBody) return;
            
            // Limpiar clases anteriores
            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'text-white');
            
            // Añadir clase según el tipo
            const classMap = {
                'success': ['bg-success', 'text-white'],
                'danger': ['bg-danger', 'text-white'],
                'warning': ['bg-warning'],
                'info': ['bg-info', 'text-white']
            };
            
            if (classMap[type]) {
                classMap[type].forEach(cls => toastEl.classList.add(cls));
            }
            
            // Establecer mensaje y mostrar toast
            toastBody.innerHTML = message;
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
            toast.show();
        }
        
        // Delegación de eventos para botones de validación
        document.addEventListener('click', function(event) {
            const validateProductionBtn = event.target.closest('.btn-validate-production');
            if (validateProductionBtn) {
                const entregaId = validateProductionBtn.getAttribute('data-id');
                showValidationModal(entregaId, 'produccion', 'Validar Entrega de Producción', false);
                return;
            }
            
            const validateScrapBtn = event.target.closest('.btn-validate-scrap');
            if (validateScrapBtn) {
                const entregaId = validateScrapBtn.getAttribute('data-id');
                showValidationModal(entregaId, 'scrap', 'Validar Entrega de Scrap', true);
            }
        });
        
        // Manejar envío de formulario de validación
        const submitValidationBtn = document.getElementById('submitValidation');
        if (submitValidationBtn) {
            submitValidationBtn.addEventListener('click', function() {
                const entregaId = this.getAttribute('data-id');
                const tipo = this.getAttribute('data-tipo');
                const comentario = document.querySelector('#validateModal textarea[data-id="data-comentario"]')?.value || '';
                
                // Cerrar modal
                const modalEl = document.getElementById('validateModal');
                const modalInstance = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
                if (modalInstance) modalInstance.hide();
                
                // Mostrar toast de carga
                showToast(`Validando entrega de ${tipo}...`, 'info');
                
                // Enviar datos al servidor
                const formData = new FormData();
                formData.append('id', entregaId);
                formData.append('tipo', tipo);
                formData.append('comentario', comentario);
                
                fetch('/timeControl/public/validar', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Redirigir según la URL recibida
                    window.location.href = response.url;
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Hubo un problema con la solicitud', 'danger');
                });
            });
        }
    }
    
    // Función para verificar el estado del servidor
    function checkServerStatus() {
        // Verificar que toastr esté disponible
        if (typeof toastr === 'undefined') return;
        
        fetch('/timeControl/public/getStatus')
            .then(response => response.json())
            .then(data => {
                if (data.status && data.message) {
                    const toastrFn = data.status === "success" ? toastr.success : toastr.error;
                    toastrFn(data.message, '', {
                        timeOut: 2000
                    });
                }
            })
            .catch(error => {
                console.error('Error al obtener el estado:', error);
            });
    }
});