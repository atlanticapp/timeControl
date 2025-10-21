toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-bottom-right",
            timeOut: 5000
        };

        document.addEventListener("DOMContentLoaded", function() {
            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    if (data && data.status && data.message) {
                        toastr[data.status === "success" ? 'success' : 'error'](data.message);
                    }
                })
                .catch(error => console.error('Error:', error));

            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = 0;
                setTimeout(() => {
                    el.style.opacity = 1;
                }, 100);
            });
        });

        function openValidateModal(id, maquina, item, jtWo, cantidad, tipo) {
            document.getElementById('validateEntregaId').value = id;
            document.getElementById('validateTipo').value = tipo;
            document.getElementById('validacionMaquina').textContent = maquina;
            document.getElementById('validacionItem').textContent = item;
            document.getElementById('validacionJtWo').textContent = jtWo;
            document.getElementById('validacionCantidad').textContent = parseFloat(cantidad).toFixed(2);
            document.getElementById('validacionTipo').textContent = tipo === 'scrap' ? 'Scrap' : 'Producción';

            const modal = document.getElementById('validateModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                const modalContent = modal.querySelector('.modern-modal');
                if (modalContent) {
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                }
            }, 10);
        }

        function openRetainModal(id, maquina, item, jtWo, cantidad, tipo) {
            document.getElementById('retainEntregaId').value = id;
            document.getElementById('retainCantidadInput').value = cantidad;
            document.getElementById('retainTipo').value = tipo;
            document.getElementById('retencionMaquina').textContent = maquina;
            document.getElementById('retencionItem').textContent = item;
            document.getElementById('retencionJtWo').textContent = jtWo;
            document.getElementById('retencionCantidad').textContent = parseFloat(cantidad).toFixed(2);
            document.getElementById('retencionTipo').textContent = tipo === 'scrap' ? 'Scrap' : 'Producción';

            const modal = document.getElementById('retainModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                const modalContent = modal.querySelector('.modern-modal');
                if (modalContent) {
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                }
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const modalContent = modal.querySelector('.modern-modal');
            
            if (modalContent) {
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
            }

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');

                if (modalId === 'validateModal') {
                    document.getElementById('comentarioValidacion').value = '';
                } else if (modalId === 'retainModal') {
                    document.getElementById('retainMotivo').value = '';
                }
            }, 200);
        }

        function submitValidation() {
            const entregaId = document.getElementById('validateEntregaId').value;
            const comentario = document.getElementById('comentarioValidacion').value.trim();
            const tipo = document.getElementById('validateTipo').value;

            if (!entregaId) {
                toastr.error('No se ha seleccionado una entrega');
                return;
            }

            const submitButton = document.getElementById('btnSubmitValidate');
            const originalContent = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

            const formData = new FormData();
            formData.append('entrega_id', entregaId);
            formData.append('tipo', tipo);
            if (comentario) {
                formData.append('comentario', comentario);
            }

            fetch('/timeControl/public/guardarProduccion', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'Validación exitosa');
                    closeModal('validateModal');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    toastr.error(data.message || 'Error al validar');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Error al procesar la solicitud');
                submitButton.disabled = false;
                submitButton.innerHTML = originalContent;
            });
        }

        function submitRetention() {
            const entregaId = document.getElementById('retainEntregaId').value;
            const cantidad = document.getElementById('retainCantidadInput').value;
            const motivo = document.getElementById('retainMotivo').value;
            const tipo = document.getElementById('retainTipo').value;

            if (!entregaId || !motivo) {
                toastr.error('Todos los campos son obligatorios');
                return;
            }

            if (!cantidad || parseFloat(cantidad) <= 0) {
                toastr.error('La cantidad debe ser mayor que 0');
                return;
            }

            const submitButton = document.getElementById('btnSubmitRetain');
            const originalContent = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

            const formData = new FormData();
            formData.append('entrega_id', parseInt(entregaId));
            formData.append('cantidad', parseFloat(cantidad));
            formData.append('motivo', motivo);
            formData.append('tipo', tipo);

            fetch('/timeControl/public/accion/retener', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'Retención exitosa');
                    closeModal('retainModal');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    toastr.error(data.message || 'Error al retener');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Error al procesar la solicitud');
                submitButton.disabled = false;
                submitButton.innerHTML = originalContent;
            });
        }

        // Sistema de filtros
        $(document).ready(function() {
            function filtrarEntregas() {
                const filtros = {
                    fecha: $('#filtroFecha').val(),
                    item: $('#filtroItem').val().toLowerCase().trim(),
                    jtwo: $('#filtroJtWo').val().toLowerCase().trim(),
                    po: $('#filtroPO').val().toLowerCase().trim(),
                    cliente: $('#filtroCliente').val().toLowerCase().trim(),
                    tipo: $('#filtroTipo').val()
                };

                $('.entrega-row').each(function() {
                    const $row = $(this);
                    const datos = {
                        fecha: $row.data('fecha'),
                        item: ($row.data('item') || '').toLowerCase(),
                        jtwo: ($row.data('jtwo') || '').toLowerCase(),
                        po: ($row.data('po') || '').toLowerCase(),
                        cliente: ($row.data('cliente') || '').toLowerCase(),
                        tipo: $row.data('tipo') || ''
                    };

                    let mostrar = true;

                    if (filtros.fecha && datos.fecha !== filtros.fecha) mostrar = false;
                    if (filtros.item && !datos.item.includes(filtros.item)) mostrar = false;
                    if (filtros.jtwo && !datos.jtwo.includes(filtros.jtwo)) mostrar = false;
                    if (filtros.po && !datos.po.includes(filtros.po)) mostrar = false;
                    if (filtros.cliente && !datos.cliente.includes(filtros.cliente)) mostrar = false;
                    if (filtros.tipo && datos.tipo !== filtros.tipo) mostrar = false;

                    $row.toggle(mostrar);
                });

                $('.maquina-group').each(function() {
                    const $grupo = $(this);
                    const filasVisibles = $grupo.find('.entrega-row:visible').length;
                    $grupo.toggle(filasVisibles > 0);
                });

                actualizarContador();
            }

            $('#filtroFecha, #filtroItem, #filtroJtWo, #filtroPO, #filtroCliente, #filtroTipo').on('input keyup change', function() {
                filtrarEntregas();
            });

            $('#btnLimpiarFiltros').on('click', function(e) {
                e.preventDefault();
                $('#filtroFecha, #filtroItem, #filtroJtWo, #filtroPO, #filtroCliente, #filtroTipo').val('');
                $('.entrega-row').show();
                $('.maquina-group').show();
                actualizarContador();
            });

            function actualizarContador() {
                const entregasVisibles = $('.entrega-row:visible').length;
                $('.total-count').text(entregasVisibles);
            }

            actualizarContador();
        });

        // Cerrar modal al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (e.target.id === 'validateModal') {
                closeModal('validateModal');
            }
            if (e.target.id === 'retainModal') {
                closeModal('retainModal');
            }
        });