document.addEventListener("DOMContentLoaded", function() {
        console.log('DOM Loaded - Inicializando correcciones');
        
        // Mostrar mensajes de estado
        fetch('/timeControl/public/getStatus')
            .then(response => response.json())
            .then(data => {
                if (data.status && data.message) {
                    const toastrFunction = data.status === "success" ? toastr.success : toastr.error;
                    toastrFunction(data.message, '', {
                        timeOut: 3000
                    });
                }
            })
            .catch(error => console.error('Error al obtener el estado:', error));

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


        // Validación moderna para Final de Producción
        function validateFinalProduction() {
            const prodInput = document.getElementById("finalProductionValue").value.trim();
            const scrapInput = document.getElementById("finalScraptAmount").value.trim();

            const prod = prodInput !== '' ? parseFloat(prodInput).toFixed(2) : '0.00';
            const scrap = scrapInput !== '' ? parseFloat(scrapInput).toFixed(2) : '0.00';

            Swal.fire({
                title: 'Confirmación de Final de Producción',
                html: `
                    <p><strong>Cantidad producida:</strong> ${prod} lb</p>
                    <p><strong>Cantidad de scrap:</strong> ${scrap} lb</p>
                    <p style='color: red;'>¿Está seguro de que desea ingresar el Final de producción? Se cerrará la sesión...</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#f44336',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("finForm").submit();
                }
            });

            return false;
        }

        // Validación moderna para Entrega Parcial
        function validateParcial() {
            const prodInput = document.getElementById("parcialProductionValue").value.trim();
            const scrapInput = document.getElementById("parcialScraptAmount").value.trim();

            if (prodInput === '' && scrapInput === '') {
                toastr.warning("Debes ingresar al menos un valor en Producción o Scrap.");
                return false;
            }

            const prod = prodInput !== '' ? parseFloat(prodInput).toFixed(2) : '0.00';
            const scrap = scrapInput !== '' ? parseFloat(scrapInput).toFixed(2) : '0.00';

            Swal.fire({
                title: 'Confirmación de Parcial',
                html: `
                    <p><strong>Cantidad producida:</strong> ${prod} lb</p>
                    <p><strong>Cantidad de scrap:</strong> ${scrap} lb</p>
                    <p style='color: red;'>¿Está seguro de que desea ingresar el Parcial?</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#f44336',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("parcialForm").submit();
                }
            });

            return false;
        }

        // Validación simple de contratiempos
        function validateBadCopyForm() {
            const val = document.getElementById("badCopy").value;
            if (val === "") {
                toastr.warning("Por favor, seleccione una opción válida para Contratiempos.");
                return false;
            }
            return confirm('¿Está seguro de que desea realizar "Contratiempos"?');
        }

        // Toggle visibilidad
        function toggleFinalProductionInput() {
            const el = document.getElementById("finalProductionInput");
            el.style.display = (el.style.display === "none") ? "block" : "none";
        }

        function toggleParcialInput() {
            const el = document.getElementById("parcialInput");
            el.style.display = (el.style.display === "none" || el.style.display === "") ? "block" : "none";
        }

        // Confirmaciones generales
        function confirmMakeReady() {
            return confirm('¿Está seguro de que desea realizar "Preparación"?');
        }

        function confirmGoodCopy() {
            return confirm('¿Está seguro de que desea realizar "Producción"?');
        }

        function numberWithCommas(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }