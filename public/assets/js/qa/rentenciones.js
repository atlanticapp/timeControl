   class GestionRetenciones {
            constructor() {
                this.retencionActual = null;
                this.cantidadTotal = 0;
                this.cantidadDisponible = 0;
                this.inicializar();
            }

            inicializar() {
                this.configurarToastr();
                this.configurarEventListeners();
                this.cargarEstadoInicial();
            }

            configurarToastr() {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-bottom-right',
                    timeOut: 3000,
                    preventDuplicates: true,
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut'
                };
            }

            configurarEventListeners() {
                document.querySelectorAll('[data-retencion]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const retencion = JSON.parse(e.currentTarget.dataset.retencion);
                        this.abrirModal(retencion);
                    });
                });

                document.getElementById('btnCerrarModal').addEventListener('click', () => this.cerrarModal());
                document.getElementById('btnCancelar').addEventListener('click', () => this.cerrarModal());
                document.getElementById('modalGestionar').addEventListener('click', (e) => {
                    if (e.target === e.currentTarget) this.cerrarModal();
                });

                document.querySelectorAll('.destino-cantidad').forEach(input => {
                    input.addEventListener('input', () => this.actualizarBalances());
                });

                document.querySelectorAll('.btn-asignar-todo').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        this.asignarTodo(e.currentTarget.dataset.destino);
                    });
                });

                document.getElementById('formGestionar').addEventListener('submit', (e) => this.guardarAsignaciones(e));
            }

            cargarEstadoInicial() {
                fetch('/timeControl/public/getStatus')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status && data.message) {
                            const toastrFunction = data.status === "success" ? toastr.success : toastr.error;
                            toastrFunction(data.message, '', {
                                timeOut: 2000
                            });
                        }
                    })
                    .catch(error => console.error('Error al obtener el estado:', error));
            }

            abrirModal(retencion) {
                this.retencionActual = retencion;
                this.cantidadTotal = parseFloat(retencion.cantidad_total);
                this.cantidadDisponible = parseFloat(retencion.cantidad_disponible);

                document.getElementById('retencionId').value = retencion.id;
                document.getElementById('modalItem').textContent = retencion.item;
                document.getElementById('modalJtWo').textContent = retencion.jtWo;
                document.getElementById('modalCantidadTotal').textContent = this.cantidadTotal.toFixed(2);
                document.getElementById('modalCantidadDisponible').textContent = this.cantidadDisponible.toFixed(2);

                document.getElementById('cantidad_produccion_final').value = '0';
                document.getElementById('cantidad_retrabajo').value = '0';
                document.getElementById('cantidad_destruccion').value = '0';
                document.querySelectorAll('textarea').forEach(textarea => textarea.value = '');

                this.actualizarBalances();
                document.getElementById('errorMessage').classList.add('hidden');

                const modal = document.getElementById('modalGestionar');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modal.querySelector('.modern-modal').classList.remove('scale-95');
                    modal.querySelector('.modern-modal').classList.add('scale-100');
                }, 10);
            }

            cerrarModal() {
                const modal = document.getElementById('modalGestionar');
                modal.querySelector('.modern-modal').classList.remove('scale-100');
                modal.querySelector('.modern-modal').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.getElementById('formGestionar').reset();
                }, 200);
            }

            actualizarBalances() {
                const cantidadProduccion = parseFloat(document.getElementById('cantidad_produccion_final').value) || 0;
                const cantidadRetrabajo = parseFloat(document.getElementById('cantidad_retrabajo').value) || 0;
                const cantidadDestruccion = parseFloat(document.getElementById('cantidad_destruccion').value) || 0;

                const totalAsignado = cantidadProduccion + cantidadRetrabajo + cantidadDestruccion;
                const disponibleActualizado = this.cantidadDisponible - totalAsignado;

                document.getElementById('balanceAsignado').textContent = totalAsignado.toFixed(2) + ' lb.';
                document.getElementById('balanceDisponible').textContent = disponibleActualizado.toFixed(2) + ' lb.';

                const porcentajeAsignado = Math.min((totalAsignado / this.cantidadDisponible) * 100, 100);
                const porcentajeDisponible = Math.max(100 - porcentajeAsignado, 0);

                document.getElementById('progresoDisponible').style.width = porcentajeDisponible + '%';
                document.getElementById('progresoAsignado').style.width = porcentajeAsignado + '%';

                if (porcentajeDisponible < 20) {
                    document.getElementById('progresoDisponible').classList.remove('bg-yellow-500');
                    document.getElementById('progresoDisponible').classList.add('bg-red-500');
                } else {
                    document.getElementById('progresoDisponible').classList.remove('bg-red-500');
                    document.getElementById('progresoDisponible').classList.add('bg-yellow-500');
                }

                if (totalAsignado > this.cantidadDisponible) {
                    document.getElementById('progresoAsignado').classList.remove('bg-blue-500');
                    document.getElementById('progresoAsignado').classList.add('bg-red-500');
                    this.mostrarError('La cantidad total asignada excede el balance disponible.');
                    return false;
                } else {
                    document.getElementById('progresoAsignado').classList.remove('bg-red-500');
                    document.getElementById('progresoAsignado').classList.add('bg-blue-500');
                    document.getElementById('errorMessage').classList.add('hidden');
                    return true;
                }
            }

            asignarTodo(destino) {
                document.getElementById('cantidad_produccion_final').value = '0';
                document.getElementById('cantidad_retrabajo').value = '0';
                document.getElementById('cantidad_destruccion').value = '0';

                document.getElementById(`cantidad_${destino}`).value = this.cantidadDisponible.toFixed(2);
                this.actualizarBalances();
            }

            mostrarError(mensaje) {
                const errorMessage = document.getElementById('errorMessage');
                const errorText = document.getElementById('errorText');
                errorMessage.classList.remove('hidden');
                errorText.textContent = mensaje;
                toastr.error(mensaje);
            }

            guardarAsignaciones(event) {
                event.preventDefault();

                if (!this.actualizarBalances()) {
                    return false;
                }

                const cantidadProduccion = parseFloat(document.getElementById('cantidad_produccion_final').value) || 0;
                const cantidadRetrabajo = parseFloat(document.getElementById('cantidad_retrabajo').value) || 0;
                const cantidadDestruccion = parseFloat(document.getElementById('cantidad_destruccion').value) || 0;

                if (cantidadProduccion + cantidadRetrabajo + cantidadDestruccion <= 0) {
                    this.mostrarError('Debe asignar al menos una cantidad a un destino.');
                    return false;
                }

                if (cantidadProduccion < 0 || cantidadRetrabajo < 0 || cantidadDestruccion < 0) {
                    this.mostrarError('Las cantidades deben ser valores numéricos positivos.');
                    return false;
                }

                const formData = new FormData();
                formData.append('retencion_id', this.retencionActual.id);

                const destinos = [];
                const cantidades = [];
                const motivos = [];

                if (cantidadProduccion > 0) {
                    destinos.push('produccion_final');
                    cantidades.push(cantidadProduccion);
                    motivos.push(document.querySelector('[name="motivo_produccion_final"]').value || 'Liberado a producción');
                }

                if (cantidadRetrabajo > 0) {
                    destinos.push('retrabajo');
                    cantidades.push(cantidadRetrabajo);
                    motivos.push(document.querySelector('[name="motivo_retrabajo"]').value || 'Enviado a retrabajo');
                }

                if (cantidadDestruccion > 0) {
                    destinos.push('destruccion');
                    cantidades.push(cantidadDestruccion);
                    motivos.push(document.querySelector('[name="motivo_destruccion"]').value || 'Material para destruir');
                }

                destinos.forEach((destino, index) => {
                    formData.append('destinos[]', destino);
                    formData.append('cantidades[]', cantidades[index]);
                    formData.append('motivos[]', motivos[index]);
                });

                const submitButton = event.target.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
                submitButton.disabled = true;

                fetch('/timeControl/public/asignarDestinos', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return Promise.reject('redirect');
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }
                    return response.text().then(text => ({
                        status: response.ok ? 'success' : 'error',
                        message: text
                    }));
                })
                .then(data => {
                    if (data.status === 'error') {
                        throw new Error(data.message || 'Error en el servidor');
                    }
                    toastr.success(data.message || 'Destinos asignados correctamente');
                    setTimeout(() => {
                        window.location.href = '/timeControl/public/retenciones';
                    }, 1500);
                })
                .catch(error => {
                    if (error === 'redirect') return;
                    console.error('Error:', error);
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                    this.mostrarError(error.message || 'Error al procesar la solicitud. Inténtelo de nuevo.');
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = 0;
                setTimeout(() => {
                    el.style.opacity = 1;
                }, 100);
            });
            new GestionRetenciones();
        });