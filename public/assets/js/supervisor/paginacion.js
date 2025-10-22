// Sistema de Paginación para Validaciones
class PaginacionValidaciones {
    constructor(itemsPorPagina = 10) {
        this.itemsPorPagina = itemsPorPagina;
        this.paginaActual = 1;
        this.totalItems = 0;
        this.totalPaginas = 0;
        this.init();
    }

    init() {
        this.crearControlesPaginacion();
        this.actualizarPaginacion();
        this.configurarEventos();
    }

    crearControlesPaginacion() {
        // Crear controles de paginación si no existen
        const validacionesCard = document.querySelector('#validaciones .modern-card');
        if (!validacionesCard) return;

        // Verificar si ya existe el contenedor de paginación
        if (document.getElementById('paginacion-container')) return;

        const paginacionHTML = `
            <div id="paginacion-container" class="border-t" style="border-color: var(--border-light);">
                <div class="p-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <!-- Info de resultados -->
                    <div class="text-sm text-gray-600">
                        Mostrando <span id="items-inicio" class="font-semibold">0</span> a 
                        <span id="items-fin" class="font-semibold">0</span> de 
                        <span id="items-total" class="font-semibold">0</span> entregas
                    </div>

                    <!-- Selector de items por página -->
                    <div class="flex items-center gap-2">
                        <label for="items-por-pagina" class="text-sm text-gray-600">Mostrar:</label>
                        <select id="items-por-pagina" class="modern-input py-1 px-2 text-sm">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>

														
                        </select>
                    </div>

                    <!-- Controles de navegación -->
                    <div class="flex items-center gap-2">
                        <button id="btn-primera-pagina" class="btn-paginacion" title="Primera página">
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                        <button id="btn-pagina-anterior" class="btn-paginacion" title="Página anterior">
                            <i class="fas fa-angle-left"></i>
                        </button>
                        
                        <div id="numeros-pagina" class="flex gap-1">
                            <!-- Los números se generarán dinámicamente -->
                        </div>
                        
                        <button id="btn-pagina-siguiente" class="btn-paginacion" title="Página siguiente">
                            <i class="fas fa-angle-right"></i>
                        </button>
                        <button id="btn-ultima-pagina" class="btn-paginacion" title="Última página">
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        validacionesCard.insertAdjacentHTML('beforeend', paginacionHTML);

        // Agregar estilos CSS
        this.agregarEstilos();
    }

    agregarEstilos() {
        if (document.getElementById('estilos-paginacion')) return;

        const estilos = document.createElement('style');
        estilos.id = 'estilos-paginacion';
        estilos.textContent = `
            .btn-paginacion {
                padding: 0.5rem 0.75rem;
                border: 1px solid var(--border-light);
                background: white;
                color: var(--primary-dark);
                border-radius: 0.375rem;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 0.875rem;
                min-width: 2.5rem;
            }

            .btn-paginacion:hover:not(:disabled) {
                background: var(--primary-blue);
                color: white;
                border-color: var(--primary-blue);
            }

            .btn-paginacion:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                background: #f3f4f6;
            }

            .btn-numero-pagina {
                padding: 0.5rem 0.75rem;
                border: 1px solid var(--border-light);
                background: white;
                color: var(--primary-dark);
                border-radius: 0.375rem;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 0.875rem;
                min-width: 2.5rem;
                font-weight: 500;
            }

            .btn-numero-pagina:hover {
                background: rgba(91, 164, 207, 0.1);
                border-color: var(--primary-blue);
            }

            .btn-numero-pagina.activo {
                background: var(--primary-blue);
                color: white;
                border-color: var(--primary-blue);
            }

            .entrega-row, .mobile-table-row {
                display: none;
            }

            .entrega-row.visible, .mobile-table-row.visible {
                display: table-row;
            }

            @media (max-width: 768px) {
                .entrega-row.visible {
                    display: table-row;
                }
                .mobile-table-row.visible {
                    display: block;
                }
            }
        `;
        document.head.appendChild(estilos);
    }

    configurarEventos() {
        // Botones de navegación
        document.getElementById('btn-primera-pagina')?.addEventListener('click', () => {
            this.irAPagina(1);
        });

        document.getElementById('btn-pagina-anterior')?.addEventListener('click', () => {
            this.irAPagina(this.paginaActual - 1);
        });

        document.getElementById('btn-pagina-siguiente')?.addEventListener('click', () => {
            this.irAPagina(this.paginaActual + 1);
        });

        document.getElementById('btn-ultima-pagina')?.addEventListener('click', () => {
            this.irAPagina(this.totalPaginas);
        });

        // Cambio de items por página
        document.getElementById('items-por-pagina')?.addEventListener('change', (e) => {
            this.itemsPorPagina = parseInt(e.target.value);
            this.paginaActual = 1;
            this.actualizarPaginacion();
        });
    }

    actualizarPaginacion() {
        // Obtener todas las filas visibles después de aplicar filtros
        const filasDesktop = Array.from(document.querySelectorAll('#tablaEntregas .entrega-row'));
        const filasMobile = Array.from(document.querySelectorAll('.mobile-table-row'));
        
        // Filtrar solo las que no están ocultas por los filtros
        const filasVisiblesDesktop = filasDesktop.filter(fila => {
            return fila.style.display !== 'none' && !fila.classList.contains('filtered-out');
        });
        
        const filasVisiblesMobile = filasMobile.filter(fila => {
            return fila.style.display !== 'none' && !fila.classList.contains('filtered-out');
        });

        this.totalItems = filasVisiblesDesktop.length;
        this.totalPaginas = Math.ceil(this.totalItems / this.itemsPorPagina);

        // Asegurar que la página actual es válida
        if (this.paginaActual > this.totalPaginas && this.totalPaginas > 0) {
            this.paginaActual = this.totalPaginas;
        }

        // Calcular índices
        const inicio = (this.paginaActual - 1) * this.itemsPorPagina;
        const fin = Math.min(inicio + this.itemsPorPagina, this.totalItems);

        // Ocultar todas las filas primero
        filasDesktop.forEach(fila => fila.classList.remove('visible'));
        filasMobile.forEach(fila => fila.classList.remove('visible'));

        // Mostrar solo las filas de la página actual
        for (let i = inicio; i < fin; i++) {
            if (filasVisiblesDesktop[i]) {
                filasVisiblesDesktop[i].classList.add('visible');
            }
            if (filasVisiblesMobile[i]) {
                filasVisiblesMobile[i].classList.add('visible');
            }
        }

        // Actualizar información de resultados
        document.getElementById('items-inicio').textContent = this.totalItems > 0 ? inicio + 1 : 0;
        document.getElementById('items-fin').textContent = fin;
        document.getElementById('items-total').textContent = this.totalItems;

        // Actualizar botones de navegación
        this.actualizarBotonesNavegacion();
        this.generarNumerosPagina();
    }

    actualizarBotonesNavegacion() {
        const btnPrimera = document.getElementById('btn-primera-pagina');
        const btnAnterior = document.getElementById('btn-pagina-anterior');
        const btnSiguiente = document.getElementById('btn-pagina-siguiente');
        const btnUltima = document.getElementById('btn-ultima-pagina');

        if (btnPrimera) btnPrimera.disabled = this.paginaActual === 1;
        if (btnAnterior) btnAnterior.disabled = this.paginaActual === 1;
        if (btnSiguiente) btnSiguiente.disabled = this.paginaActual === this.totalPaginas || this.totalPaginas === 0;
        if (btnUltima) btnUltima.disabled = this.paginaActual === this.totalPaginas || this.totalPaginas === 0;
    }

    generarNumerosPagina() {
        const contenedor = document.getElementById('numeros-pagina');
        if (!contenedor) return;

        contenedor.innerHTML = '';

        if (this.totalPaginas === 0) return;

        // Lógica para mostrar números de página con puntos suspensivos
        let paginas = [];
        
        if (this.totalPaginas <= 7) {
            // Mostrar todas las páginas si son 7 o menos
            paginas = Array.from({length: this.totalPaginas}, (_, i) => i + 1);
        } else {
            // Mostrar páginas con puntos suspensivos
            if (this.paginaActual <= 4) {
                paginas = [1, 2, 3, 4, 5, '...', this.totalPaginas];
            } else if (this.paginaActual >= this.totalPaginas - 3) {
                paginas = [1, '...', this.totalPaginas - 4, this.totalPaginas - 3, this.totalPaginas - 2, this.totalPaginas - 1, this.totalPaginas];
            } else {
                paginas = [1, '...', this.paginaActual - 1, this.paginaActual, this.paginaActual + 1, '...', this.totalPaginas];
            }
        }

        paginas.forEach(pagina => {
            if (pagina === '...') {
                const span = document.createElement('span');
                span.className = 'btn-paginacion';
                span.style.cursor = 'default';
                span.textContent = '...';
                contenedor.appendChild(span);
            } else {
                const btn = document.createElement('button');
                btn.className = 'btn-numero-pagina';
                btn.textContent = pagina;
                
                if (pagina === this.paginaActual) {
                    btn.classList.add('activo');
                }
                
                btn.addEventListener('click', () => {
                    this.irAPagina(pagina);
                });
                
                contenedor.appendChild(btn);
            }
        });
    }

    irAPagina(numeroPagina) {
        if (numeroPagina < 1 || numeroPagina > this.totalPaginas) return;
        
        this.paginaActual = numeroPagina;
        this.actualizarPaginacion();

        // Scroll suave hacia arriba de la tabla
        const validacionesSection = document.getElementById('validaciones');
        if (validacionesSection) {
            validacionesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Método para actualizar después de aplicar filtros
    actualizarDespuesDeFiltros() {
        this.paginaActual = 1;
        this.actualizarPaginacion();
    }
}

// Inicializar la paginación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Crear instancia de paginación
    window.paginacionValidaciones = new PaginacionValidaciones(10);
    
    // Integrar con el sistema de filtros existente
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', function() {
            // Limpiar filtros
            document.getElementById('filtroFecha').value = '';
            document.getElementById('filtroItem').value = '';
            document.getElementById('filtroJtWo').value = '';
            document.getElementById('filtroPO').value = '';
            document.getElementById('filtroCliente').value = '';
            
            // Mostrar todas las filas
            document.querySelectorAll('.entrega-row, .mobile-table-row').forEach(fila => {
                fila.style.display = '';
                fila.classList.remove('filtered-out');
            });
            
            // Actualizar paginación
            if (window.paginacionValidaciones) {
                window.paginacionValidaciones.actualizarDespuesDeFiltros();
            }
        });
    }
    
    // Integrar filtros con paginación
    const filtros = ['filtroFecha', 'filtroItem', 'filtroJtWo', 'filtroPO', 'filtroCliente'];
    filtros.forEach(filtroId => {
        const input = document.getElementById(filtroId);
        if (input) {
            input.addEventListener('input', function() {
                aplicarFiltros();
                if (window.paginacionValidaciones) {
                    window.paginacionValidaciones.actualizarDespuesDeFiltros();
                }
            });
        }
    });
});

function aplicarFiltros() {
    const fecha = document.getElementById('filtroFecha')?.value.toLowerCase() || '';
    const item = document.getElementById('filtroItem')?.value.toLowerCase() || '';
    const jtwo = document.getElementById('filtroJtWo')?.value.toLowerCase() || '';
    const po = document.getElementById('filtroPO')?.value.toLowerCase() || '';
    const cliente = document.getElementById('filtroCliente')?.value.toLowerCase() || '';
    
    // Filtrar filas desktop
    document.querySelectorAll('.entrega-row').forEach(fila => {
        const filaFecha = fila.dataset.fecha || '';
        const filaItem = (fila.dataset.item || '').toLowerCase();
        const filaJtwo = (fila.dataset.jtwo || '').toLowerCase();
        const filaPo = (fila.dataset.po || '').toLowerCase();
        const filaCliente = (fila.dataset.cliente || '').toLowerCase();
        
        const cumpleFiltros = 
            filaFecha.includes(fecha) &&
            filaItem.includes(item) &&
            filaJtwo.includes(jtwo) &&
            filaPo.includes(po) &&
            filaCliente.includes(cliente);
        
        if (cumpleFiltros) {
            fila.style.display = '';
            fila.classList.remove('filtered-out');
        } else {
            fila.style.display = 'none';
            fila.classList.add('filtered-out');
        }
    });
    
    // Filtrar filas mobile
    document.querySelectorAll('.mobile-table-row').forEach(fila => {
        const filaFecha = fila.dataset.fecha || '';
        const filaItem = (fila.dataset.item || '').toLowerCase();
        const filaJtwo = (fila.dataset.jtwo || '').toLowerCase();
        const filaPo = (fila.dataset.po || '').toLowerCase();
        const filaCliente = (fila.dataset.cliente || '').toLowerCase();
        
        const cumpleFiltros = 
            filaFecha.includes(fecha) &&
            filaItem.includes(item) &&
            filaJtwo.includes(jtwo) &&
            filaPo.includes(po) &&
            filaCliente.includes(cliente);
        
        if (cumpleFiltros) {
            fila.style.display = '';
            fila.classList.remove('filtered-out');
        } else {
            fila.style.display = 'none';
            fila.classList.add('filtered-out');
        }
    });
}