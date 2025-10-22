(function() {
    'use strict';

    // Cache DOM elements
    const DOM_CACHE = {
        tabLinks: null,
        tabContents: null,
        timeElements: null,
        filterInputs: null,
        entregaRows: null,
        mobileRows: null
    };

    function initDOMCache() {
        DOM_CACHE.tabLinks = document.querySelectorAll('.tab-link');
        DOM_CACHE.tabContents = document.querySelectorAll('.tab-content');
        DOM_CACHE.filterInputs = {
            fecha: document.getElementById('filtroFecha'),
            item: document.getElementById('filtroItem'),
            jtwo: document.getElementById('filtroJtWo'),
            po: document.getElementById('filtroPO'),
            cliente: document.getElementById('filtroCliente')
        };
    }

    // Función para normalizar fechas
    function normalizarFecha(fecha) {
        if (!fecha) return '';
        
        // Si ya está en formato YYYY-MM-DD, retornarla
        if (/^\d{4}-\d{2}-\d{2}$/.test(fecha)) {
            return fecha;
        }
        
        // Si está en formato DD/MM/YYYY, convertirla a YYYY-MM-DD
        if (/^\d{2}\/\d{2}\/\d{4}$/.test(fecha)) {
            const partes = fecha.split('/');
            return `${partes[2]}-${partes[1]}-${partes[0]}`;
        }
        
        // Si es un objeto Date
        if (fecha instanceof Date) {
            const year = fecha.getFullYear();
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const day = String(fecha.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        return fecha;
    }

    const TimeUpdateSystem = {
        rows: [],
        animationFrameId: null,
        isRunning: false,
        lastUpdate: 0,
        UPDATE_INTERVAL: 1000,

        init() {
            this.cacheTimeRows();
            this.start();
        },

        cacheTimeRows() {
            const tableRows = document.querySelectorAll('tbody tr[data-start-time]');
            this.rows = Array.from(tableRows).map(row => {
                const timeElement = row.querySelector('.tiempo');
                const startTime = parseInt(row.dataset.startTime, 10) * 1000;
                
                return {
                    element: timeElement,
                    startTime: startTime,
                    row: row
                };
            }).filter(item => item.element && !isNaN(item.startTime));
        },

        updateTime(timestamp) {
            if (timestamp - this.lastUpdate < this.UPDATE_INTERVAL) {
                if (this.isRunning) {
                    this.animationFrameId = requestAnimationFrame((ts) => this.updateTime(ts));
                }
                return;
            }

            this.lastUpdate = timestamp;
            const now = Date.now();

            for (let i = 0; i < this.rows.length; i++) {
                const item = this.rows[i];
                const diff = Math.floor((now - item.startTime) / 1000);

                const hours = Math.floor(diff / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;

                const timeString = 
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0');

                if (item.element.textContent !== timeString) {
                    item.element.textContent = timeString;
                }

                const newClass = hours >= 8 ? 'text-red-600' : 
                               (hours >= 6 ? 'text-yellow-600' : 'text-green-600');
                
                if (!item.element.classList.contains(newClass)) {
                    item.element.className = 'tiempo font-bold ' + newClass;
                }
            }

            if (this.isRunning) {
                this.animationFrameId = requestAnimationFrame((ts) => this.updateTime(ts));
            }
        },

        start() {
            if (this.isRunning) return;
            this.isRunning = true;
            this.animationFrameId = requestAnimationFrame((ts) => this.updateTime(ts));
        },

        stop() {
            this.isRunning = false;
            if (this.animationFrameId) {
                cancelAnimationFrame(this.animationFrameId);
                this.animationFrameId = null;
            }
        },

        refresh() {
            this.stop();
            this.cacheTimeRows();
            this.start();
        }
    };

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const FilterSystem = {
        cachedRows: null,

        init() {
            this.cacheRows();
            this.attachListeners();
        },

        cacheRows() {
            const desktopRows = document.querySelectorAll('.entrega-row');
            const mobileRows = document.querySelectorAll('.mobile-table-row');
            
            this.cachedRows = [...desktopRows, ...mobileRows].map(row => {
                const fechaOriginal = row.dataset.fecha || '';
                
                return {
                    element: row,
                    data: {
                        fecha: normalizarFecha(fechaOriginal),
                        fechaOriginal: fechaOriginal, // Guardar original para debug
                        item: (row.dataset.item || '').toLowerCase(),
                        jtwo: (row.dataset.jtwo || '').toLowerCase(),
                        po: (row.dataset.po || '').toLowerCase(),
                        cliente: (row.dataset.cliente || '').toLowerCase()
                    }
                };
            });

            // Debug: mostrar fechas cacheadas
            console.log('Filas cacheadas:', this.cachedRows.length);
            if (this.cachedRows.length > 0) {
                console.log('Ejemplo de fecha normalizada:', {
                    original: this.cachedRows[0].data.fechaOriginal,
                    normalizada: this.cachedRows[0].data.fecha
                });
            }
        },

        filter() {
            const fechaFiltro = DOM_CACHE.filterInputs.fecha?.value || '';
            const fechaNormalizada = normalizarFecha(fechaFiltro);
            
            const filters = {
                fecha: fechaNormalizada,
                item: (DOM_CACHE.filterInputs.item?.value || '').toLowerCase(),
                jtwo: (DOM_CACHE.filterInputs.jtwo?.value || '').toLowerCase(),
                po: (DOM_CACHE.filterInputs.po?.value || '').toLowerCase(),
                cliente: (DOM_CACHE.filterInputs.cliente?.value || '').toLowerCase()
            };

            // Debug
            if (fechaNormalizada) {
                console.log('Filtrando por fecha:', fechaNormalizada);
            }

            let contadorVisible = 0;

            for (let i = 0; i < this.cachedRows.length; i++) {
                const row = this.cachedRows[i];
                const data = row.data;
                
                // Comparación de fecha mejorada
                const matchFecha = !filters.fecha || data.fecha === filters.fecha;
                
                const match = 
                    matchFecha &&
                    (!filters.item || data.item.includes(filters.item)) &&
                    (!filters.jtwo || data.jtwo.includes(filters.jtwo)) &&
                    (!filters.po || data.po.includes(filters.po)) &&
                    (!filters.cliente || data.cliente.includes(filters.cliente));

                row.element.style.display = match ? '' : 'none';
                
                if (match) contadorVisible++;
            }

            // Actualizar grupos de máquinas si existen
            this.updateMachineGroups();

            // Actualizar contador si existe
            this.updateCounter(contadorVisible);

            console.log('Filas visibles:', contadorVisible);
        },

        updateMachineGroups() {
            const grupos = document.querySelectorAll('.maquina-group');
            grupos.forEach(grupo => {
                const filasVisibles = grupo.querySelectorAll('.entrega-row:not([style*="display: none"])').length;
                grupo.style.display = filasVisibles > 0 ? '' : 'none';
            });
        },

        updateCounter(count) {
            const counter = document.getElementById('pending-counter');
            if (counter) {
                counter.textContent = count;
            }
        },

        clear() {
            Object.values(DOM_CACHE.filterInputs).forEach(input => {
                if (input) input.value = '';
            });
            
            // Mostrar todas las filas
            this.cachedRows.forEach(row => {
                row.element.style.display = '';
            });

            // Mostrar todos los grupos
            const grupos = document.querySelectorAll('.maquina-group');
            grupos.forEach(grupo => {
                grupo.style.display = '';
            });

            // Actualizar contador
            this.updateCounter(this.cachedRows.length);
            
            console.log('Filtros limpiados');
        },

        attachListeners() {
            const debouncedFilter = debounce(() => this.filter(), 300);
            
            Object.values(DOM_CACHE.filterInputs).forEach(input => {
                if (input) {
                    input.addEventListener('input', debouncedFilter);
                    input.addEventListener('change', debouncedFilter);
                }
            });

            const clearBtn = document.getElementById('btnLimpiarFiltros');
            if (clearBtn) {
                clearBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.clear();
                });
            }
        },

        // Método para refrescar el cache después de cambios dinámicos
        refresh() {
            console.log('Refrescando cache de filtros...');
            this.cacheRows();
            this.filter();
        }
    };

    const TabSystem = {
        init() {
            DOM_CACHE.tabLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.switchTab(link);
                });
            });

            // Activar tab por defecto
            const defaultTab = document.querySelector('#tab-operaciones');
            if (defaultTab) {
                this.switchTab(defaultTab);
            }
        },

        switchTab(activeLink) {
            DOM_CACHE.tabLinks.forEach(link => link.classList.remove('active'));
            DOM_CACHE.tabContents.forEach(content => content.classList.add('hidden'));

            activeLink.classList.add('active');
            const targetId = activeLink.getAttribute('href');
            const targetContent = document.querySelector(targetId);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }

            if (targetId === '#operaciones-abiertas') {
                TimeUpdateSystem.refresh();
            }

            // Refrescar filtros cuando cambiamos de tab
            FilterSystem.refresh();
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Inicializando sistemas...');
        
        initDOMCache();
        TabSystem.init();
        TimeUpdateSystem.init();
        FilterSystem.init();

        document.addEventListener('newDelivery', (e) => {
            toastr.info('Nueva entrega detectada', 'Hay nuevos registros disponibles', {
                timeOut: 5000,
                extendedTimeOut: 1000,
                closeButton: true,
                tapToDismiss: false,
                onclick: () => window.location.reload()
            });
        });

        window.addEventListener('beforeunload', () => {
            TimeUpdateSystem.stop();
        });

        console.log('Sistemas inicializados correctamente');
    });

    window.confirmLogout = function() {
        if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
            document.cookie = 'jwt=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            window.location.href = "/timeControl/public/logout";
        }
    };

    // Exponer FilterSystem globalmente para debugging
    window.FilterSystem = FilterSystem;

})();