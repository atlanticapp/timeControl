 document.addEventListener("DOMContentLoaded", function() {
            fetch('/timeControl/public/getStatus') // Llama al endpoint de PHP
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.message) {
                        const toastrFunction = data.status === "success" ? toastr.success : toastr.error;

                        toastrFunction(data.message, '', {
                            timeOut: 2000
                        });

                        setTimeout(() => {
                            window.location.href = "/timeControl/public/datos_trabajo_maquina"; // Limpia la URL
                        }, 2000);
                    }
                });

            // Manejo de formularios de corrección
            const forms = document.querySelectorAll('.correction-form');
            console.log('Formularios de corrección encontrados:', forms.length);
            
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            let currentForm = null;

            forms.forEach((form, index) => {
                console.log(`Configurando form ${index}:`, form);
                const confirmBtn = form.querySelector('.confirm-correction');

                if (!confirmBtn) {
                    console.error('No se encontró botón de confirmación en form', index);
                    return;
                }

                confirmBtn.addEventListener('click', function(e) {
                    console.log('Click en botón confirmar corrección');
                    e.preventDefault(); // Prevenir submit inmediato
                    
                    const cantidad = form.querySelector('.cantidad-input').value;
                    const comentario = form.querySelector('.comentario-input').value;
                    
                    console.log('Datos del formulario:', { cantidad, comentario });

                    if (!cantidad || parseFloat(cantidad) < 0) {
                        toastr.error('Debe ingresar una cantidad válida mayor o igual a 0');
                        return;
                    }

                    // Actualizar modal de confirmación
                    document.getElementById('confirmQuantity').textContent = cantidad;
                    document.getElementById('confirmComment').textContent = comentario || '(Sin comentario)';

                    currentForm = form;
                    confirmationModal.show();
                });
            });

            // Botón de confirmación final
            const submitBtn = document.getElementById('submitCorrection');
            if (submitBtn) {
                submitBtn.onclick = function() {
                    console.log('Confirmando envío de corrección');
                    if (currentForm) {
                        toastr.info('Procesando corrección...');
                        console.log('Enviando formulario:', currentForm);
                        currentForm.submit();
                    } else {
                        console.error('No hay formulario seleccionado');
                        toastr.error('Error: No se pudo identificar el formulario');
                    }
                };
            } else {
                console.error('No se encontró el botón submitCorrection');
            }
        });

        function confirmLogout() {
            if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
                window.location.href = "/timeControl/public/logout";
            }
        }