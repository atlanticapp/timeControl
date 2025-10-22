// Sistema de Paginación Eficiente para Validación QA
// Agregar este código al final de tu archivo validacion.js o crear un nuevo archivo pagination.js

class PaginationSystem {
    constructor(options = {}) {
        this.itemsPerPage = options.itemsPerPage || 10;
        this.currentPage = 1;
        this.totalPages = 1;
        this.allRows = [];
        this.filteredRows = [];
        this.paginationContainer = null;
        this.init();
    }

    init() {
        // Crear contenedor de paginación
        this.createPaginationUI();
        
        // Obtener todas las filas
        this.collectAllRows();
        
        // Aplicar paginación inicial
        this.applyPagination();
    }

    collectAllRows() {
        // Recolectar todas las filas de entregas
        this.allRows = Array.from(document.querySelectorAll('.entrega-row'));
        this.filteredRows = [...this.allRows];
        this.updateTotalPages();
    }

    createPaginationUI() {
        // Buscar el contenedor de la tabla
        const tableContainer = document.querySelector('.modern-card.overflow-hidden');
        if (!tableContainer) return;

        // Crear contenedor de paginación
        const paginationHTML = `
            <div class="pagination-container bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Info de registros -->
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-info-circle" style="color: var(--primary-blue);"></i>
                        <span>Mostrando</span>
                        <span class="font-semibold" id="startRecord">0</span>
                        <span>-</span>
                        <span class="font-semibold" id="endRecord">0</span>
                        <span>de</span>
                        <span class="font-semibold" id="totalRecords">0</span>
                        <span>entregas</span>
                    </div>

                    <!-- Controles de paginación -->
                    <div class="flex items-center gap-3">
                        <!-- Items por página -->
                        <div class="flex items-center gap-2 text-sm">
                            <label for="itemsPerPage" class="text-gray-600">Mostrar:</label>
                            <select id="itemsPerPage" class="modern-select py-1 px-2 text-sm w-20">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <!-- Botones de navegación -->
                        <div class="flex items-center gap-1">
                            <button id="firstPage" class="pagination-btn" title="Primera página">
                                <i class="fas fa-angle-double-left"></i>
                            </button>
                            <button id="prevPage" class="pagination-btn" title="Página anterior">
                                <i class="fas fa-angle-left"></i>
                            </button>
                            
                            <!-- Números de página -->
                            <div id="pageNumbers" class="flex items-center gap-1 mx-2"></div>
                            
                            <button id="nextPage" class="pagination-btn" title="Página siguiente">
                                <i class="fas fa-angle-right"></i>
                            </button>
                            <button id="lastPage" class="pagination-btn" title="Última página">
                                <i class="fas fa-angle-double-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        tableContainer.insertAdjacentHTML('beforeend', paginationHTML);
        this.paginationContainer = tableContainer.querySelector('.pagination-container');

        // Agregar estilos
        this.addPaginationStyles();

        // Agregar event listeners
        this.attachEventListeners();
    }

    addPaginationStyles() {
        if (document.getElementById('pagination-styles')) return;

        const styles = `
            <style id="pagination-styles">
                .pagination-btn {
                    padding: 0.5rem 0.75rem;
                    border: 1px solid var(--border-light);
                    background: white;
                    color: var(--primary-blue);
                    border-radius: 6px;
                    transition: all 0.2s ease;
                    font-size: 0.875rem;
                    min-width: 38px;
                    height: 38px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .pagination-btn:hover:not(:disabled) {
                    background: var(--primary-blue);
                    color: white;
                    transform: translateY(-1px);
                    box-shadow: 0 2px 8px rgba(91, 164, 207, 0.3);
                }

                .pagination-btn:disabled {
                    opacity: 0.4;
                    cursor: not-allowed;
                }

                .pagination-btn.active {
                    background: var(--primary-blue);
                    color: white;
                    font-weight: 600;
                    border-color: var(--primary-blue);
                }

                .pagination-container {
                    border-radius: 0 0 16px 16px;
                }

                @media (max-width: 640px) {
                    .pagination-btn {
                        padding: 0.4rem 0.6rem;
                        min-width: 32px;
                        height: 32px;
                        font-size: 0.75rem;
                    }
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', styles);
    }

    attachEventListeners() {
        // Items por página
        document.getElementById('itemsPerPage')?.addEventListener('change', (e) => {
            this.itemsPerPage = parseInt(e.target.value);
            this.currentPage = 1;
            this.updateTotalPages();
            this.applyPagination();
        });

        // Navegación
        document.getElementById('firstPage')?.addEventListener('click', () => this.goToPage(1));
        document.getElementById('prevPage')?.addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage')?.addEventListener('click', () => this.goToPage(this.currentPage + 1));
        document.getElementById('lastPage')?.addEventListener('click', () => this.goToPage(this.totalPages));
    }

    updateTotalPages() {
        this.totalPages = Math.ceil(this.filteredRows.length / this.itemsPerPage) || 1;
    }

    goToPage(page) {
        if (page < 1 || page > this.totalPages) return;
        this.currentPage = page;
        this.applyPagination();
    }

    applyPagination() {
        // Ocultar todas las filas primero
        this.allRows.forEach(row => row.style.display = 'none');

        // Calcular índices
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;

        // Mostrar solo las filas de la página actual
        const rowsToShow = this.filteredRows.slice(startIndex, endIndex);
        rowsToShow.forEach(row => row.style.display = '');

        // Manejar grupos de máquinas vacíos
        this.updateMachineGroups();

        // Actualizar UI de paginación
        this.updatePaginationUI();
        
        // Actualizar contador de pendientes
        this.updatePendingCounter();
    }

    updateMachineGroups() {
        document.querySelectorAll('.maquina-group').forEach(group => {
            const visibleRows = group.querySelectorAll('.entrega-row:not([style*="display: none"])');
            group.style.display = visibleRows.length > 0 ? '' : 'none';
        });
    }

    updatePaginationUI() {
        const startRecord = this.filteredRows.length === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
        const endRecord = Math.min(this.currentPage * this.itemsPerPage, this.filteredRows.length);
        
        // Actualizar contadores
        document.getElementById('startRecord').textContent = startRecord;
        document.getElementById('endRecord').textContent = endRecord;
        document.getElementById('totalRecords').textContent = this.filteredRows.length;

        // Actualizar botones de navegación
        document.getElementById('firstPage').disabled = this.currentPage === 1;
        document.getElementById('prevPage').disabled = this.currentPage === 1;
        document.getElementById('nextPage').disabled = this.currentPage === this.totalPages;
        document.getElementById('lastPage').disabled = this.currentPage === this.totalPages;

        // Actualizar números de página
        this.renderPageNumbers();
    }

    renderPageNumbers() {
        const container = document.getElementById('pageNumbers');
        if (!container) return;

        container.innerHTML = '';

        // Calcular rango de páginas a mostrar
        let startPage = Math.max(1, this.currentPage - 2);
        let endPage = Math.min(this.totalPages, this.currentPage + 2);

        // Ajustar si estamos cerca del inicio o final
        if (this.currentPage <= 3) {
            endPage = Math.min(5, this.totalPages);
        }
        if (this.currentPage >= this.totalPages - 2) {
            startPage = Math.max(1, this.totalPages - 4);
        }

        // Primera página con puntos suspensivos
        if (startPage > 1) {
            this.addPageButton(container, 1);
            if (startPage > 2) {
                container.insertAdjacentHTML('beforeend', '<span class="px-2 text-gray-400">...</span>');
            }
        }

        // Páginas del rango
        for (let i = startPage; i <= endPage; i++) {
            this.addPageButton(container, i);
        }

        // Última página con puntos suspensivos
        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                container.insertAdjacentHTML('beforeend', '<span class="px-2 text-gray-400">...</span>');
            }
            this.addPageButton(container, this.totalPages);
        }
    }

    addPageButton(container, pageNum) {
        const button = document.createElement('button');
        button.className = `pagination-btn ${pageNum === this.currentPage ? 'active' : ''}`;
        button.textContent = pageNum;
        button.addEventListener('click', () => this.goToPage(pageNum));
        container.appendChild(button);
    }

    updatePendingCounter() {
        const counter = document.getElementById('pending-counter');
        if (counter) {
            counter.textContent = this.filteredRows.length;
        }
    }

    // Método para actualizar después de filtros
    applyFilters(filterFunction) {
        this.filteredRows = this.allRows.filter(filterFunction);
        this.currentPage = 1;
        this.updateTotalPages();
        this.applyPagination();
    }

    // Método para refrescar después de cambios
    refresh() {
        this.collectAllRows();
        this.applyPagination();
    }
}

// Inicializar paginación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que todo esté cargado
    setTimeout(() => {
        window.paginationSystem = new PaginationSystem({
            itemsPerPage: 10
        });

        // Integrar con el sistema de filtros existente
        const originalFiltrar = window.filtrarDestinos || filtrarEntregas;
        if (originalFiltrar) {
            window.filtrarEntregas = function() {
                originalFiltrar();
                
                // Actualizar paginación después de filtrar
                if (window.paginationSystem) {
                    const visibleRows = Array.from(document.querySelectorAll('.entrega-row'))
                        .filter(row => row.style.display !== 'none');
                    window.paginationSystem.filteredRows = visibleRows;
                    window.paginationSystem.currentPage = 1;
                    window.paginationSystem.updateTotalPages();
                    window.paginationSystem.applyPagination();
                }
            };
        }
    }, 500);
});

// Función auxiliar para integrar con filtros
function filtrarEntregas() {
    const filtroFecha = document.getElementById('filtroFecha')?.value || '';
    const filtroItem = document.getElementById('filtroItem')?.value.toLowerCase() || '';
    const filtroJtWo = document.getElementById('filtroJtWo')?.value.toLowerCase() || '';
    const filtroPO = document.getElementById('filtroPO')?.value.toLowerCase() || '';
    const filtroCliente = document.getElementById('filtroCliente')?.value.toLowerCase() || '';

    if (window.paginationSystem) {
        window.paginationSystem.applyFilters((row) => {
            const rowFecha = row.getAttribute('data-fecha') || '';
            const rowItem = (row.getAttribute('data-item') || '').toLowerCase();
            const rowJtWo = (row.getAttribute('data-jtwo') || '').toLowerCase();
            const rowPO = (row.getAttribute('data-po') || '').toLowerCase();
            const rowCliente = (row.getAttribute('data-cliente') || '').toLowerCase();

            return (!filtroFecha || rowFecha === filtroFecha) &&
                   (!filtroItem || rowItem.includes(filtroItem)) &&
                   (!filtroJtWo || rowJtWo.includes(filtroJtWo)) &&
                   (!filtroPO || rowPO.includes(filtroPO)) &&
                   (!filtroCliente || rowCliente.includes(filtroCliente));
        });
    }
}

// Integrar con los filtros existentes
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        document.getElementById('filtroFecha')?.addEventListener('change', filtrarEntregas);
        document.getElementById('filtroItem')?.addEventListener('input', filtrarEntregas);
        document.getElementById('filtroJtWo')?.addEventListener('input', filtrarEntregas);
        document.getElementById('filtroPO')?.addEventListener('input', filtrarEntregas);
        document.getElementById('filtroCliente')?.addEventListener('input', filtrarEntregas);

        document.getElementById('btnLimpiarFiltros')?.addEventListener('click', function() {
            document.getElementById('filtroFecha').value = '';
            document.getElementById('filtroItem').value = '';
            document.getElementById('filtroJtWo').value = '';
            document.getElementById('filtroPO').value = '';
            document.getElementById('filtroCliente').value = '';
            filtrarEntregas();
        });
    }, 600);
});