toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-bottom-right",
            timeOut: 5000,
            extendedTimeOut: 1000,
            tapToDismiss: false
        };

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = 0;
                setTimeout(() => {
                    el.style.opacity = 1;
                }, 100);
            });

            document.addEventListener('newDelivery', (e) => {
                toastr.info('Nueva entrega detectada', 'Hay nuevos registros disponibles', {
                    timeOut: 5000,
                    extendedTimeOut: 1000,
                    closeButton: true,
                    tapToDismiss: false,
                    onclick: () => window.location.reload()
                });
            });

            function filtrar() {
                let fecha = $('#filtroFecha').val();
                let item = $('#filtroItem').val().toLowerCase();
                let jtwo = $('#filtroJtWo').val().toLowerCase();
                let po = $('#filtroPO').val().toLowerCase();
                let cliente = $('#filtroCliente').val().toLowerCase();

                $('.entrega-row').each(function () {
                    let $row = $(this);
                    let match = true;
                    if (fecha && $row.data('fecha') !== fecha) match = false;
                    if (item && !$row.data('item').toLowerCase().includes(item)) match = false;
                    if (jtwo && !$row.data('jtwo').toLowerCase().includes(jtwo)) match = false;
                    if (po && !$row.data('po').toLowerCase().includes(po)) match = false;
                    if (cliente && !$row.data('cliente').toLowerCase().includes(cliente)) match = false;
                    $row.toggle(match);
                });

                $('.maquina-group').each(function() {
                    const $grupo = $(this);
                    const filasVisibles = $grupo.find('.entrega-row:visible').length;
                    $grupo.toggle(filasVisibles > 0);
                });

                actualizarContador();
            }

            function actualizarContador() {
                const entregasVisibles = $('.entrega-row:visible').length;
                $('#pending-counter').text(entregasVisibles);
            }

            $('#filtroFecha, #filtroItem, #filtroJtWo, #filtroPO, #filtroCliente').on('input change', filtrar);
            $('#btnLimpiarFiltros').on('click', function (e) {
                e.preventDefault();
                $('#filtroFecha, #filtroItem, #filtroJtWo, #filtroPO, #filtroCliente').val('');
                $('.entrega-row').show();
                $('.maquina-group').show();
                actualizarContador();
            });

            $('.btn-review').on('click', function() {
                const id = $(this).data('id');
                const tipo = $(this).data('tipo');
                const cantidad = $(this).data('cantidad');
                const maquina = $(this).data('maquina');
                const item = $(this).data('item');
                const jtwo = $(this).data('jtwo');
                const po = $(this).data('po');
                const cliente = $(this).data('cliente');

                $('#revisionModal').find('#revisionMaquina').text(maquina);
                $('#revisionModal').find('#revisionItem').text(item);
                $('#revisionModal').find('#revisionJtWo').text(jtwo);
                $('#revisionModal').find('#revisionCantidad').text(cantidad.toFixed(2) + ' lb.');
                $('#revisionModal').find('#revisionTipo').text(tipo.charAt(0).toUpperCase() + tipo.slice(1));
                $('#revisionModal').find('#notaRevision').val('');

                $('#revisionModal').removeClass('hidden').addClass('flex');
                setTimeout(() => {
                    const modalContent = $('#revisionModal').find('.modern-modal');
                    modalContent.removeClass('scale-95').addClass('scale-100');
                }, 10);

                $('#submitRevisionBtn').off('click').on('click', function() {
                    const nota = $('#notaRevision').val();
                    const formData = new FormData();
                    formData.append('entrega_id', id);
                    formData.append('tipo', tipo);
                    if (nota) formData.append('nota', nota);

                    const submitButton = $(this);
                    const originalContent = submitButton.html();
                    submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...');

                    fetch('/timeControl/public/revision', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message || 'Revisión enviada exitosamente');
                            $('#revisionModal').addClass('hidden').removeClass('flex');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(data.message || 'Error al enviar la revisión');
                            submitButton.prop('disabled', false).html(originalContent);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Error al procesar la solicitud');
                        submitButton.prop('disabled', false).html(originalContent);
                    });
                });
            });

            $('.btn-validate-production, .btn-validate-scrap').on('click', function() {
                const id = $(this).data('id');
                const tipo = $(this).data('tipo');
                const cantidad = $(this).data('cantidad');
                const maquina = $(this).data('maquina');
                const item = $(this).data('item');
                const jtwo = $(this).data('jtwo');
                const po = $(this).data('po');
                const cliente = $(this).data('cliente');

                $('#validateModal').find('#validacionMaquina').text(maquina);
                $('#validateModal').find('#validacionItem').text(item);
                $('#validateModal').find('#validacionJtWo').text(jtwo);
                $('#validateModal').find('#validacionCantidad').text(cantidad.toFixed(2) + ' lb.');
                $('#validateModal').find('#validacionTipo').text(tipo.charAt(0).toUpperCase() + tipo.slice(1));
                $('#validateModal').find('#comentarioValidacion').val('');

                $('#validateModal').removeClass('hidden').addClass('flex');
                setTimeout(() => {
                    const modalContent = $('#validateModal').find('.modern-modal');
                    modalContent.removeClass('scale-95').addClass('scale-100');
                }, 10);

                $('#submitValidation').off('click').on('click', function() {
                    const comentario = $('#comentarioValidacion').val();
                    const formData = new FormData();
                    formData.append('entrega_id', id);
                    formData.append('tipo', tipo);
                    if (comentario) formData.append('comentario', comentario);

                    const submitButton = $(this);
                    const originalContent = submitButton.html();
                    submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...');

                    fetch('/timeControl/public/guardarProduccion', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message || 'Validación exitosa');
                            $('#validateModal').addClass('hidden').removeClass('flex');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(data.message || 'Error al validar');
                            submitButton.prop('disabled', false).html(originalContent);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Error al procesar la solicitud');
                        submitButton.prop('disabled', false).html(originalContent);
                    });
                });
            });

            $('.modal-close').on('click', function() {
                const modal = $(this).closest('.fixed');
                modal.find('.modern-modal').removeClass('scale-100').addClass('scale-95');
                setTimeout(() => {
                    modal.addClass('hidden').removeClass('flex');
                    modal.find('textarea').val('');
                }, 200);
            });

            $('#revisionModal, #validateModal').on('click', function(e) {
                if (e.target === this) {
                    $(this).find('.modern-modal').removeClass('scale-100').addClass('scale-95');
                    setTimeout(() => {
                        $(this).addClass('hidden').removeClass('flex');
                        $(this).find('textarea').val('');
                    }, 200);
                }
            });
        });